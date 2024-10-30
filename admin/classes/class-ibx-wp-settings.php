<?php
/**
 * Iboxindia WP Settings
 *
 * @since 2.3.7
 * @package Iboxindia
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'Iboxindia_WP_Settings' ) ) :

  class Iboxindia_WP_Settings {

    private static $settings_key = 'iboxindia_settings';

    public static function get( $name, $default = false ) {
      $options = get_option( self::$settings_key );
      // var_dump($options);

      if ( false === $options ) {
        return $default;
      }

      if ( isset( $options[$name] ) ) {
        return $options[$name];
      } else {
        return $default;
      }
    }

    public static function set( $name, $value = false ) {
      $options = get_option( self::$settings_key );
      $options = ( false === $options ) ? array() : (array) $options;
      $options = array_merge( $options, array( $name => $value ) );
      update_option( self::$settings_key, $options );
    }
  
    public static function reset( ) {
      update_option( self::$settings_key, false );
    }
  }
endif;
?>