<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Data_Import_Page' ) ) :

	/**
	 * Iboxindia_WP_Data_Import_Page
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Data_Import_Page {

    /**
     * Instance of Iboxindia_WP_Data_Import_Page
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP_Data_Import_Page
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP_Data_Import_Page.
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
    }
    public function add_menu() {
      $page = add_submenu_page( IBX_WP_PLUGIN_NAME, 'Import Data', 'Import Data', 'administrator', IBX_WP_PLUGIN_NAME.'-import-data', [ $this, 'show' ], 15 );
      $ibx_admin = Iboxindia_WP_Admin::get_instance();
      add_action( "admin_print_styles-{$page}", array ($ibx_admin, 'enqueue_admin_style' ) );
    }


    public function show() {
      echo "import data";
    }
  }
  Iboxindia_WP_Data_Import_Page::get_instance();
endif;
?>