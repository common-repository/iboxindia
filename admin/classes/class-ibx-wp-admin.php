<?php
/**
 * Admin Notices
 *
 * @since 2.3.7
 * @package Iboxindia
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'Iboxindia_WP_Admin' ) ) :

  /**
   * Admin
   */
  class Iboxindia_WP_Admin {

    /**
     * Instance of Iboxindia_WP_Admin
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Admin
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Admin.
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
      add_action( 'admin_init', array( $this, 'init' ) );
      add_action( 'admin_menu', array( $this, 'add_menu' ) );
      // add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_style' ) ); 
      add_action( 'admin_notices', array( $this, 'admin_notices' ) );

    }

    /**
     * Admin Notices
     *
     * @since 2.3.7
     * @return void
     */
    public function admin_notices() {

      $image_path = esc_url( IBX_WP_PLUGIN_URL . '/inc/assets/images/logo.svg' );

      Iboxindia_WP_Notices::add_notice(
        array(
          'id'      => 'ibx-wp-start-notice',
          'type'    => 'info',
          'class'   => 'ibx-wp-star',
          // 'show_if' => ( false === ::get_instance()->is_white_labeled() ),
          /* translators: %1$s white label plugin name and %2$s deactivation link */
          'message' => sprintf(
            '<div class="ibx-notice-container">
              <div class="notice-image" style="display: flex;">
                <img src="%1$s" class="hide custom-logo" alt="Iboxindia Wordpress" itemprop="logo" style="max-width: 90px;">
              </div>
              <div class="notice-content">
                <div class="notice-heading">
                  %2$s
                </div>
                %3$s<br />
                <div class="ibx-review-notice-container">
                  <a href="%4$s" class="ibx-notice-close ibox-review-notice button-primary" target="_blank">
                  %5$s
                  </a>
                  
                  <span class="dashicons dashicons-calendar"></span>
                  <a href="%6$s" class="ibox-notice-close ibox-review-notice">
                  %7$s
                  </a>
                  
                  <span class="dashicons dashicons-smiley"></span>
                  <a href="%8$s" class="ibx-notice-close ibox-review-notice">
                  %9$s
                  </a>
                </div>
              </div>
            </div>',
            $image_path,
            __( 'Hello! We noticed that the Iboxindia Wordpress Setup plugin is active. If you have installed this plugin by mistake, we request you to kindly deactivate it to remove this notice.', 'iboxindia' ),
            __( 'If you need any assistance from us to setup your website and get online within minutes, just click the below button to get in touch. &mdash; Thanks a ton!', 'iboxindia' ),
            'https://iboxindia.com/contact?from_wordpress=true&site=' . get_site_url(),
            __( 'Ok, Please help me.', 'iboxindia' ),
            '#',
            __( 'Nope, maybe later', 'iboxindia' ),
            'https://iboxindia.com/support?from_wordpress=true&site=' . get_site_url(),
            __( 'Support our free efforts', 'iboxindia' )
          ),
        )
      );
    }

    public function init() {
      if( empty( Iboxindia_WP_Settings::get( "hash" ) ) ) {
        Iboxindia_WP_Settings::set( "hash", false );
      }
      if( empty( Iboxindia_WP_Settings::get( "timeout" ) ) ) {
        Iboxindia_WP_Settings::set( "timeout", 60 );
      }
      if( empty( Iboxindia_WP_Settings::get( "logged_in" ) ) ) {
          Iboxindia_WP_Settings::set( "logged_in", false );
      }
      if( empty( Iboxindia_WP_Settings::get( "debug" ) ) ) {
        Iboxindia_WP_Settings::set( "debug", false );
      }
      if( empty( Iboxindia_WP_Settings::get( "upload_path" ) ) ) {
        $dirname = wp_upload_dir()['basedir'] . '/iboxindia-assets';
        if ( ! file_exists( $dirname ) ) {
          wp_mkdir_p( $dirname );
        }
        Iboxindia_WP_Settings::set( "upload_path", $dirname );
      }
    }

    public function enqueue_admin_style( $hook ) {
      wp_enqueue_style( 'ibx-wp', IBX_WP_PLUGIN_URL . '/assets/css/ibx-admin.css', array(), IBX_WP_PLUGIN_VER );
      wp_enqueue_style( 'ibx-wp-items', IBX_WP_PLUGIN_URL . '/assets/css/ibx-wp-items.css', array(), IBX_WP_PLUGIN_VER );
      wp_enqueue_style( 'materialize-css', '//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css', array(), '1.0.0' );
      wp_enqueue_style( 'materialize-css-icons', '//fonts.googleapis.com/icon?family=Material+Icons' );
			wp_register_script( 'materialize-js', '//cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js', array( 'jquery' ), '1.0.0', true );
      wp_enqueue_script( 'materialize-js' );

      $existingItems = array();
      $existingItems['theme'] = [];
      foreach ( wp_get_themes() as $key => $theme ) {
        $themeMod = [];
        // var_dump($theme);
        $themeMod['Version'] = $theme->get('Version');
        $themeMod['Name'] = $theme->get('Name');
        $themeMod['Author'] = $theme->get('Author');
        $existingItems['theme'][ $key ] = $themeMod;
      }
      $existingItems['plugin'] = [];
      foreach ( get_plugins() as $key => $plugin ) {
        $temp_array = explode( '/', $key );
        $existingItems['plugin'][str_replace( '.php', '', end( $temp_array ) )] = $plugin;
      }
      
      wp_register_script( 'ibx-wp-admin-functions', IBX_WP_PLUGIN_URL . '/assets/js/ibx-admin-functions.js', array( 'jquery' ), IBX_WP_PLUGIN_VER, true );
      wp_localize_script( 'ibx-wp-admin-functions', 'iboxindiaConfig', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'existingItems' => $existingItems
      ] );
      wp_enqueue_script( 'ibx-wp-admin-functions' );

    }
    
    public function add_menu() {

      global $admin_page_hooks;
      if ( empty ( $admin_page_hooks['iboxindia'] ) ) {
        $page = add_menu_page( IBX_WP_PLUGIN_NAME, 'Iboxindia', 'administrator', 'iboxindia', [ $this, 'displayIboxindiaOverviewDashboard' ], 'dashicons-admin-generic', 55 );
        add_action( "admin_print_styles-{$page}", array ($this, 'enqueue_admin_style' ) );
        do_action( 'ibx_wp_admin_menu' );
      }
    }

    public function displayIboxindiaOverviewDashboard() {
      echo 'Overview';
    }

  }

  /**
   * Kicking this off by calling 'get_instance()' method
   */
  $ibx_admin = Iboxindia_WP_Admin::get_instance();

endif;
