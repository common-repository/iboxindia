<?php

use function PHPSTORM_META\type;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Rest_API' ) ) :

	/**
	 * Iboxindia_WP_Rest_API
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Rest_API {

    private static $base_url = 'https://api.iboxindia.com';

    public static function getPackages( $type ) {
      return Iboxindia_WP_Rest_Client::get(self::$base_url . '/wp/packages/' . $type);
    }
    public static function getPackage( $type, $slug ) {
      return Iboxindia_WP_Rest_Client::get(self::$base_url . '/wp/packages/' . $type . '/' . $slug );
    }
    public static function getPackageThumbnail( $type, $slug ) {
      return Iboxindia_WP_Rest_Client::get_binary(self::$base_url . '/wp/packages/' . $type . '/' . $slug . '/thumbnail' );
    }
    public static function downloadPackage( $type, $slug ) {
      // return Iboxindia_WP_Rest_Client::get_binary(self::$base_url . '/wp/packages/' . $type . '/' . $slug . '/download' );
      
      $hash = Iboxindia_WP_Settings::get( "hash" );
      $headers = array(
        'domain' => get_site_url()
      );
      if ( ! empty( $hash ) ) {
        $headers['Authorization'] = 'Bearer ' . $hash;
      }
      return ibx_wp_custom_file_download(self::$base_url . '/wp/packages/' . $type . '/' . $slug . '/download', $headers);
    }
    public static function loginUser ($username, $password ) {
      return Iboxindia_WP_Rest_Client::post(self::$base_url . '/auth/signin', ["email" => $username, "password" => $password]);
    }
  }
endif;
?>