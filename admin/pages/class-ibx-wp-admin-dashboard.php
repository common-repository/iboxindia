<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Dashboard_Page' ) ) :

	/**
	 * Iboxindia_WP_Dashboard_Page
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Dashboard_Page {

		private static $version = '1.0.0';
    /**
     * Instance of Iboxindia_WP_Dashboard_Page
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Dashboard_Page
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Dashboard_Page.
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
      add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
      add_action( 'ibx_wp_admin_menu', array( $this, 'add_menu' ) );
      add_action( "wp_ajax_iboxindia_packages", array( $this, 'getPackages' ) );
      add_action( "wp_ajax_iboxindia_package_info", array( $this, 'getPackageInfo' ) );
      add_action( "wp_ajax_iboxindia_package_thumbnail", array( $this, 'getPackageThumbnail' ) );
      // add_action( "wp_ajax_iboxindia_download_package", array( $this, 'downloadPackage' ) );
      add_action( "wp_ajax_iboxindia_install_package", array( $this, 'installPackage' ) );
      add_action( "wp_ajax_iboxindia_update_package", array( $this, 'updatePackage' ) );
    }
    public function add_menu() {
      $page=add_submenu_page( IBX_WP_PLUGIN_NAME, 'Dashboard', 'Dashboard', 'administrator', IBX_WP_PLUGIN_NAME.'-dashboard', [ $this, 'show' ], 5 );
      $ibx_admin = Iboxindia_WP_Admin::get_instance();
      add_action( "admin_print_styles-{$page}", array ($ibx_admin, 'enqueue_admin_style' ) );
    }

    public function show() {
      $currentTab = isset ( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'theme';
      $tabs = array( 'theme' => 'Themes', 'plugin' => 'Plugins' ); ?>
      <div class="wrap">
        <h2>Iboxindia - <?php echo Iboxindia_WP::get_instance()->isActive() ? 'Premium' : 'Open Source'; ?></h2>
        <h2 class="nav-tab-wrapper">
          <?php foreach( $tabs as $tab => $name ) {
            $class = ( $tab == $currentTab ) ? 'nav-tab-active' : '';
            echo "<a class='nav-tab $class' href='?page=iboxindia-dashboard&tab=$tab'>$name</a>";
          } ?>
        </h2>
        <div class="ibx-items-browser">
          <div class="row ibx-items wp-clearfix">
            <div class="col s4 dummy">
              <div class="ibx-item">
                <div class="ibx-item-screenshot">
                  <div class="ibx-item-version tag"></div>
                  <img src="" alt="" />
                </div>
                <div class="update-message notice inline notice-warning notice-alt">
                  <p>
                    Installed. <span class="update-version">Update to <span>0.0.0</span></span>
                  </p>
                </div>
                <div class="ibx-item-container">
                  <h2 class="ibx-item-name" id=""></h2>
                  <div class="ibx-item-actions">
                    <button data-target="install-update-dialog" class="btn modal-trigger install-button" type="button">Install</button>
                    <button data-existing-ver="0" data-current-ver="0" class="btn button-primary update-button" type="button">Update</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal Structure -->
        <div id="install-update-dialog" class="modal modal-fixed-footer">
          <div class="modal-content">
            <h4 class="header"></h4>
            <div class="row">
              <div class="col s3">
                Name
              </div>
              <div class="col s9">
                <span class="name"></span>
              </div>
            </div>
            <div class="row">
              <div class="col s3">
                Author
              </div>
              <div class="col s9">
                <span class="author"></span>
              </div>
            </div>
            <div class="row">
              <div class="col s3">
                Type
              </div>
              <div class="col s9">
                <span class="type"></span>
              </div>
            </div>
            <div class="row">
              <div class="col s3">
                Version
              </div>
              <div class="col s9">
                <span class="version"></span>
              </div>
            </div>
            <div class="row">
              <div class="col s3">
                Downloaded
              </div>
              <div class="col s9">
                <i class="downloaded material-icons"></i>
              </div>
            </div>
          </div>
          <div class="modal-footer">
          <button type="button" class="waves-effect waves-light btn refresh-info">
              <i class="material-icons left">autorenew</i>
              Refresh Information
            </button>
            <button type="button" class="waves-effect waves-light btn install-package">
              <i class="material-icons right">send</i>
              <span class="lds-dual-ring"></span>
              Install
            </button>
            <button type='button' class="modal-close waves-effect waves-red btn red">Close</a>
          </div>
        </div>
        <script>
          jQuery(document).ready( function() {
            jQuery('#install-update-dialog').modal({
              onOpenStart: refreshModalInfo
            });
            jQuery('#install-update-dialog .refresh-info').on('click', function(e) { e.stopPropagation(); showPackageInfo(jQuery(this).attr('data-type'), jQuery(this).attr('data-slug')); } );
            // jQuery('#install-update-dialog .download-package').on('click', function(e) { e.stopPropagation(); downloadPackage(jQuery(this).attr('data-type'), jQuery(this).attr('data-slug')); } );
            jQuery('#install-update-dialog .install-package').on('click', function(e) { e.stopPropagation(); installPackage(jQuery(this).attr('data-type'), jQuery(this).attr('data-slug')); } );
            jQuery('.ibx-items-browser .ibx-items .install-button').on('click', showInstallUpdateDialog);
            jQuery('.ibx-items-browser .ibx-items .update-button').on('click', showInstallUpdateDialog);
            loadPackages('<?php echo $currentTab;?>');
          });
        </script>
      </div> <?php 
    }
    public function enqueue_scripts() {
      // echo "asd-" . self::_get_uri();
		}
    public function _get_uri() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
			$path      = wp_normalize_path( dirname( __FILE__ ) );
			$theme_dir = wp_normalize_path( get_template_directory() );

			if ( strpos( $path, $theme_dir ) !== false ) {
				return trailingslashit( get_template_directory_uri() . str_replace( $theme_dir, '', $path ) );
			} else {
				return plugin_dir_url( __FILE__ );
			}
		}


    public function getPackages() {
      $type = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : 'theme';

      $packages = Iboxindia_WP_Rest_API::getPackages( $type );
 
      wp_send_json( $packages );
    }
    public function installPackage() {
      $slug = sanitize_key( $_POST['slug'] );
      $type = sanitize_key( $_POST['type'] );
    
      if($type == 'theme') {
        $destination_path = WP_CONTENT_DIR . '/themes';
        // $up = new Theme_Upgrader();
      } else if($type == 'plugin') {
        $destination_path = WP_PLUGIN_DIR;
        // $up = new Plugin_Upgrader();
      }

      $file_loc = Iboxindia_WP_Rest_API::downloadPackage($type, $slug);
      if( file_exists( $file_loc) ) {
        WP_Filesystem();
        $unzipfile = unzip_file( $file_loc, $destination_path );
      }
    
      $resp = [];
      if ( $unzipfile ) {
        $resp['raw'] = $unzipfile;
        $resp['success'] = true;
        $resp['message'] = 'Successfully installed ' . $slug . ' from [' . $file_loc . '] to [' . $destination_path . ']';
      } else {
        $resp['success'] = false;
        $resp['message'] = 'Failed to install ' . $slug . ' from [' . $file_loc . '] to [' . $destination_path . ']';
      }
    
      wp_send_json($resp);
    }
    public function updatePackage() {
      return $this->installPackage();
    }
    function getPackageInfo() {
      $type = sanitize_key( $_GET['type'] );
      $slug = sanitize_key( $_GET['slug'] );
      $package_info = Iboxindia_WP_Rest_API::getPackage($type, $slug);
      $package_info['fileExists'] = true;

      wp_send_json( $package_info );
    }
    function getPackageThumbnail() {
      $type = sanitize_key( $_GET['type'] );
      $slug = sanitize_key( $_GET['slug'] );
      $image_data = Iboxindia_WP_Rest_API::getPackageThumbnail($type, $slug);
      Header('Content-Type: image/png;');
      echo $image_data;
      wp_die( );
    }
    function downloadPackage($type, $slug) {
      Iboxindia_WP_Rest_API::downloadPackage($type, $slug);
      wp_send_json($result);
    }
  }
  Iboxindia_WP_Dashboard_Page::get_instance();
endif;
?>