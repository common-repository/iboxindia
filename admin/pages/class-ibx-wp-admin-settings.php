<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Settings_Page' ) ) :

	/**
	 * Iboxindia_WP_Settings_Page
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Settings_Page {

    /**
     * Instance of Iboxindia_WP_Settings_Page
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Settings_Page
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Settings_Page.
     *
     * @since 2.3.7
     *
     * @return object Class object.
     */
    public static function get_instance() {
      if ( ! isset( self::$instance ) ) {
        self::$instance = new self();
      }

      return self::$instance;
    }

    /**
     * Constructor.
     *
     * @since 2.3.7
     */
    private function __construct() {
      add_action( 'ibx_wp_admin_menu', array( $this, 'add_menu' ) );
      add_action( "wp_ajax_iboxindia_login_user", array( $this, 'loginUser' ) );
      add_action( "wp_ajax_iboxindia_clear_login", array( $this, 'resetUser' ) );
      add_action( "wp_ajax_iboxindia_update_settings", array( $this, 'updateSettings' ) );
      
    }
    public function add_menu() {
      $page=add_submenu_page( IBX_WP_PLUGIN_NAME, 'Iboxindia Settings', 'Settings', 'administrator', IBX_WP_PLUGIN_NAME.'-settings', [ $this, 'show' ], 10 );
      $ibx_admin = Iboxindia_WP_Admin::get_instance();
      add_action( "admin_print_styles-{$page}", array ($ibx_admin, 'enqueue_admin_style' ) );
    }

    private function mask ( $str, $start = 0, $length = null ) {
      $mask = preg_replace ( "/\S/", "*", $str );
      if( is_null ( $length )) {
          $mask = substr ( $mask, $start );
          $str = substr_replace ( $str, $mask, $start );
      }else{
          $mask = substr ( $mask, $start, $length );
          $str = substr_replace ( $str, $mask, $start, $length );
      }
      return $str;
    }
    public function show() {
      // var_dump( $settings );

      $hash = Iboxindia_WP_Settings::get( "hash" );
      $timeout = Iboxindia_WP_Settings::get( "timeout" );
      $debug = Iboxindia_WP_Settings::get( "debug" );  ?>
      <!-- pending tabbed design -->
      <div class="wrap">
        <div id="icon-themes" class="icon32"></div>  
        <h2>Iboxindia WP Settings</h2>
        <div class="iboxindia-settings login-logout-section">
          <?php if ( empty( $hash ) ) { ?>
            <form class="ajax-form" action="<?php echo admin_url( 'admin-ajax.php?action=iboxindia_login_user' ) ?>" method="POST" data-success-callback="reloadPage">
              <input type="text" name="username" placeholder="Username" />
              <input type="password" name="password" placeholder="Password" />
              <button type="submit" class="btn waves-effect waves-light">Save
                <i class="material-icons right">send</i>
              </button>
            </form> 
          <?php } else { ?>
            <form class="ajax-form" action="<?php echo admin_url( 'admin-ajax.php?action=iboxindia_clear_login' ) ?>" method="POST" data-success-callback="reloadPage">
              <div class="">Logged in: <span><?php 
                $hash = strtoupper( $hash );
                $hash = substr( $hash, strlen( $hash ) - 16 );
                echo $this->mask( $hash, strlen( $hash ) - 16, strlen( $hash ) - 4 );
              ?></span>
              <button type="submit" class="btn waves-effect waves-light">Logout
                <i class="material-icons right">logout</i>
              </button>
              </div>
            </form>
          <?php } ?>
        </div>
        <div class="iboxindia-settings">
          <form class="ajax-form" action="<?php echo admin_url( 'admin-ajax.php?action=iboxindia_update_settings' ) ?>" method="POST" data-success-callback="reloadPage">
            <div class="input-field col s12">
              <input type="number" name="timeout" id="timeout" value="<?php echo $timeout; ?>"/>
              <label for="timeout">Timeout</label>
            </div>
            <div class="input-field col s12">
              <select name="debug">
                <option value="1" <?php echo $debug ? 'selected' : ''; ?>> True </option>
                <option value="0" <?php echo $debug ? '' : 'selected'; ?>> False </option>
              </select>
              <label>Debug</label>
            </div>
            <button type="submit" class="btn waves-effect waves-light">Save
              <i class="material-icons right">send</i>
            </button>
          </form>
        </div>
        <?php do_action( 'add_iboxindia_settings' ); ?>
      </div>
      <?php
    }
    public function loginUser() {
      $json = file_get_contents('php://input');
      parse_str ( $json, $data );
      // $data = json_decode($json, true);

      $username = sanitize_text_field( $data['username'] );
      $password = sanitize_text_field( $data['password'] );
      $resp = Iboxindia_WP_Rest_API::loginUser( $username, $password );
      // var_dump($username);
      // var_dump($password);
      // var_dump($data);
      // var_dump($data);
      // var_dump($resp);
      // var_dump($resp);
      // var_dump($resp['error']);
      // echo json_decode(stripslashes($resp), true);
      // wp_send_json ( $resp );
      if( isset( $resp['data'] ) ) {
        Iboxindia_WP_Settings::set( "hash", $resp['data']['token'] );
        wp_send_json ( ['data' => [ "logged_in" => true ] ] );
      } else {
        wp_send_json ( $resp );
      }
    }
    public function resetUser() {
      Iboxindia_WP_Settings::set( "hash", false );
    }
    public function updateSettings() {
      $timeout = intval( sanitize_text_field( isset ( $_POST['timeout'] ) ? $_POST['timeout'] : 60 ) );
      $debug = boolval( sanitize_text_field( isset ( $_POST['debug'] ) ? $_POST['debug'] : 0 ) );
      Iboxindia_WP_Settings::set( 'timeout', $timeout );
      Iboxindia_WP_Settings::set( 'debug', $debug );
    }
  }
  Iboxindia_WP_Settings_Page::get_instance();
endif;
?>