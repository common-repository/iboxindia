<?php

/**
 * Download File In Uploads Directory
 *
 * Eg. 
 *
 * $file = download_file( `file-url` );
 *
 * if( $file['success'] ) {
 * 		$file_abs_url = $file['data']['file'];
 * 		$file_url     = $file['data']['file'];
 * 		$file_type    = $file['data']['type'];
 * }
 *
 * @param  string $file Download File URL.
 * @return array        Downloaded file data.
 */

function ibx_wp_custom_file_download( $url, $headers=array(), $timeout = 300 ) {
  
  // $settings = IBX_WP::get_option( "settings" );
  // Gives us access to the download_url() and wp_handle_sideload() functions.
  require_once( ABSPATH . 'wp-admin/includes/file.php' );
  
  // WARNING: The file is not automatically deleted, the script must unlink() the file.
  if ( ! $url ) {
    return new WP_Error( 'http_no_url', __( 'Invalid URL Provided.' ) );
  }
  
  $url_filename = basename( parse_url( $url, PHP_URL_PATH ) );
  
  $tmpfname = wp_tempnam( $url_filename );
  if ( ! $tmpfname ) {
    return new WP_Error( 'http_no_file', __( 'Could not create Temporary file.' ) );
  }

  $headers['Accept'] = 'application/octet-stream';

  $http = _wp_http_get_object();
  $response = $http->get(
    $url,
    array(
      'timeout'  => $timeout,
      'stream'   => true,
      'reject_unsafe_urls' => false,
      'filename' => $tmpfname,
      'headers'     => $headers
    )
  );
   
  if ( is_wp_error( $response ) ) {
    unlink( $tmpfname ); // delete temp file
    return $response;
  }
  
  $response_code = wp_remote_retrieve_response_code( $response );
  
  if ( 401 == $response_code ) {
    Iboxindia_WP_Settings::set( "hash", false );
  }
  if ( 200 != $response_code ) {
    $data = array(
      'code' => $response_code,
    );
  
    // Retrieve a sample of the response body for debugging purposes.
    $tmpf = fopen( $tmpfname, 'rb' );
    if ( $tmpf ) {
      /**
       * Filters the maximum error response body size in `download_url()`.
       *
       * @since 5.1.0
       *
       * @see download_url()
       *
       * @param int $size The maximum error response body size. Default 1 KB.
       */
      $response_size = apply_filters( 'download_url_error_max_body_size', KB_IN_BYTES );
      $data['body']  = fread( $tmpf, $response_size );
      fclose( $tmpf );
    }
  
    unlink( $tmpfname );
    return new WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ), $data );
  }
   
  $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );
  if ( $content_md5 ) {
    $md5_check = verify_file_md5( $tmpfname, $content_md5 );
    if ( is_wp_error( $md5_check ) ) {
      unlink( $tmpfname );
      return $md5_check;
    }
  }

  return $tmpfname;
}

function ibx_wp_download_file ( $file_url, $file_name, $timeout_seconds ) {
  
  // // Download file to temp dir.
  $temp_file = ibx_wp_custom_file_download( $file_url, $timeout_seconds );

  // WP Error.
  if ( is_wp_error( $temp_file ) ) {
    return array(
      'success' => false,
      'data'    => $temp_file->get_error_message(),
    );
  }

  // Array based on $_FILE as seen in PHP file uploads.
  $file_args = array(
    'name'     => $file_name,
    'tmp_name' => $temp_file,
    'error'    => 0,
    'size'     => filesize( $temp_file ),
  );

  $overrides = array(

    // Tells WordPress to not look for the POST form
    // fields that would normally be present as
    // we downloaded the file from a remote server, so there
    // will be no form fields
    // Default is true.
    'test_form'   => false,

    // Setting this to false lets WordPress allow empty files, not recommended.
    // Default is true.
    'test_size'   => true,

    // A properly uploaded file will pass this test. There should be no reason to override this one.
    'test_upload' => true,

  );

  // Move the temporary file into the uploads directory.
  // var_dump($file_name);
  $uploadPath = Iboxindia_WP_Settings::get( "upload_path" );
  // var_dump($uploadPath);
  $move_new_file = @copy( $temp_file, $uploadPath . '/' . $file_name );
  unlink( $temp_file );
  // move_uploaded_file( $temp_file, $uploadPath . '/' . $file_name );
  // $results = wp_handle_sideload( $file_args, $overrides );

  // echo '<pre>';
  // print_r($results);
  // echo '</pre>';
  
  if ( isset( $results['error'] ) ) {
    return array(
      'success' => false,
      'data'    => $results,
    );
  }

  // Success!
  return array(
    'success' => true,
    'data'    => $move_new_file,
  );
}