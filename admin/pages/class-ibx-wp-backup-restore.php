<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// require_once '../external/google-api-php-client/vendor/autoload.php';

if ( ! class_exists( 'Iboxindia_WP_Backup_Restore_Page' ) ) :

	/**
	 * Iboxindia_WP_Backup_Restore_Page
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Backup_Restore_Page {

    /**
     * Instance of Iboxindia_WP_Backup_Restore_Page
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Backup_Restore_Page
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Backup_Restore_Page.
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
      add_action( 'add_iboxindia_settings', array( $this, 'add_settings' ) );
    }
    public function add_menu() {
      $page = add_submenu_page( IBX_WP_PLUGIN_NAME, 'Backup Restore', 'Backup Restore', 'administrator', IBX_WP_PLUGIN_NAME.'-backup-restore', [ $this, 'show' ], 20 );
      $ibx_admin = Iboxindia_WP_Admin::get_instance();
      add_action( "admin_print_styles-{$page}", array ($ibx_admin, 'enqueue_admin_style' ) );

    }

    public function add_settings() { ?>
      <div class="row">
        <div class="col s12">
        <form class="ajax-form" action="<?php echo admin_url( 'admin-ajax.php?action=iboxindia_update_backup_settings' ) ?>" method="POST">
          <div class="p-10 blue-grey darken-1">
            <div class="white-text">
              <h6>Backup/Restore Settings</h6>
              <p>I am a very simple card. I am good at containing small bits of information.
              I am convenient because I require little markup to use effectively.</p>
            </div>
            <div class="">
              <button type="submit" class="btn">Save</a>
            </div>
          </div>
        </div>
      </div>
      <?php
    }

    public function show() { 
      $post_type_args = array(
        'public' => true,
        'show_ui' => true
      );
      $post_types = get_post_types($post_type_args);
      ?>
      <form class="ajax-form" action="<?php echo admin_url( 'admin-ajax.php?action=iboxindia_add_backup' ) ?>" method="POST" data-success-callback="reloadPage">
        <div class="row">
          <div class="col s12">
            <div class="white">
              asd
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col s12">
            <ul class="tabs">
              <li class="tab col"><a class="active" href="#settings">Settings</a></li>
              <li class="tab col"><a href="#themes">Themes</a></li>
              <li class="tab col"><a href="#plugins">Plugins</a></li>
              <?php foreach ( $post_types as $key => $post_type ) { ?>
                <!-- <?php var_dump($key); ?>
                <?php var_dump($post_type); ?> -->
                <li class="tab col"><a href="#<?php echo $key; ?>"><?php echo $post_type; ?></a></li>
              <?php } ?>
            </ul>
          </div>
          <div id="settings" class="col s12">
            <div class="tab-content white">
              <div class="row">
                <div class="col s4">
                  <label>
                    <input type="checkbox" name="site-title" />
                    <span>Site Title</span>
                  </label>
                </div>
                <div class="col s4">
                  <label>
                    <input type="checkbox" name="site-subtitle"/>
                    <span>Subtitle</span>
                  </label>
                </div>
                <div class="col s4">
                  <label>
                    <input type="checkbox" name="site-date-time-format"/>
                    <span>Date/time Format</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div id="themes" class="col s12">
            <?php $curr_theme = wp_get_theme(); ?>
            <div class="tab-content white">
              <div class="row">
                <div class="col s4">
                  <label>
                    <input type="radio" name="export-theme" value="active-theme" />
                    <span>Only Active</span>
                  </label>
                </div>
                <div class="col s4">
                  <label>
                  <input type="radio" name="export-theme" value="selected-theme" />
                    <span>Selected</span>
                  </label>
                </div>
              </div>
              <div class="theme-list">
                <div class="row">
                  <?php foreach ( wp_get_themes() as $key => $theme ) { ?>
                  <div class="col s4 m-tb-10">
                    <label>
                      <input type="checkbox" name="selected-themes[]" value="<?php echo $theme->get_stylesheet(); ?>" 
                      <?php echo $theme->get_stylesheet() === $curr_theme->get_stylesheet() ? 'checked="checked" disabled="disabled"' : '' ?>/>
                      <span><?php echo $theme->get('Name'); ?></span>
                    </label>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <div id="plugins" class="col s12">
            <div class="tab-content white">
              <div class="row">
                <div class="col s4">
                  <label>
                    <input type="radio" name="export-theme" value="active-theme" />
                    <span>Only Active</span>
                  </label>
                </div>
                <div class="col s4">
                  <label>
                  <input type="radio" name="export-theme" value="selected-theme" />
                    <span>Selected</span>
                  </label>
                </div>
              </div>
              <div class="theme-list">
                <div class="row">
                  <?php foreach ( get_plugins() as $key => $plugin ) { ?>
                  <div class="col s4 m-tb-10">
                    <label>
                      <input type="checkbox" name="selected-themes[]" value="<?php echo $key; ?>" 
                      <?php echo is_plugin_active($key) ? 'checked="checked" disabled="disabled"' : '' ?>/>
                      <span><?php echo $plugin['Name']; ?></span>
                    </label>
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <?php foreach ( $post_types as $key => $post_type ) { ?>
            <div id="<?php echo $key; ?>" class="col s12">
              <div class="tab-content white">
                <div class="row">
                  <div class="col s4">
                    <label>
                      <input type="radio" name="export-theme" value="active-theme" />
                      <span>Only Active</span>
                    </label>
                  </div>
                  <div class="col s4">
                    <label>
                    <input type="radio" name="export-theme" value="selected-theme" />
                      <span>Selected</span>
                    </label>
                  </div>
                </div>
                <div class="theme-list">
                  <div class="row">
                    <?php foreach ( get_posts( [ 'post_type' => $post_type, 'post_status' => get_post_stati(), 'sort_column'=>'post_status'] ) as $post ) { ?>
                    <div class="col s4 m-tb-10">
                      <label>
                        <input type="checkbox" name="selected-themes[]" value="<?php echo $post->ID; ?>" 
                        <?php echo $post->post_status === 'publish' ? 'checked="checked" disabled="disabled"' : '' ?>/>
                        <span><?php echo $post->post_title; ?> <span class="chip"><?php echo $post->post_status; ?></span></span>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </form>
    <?php
    }
  }
  Iboxindia_WP_Backup_Restore_Page::get_instance();
endif;
?>