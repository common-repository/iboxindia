<?php
/**
 *
 * @since 2.3.7
 * @package Iboxindia
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'Iboxindia_WP' ) ) :

  /**
   * Admin
   */
  class Iboxindia_WP {

    /**
     * Instance of Iboxindia_WP
     *
     * @since 2.3.7
     * @var (Object) Iboxindia_WP
     */
    private static $instance = null;

    /**
     * Instance of Iboxindia_WP.
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
    }


    public function init() {
    }

    public function isActive() {
      if( ! empty( Iboxindia_WP_Settings::get( "hash" ) ) ) {
        return true;
      }
    }
    public function getKey() {
      return Iboxindia_WP_Settings::get( "hash" );
    }
  }
  Iboxindia_WP::get_instance();
endif;
