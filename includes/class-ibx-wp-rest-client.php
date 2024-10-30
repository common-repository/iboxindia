<?php

use function PHPSTORM_META\type;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Iboxindia_WP_Rest_Client' ) ) :

	/**
	 * Iboxindia_WP_Rest_Client
	 *
	 * @since 1.4.0
	 */
	class Iboxindia_WP_Rest_Client {

    public static function get_binary($uri='', $params=[]) {
      $debug = Iboxindia_WP_Settings::get( "debug" );

      $params['json']=true;
      $hash = Iboxindia_WP_Settings::get( "hash" );
      $url = $uri . '?' . http_build_query($params);

      $args = array(
        'headers' => array(
          'domain' => get_site_url(),
        )
      );
      if ( ! empty( $hash ) ) {
        $args['headers']['Authorization'] = 'Bearer ' . $hash;
      }

      $http_response = wp_remote_get ( $url, $args );
      $response = [];
      if($debug) {
        $response[ 'url' ] = $url;
        $response[ 'hash' ] = $hash;
        $response[ 'http_response' ] = $http_response;
      }

      if ( is_wp_error( $http_response ) ) {
        $error_message = $http_response->get_error_message();
        return $error_message;
        if($error_message == 'Jwt expired!') {
          Iboxindia_WP_Settings::set( "hash", false );
        }
      } else {
        $body = wp_remote_retrieve_body( $http_response );
        return $body;
      }
    }
    public static function get($uri='', $params=[]) {
      $debug = Iboxindia_WP_Settings::get( "debug" );

      $params['json']=true;
      $hash = Iboxindia_WP_Settings::get( "hash" );
      $url = $uri . '?' . http_build_query($params);

      $args = array(
        'headers' => array(
          'domain' => get_site_url(),
        )
      );
      if ( ! empty( $hash ) ) {
        $args['headers']['Authorization'] = 'Bearer ' . $hash;
      }

      $http_response = wp_remote_get ( $url, $args );
      $response = [];
      if($debug) {
        $response[ 'url' ] = $url;
        $response[ 'hash' ] = $hash;
        $response[ 'http_response' ] = $http_response;
      }

      return array_merge( $response, self::parseHttpResponse( $http_response ) );
    }

    public static function post($uri='', $params=[]) {
      $hash = Iboxindia_WP_Settings::get( "hash" );
      $url = $uri . '?json';
      // var_dump(json_encode($params));
      $args = array(
        'headers' => array(
          'domain' => get_site_url(),
        ),
        'body'    => $params
      );
      if ( ! empty( $hash ) ) {
        $args['headers']['Authorization'] = 'Bearer ' . $hash;
      }

      // var_dump($args);
      $http_response = wp_remote_post ( $url, $args );


      $response = [];
      if($debug) {
        $response[ 'url' ] = $url;
        $response[ 'hash' ] = $hash;
        $response[ 'http_response' ] = $http_response;
      }

      return array_merge( $response, self::parseHttpResponse( $http_response ) );
    }

    private static function parseHttpResponse($http_response) {
      if ( is_wp_error( $http_response ) ) {
        $error_message = $http_response->get_error_message();
        $response[ 'error' ] = $error_message;
        if($error_message == 'Jwt expired!') {
          Iboxindia_WP_Settings::set( "hash", false );
        }
      } else {
        $body = wp_remote_retrieve_body( $http_response );
        $tempResponse = json_decode($body, true);
        // $response = json_decode($body, true);
        if($tempResponse['status'] == 1) {
          $response[ 'data' ] = $tempResponse['data'];
        } else {
          if($tempResponse['error'] == 'Jwt expired!') {
            Iboxindia_WP_Settings::set( "hash", false );
          }
          $response[ 'error' ] = $tempResponse['error'];
        }
      }
      return $response;
    }
  }
endif;
?>