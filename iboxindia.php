<?php

/**
 * Plugin Name: Iboxindia
 * Plugin URI: https://iboxindia.com/wordpress/
 * Description: Plugin to configure sites for clients of IboxIndia. Kindly contact info@iboxindia.com for more information.
 * Version: 2.0.0
 * Author: IboxIndia
 * Author URI: https://iboxindia.com
 * Text Domain: iboxindia 
 *
 * @package Iboxindia
 */

/**
 * Set constants.
 */
if( !function_exists('get_plugin_data') ){
  require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
$plugin_data = get_plugin_data( __FILE__ );

define( 'IBX_WP_PLUGIN_VER', $plugin_data['Version'] );

define( 'IBX_WP_REQUIRED_WP_VERSION', $plugin_data['RequiresWP'] );

define( 'IBX_WP_TEXT_DOMAIN', $plugin_data['TextDomain'] );

define( 'IBX_WP_PLUGIN', __FILE__ );

define( 'IBX_WP_PLUGIN_BASENAME', plugin_basename( IBX_WP_PLUGIN ) );

define( 'IBX_WP_PLUGIN_NAME', trim( dirname( IBX_WP_PLUGIN_BASENAME ), '/' ) );

define( 'IBX_WP_PLUGIN_DIR', untrailingslashit( dirname( IBX_WP_PLUGIN ) ) );

define( 'IBX_WP_PLUGIN_MODULES_DIR', IBX_WP_PLUGIN_DIR . '/modules' );

define( 'IBX_WP_PLUGIN_URL', untrailingslashit( plugins_url( '', IBX_WP_PLUGIN ) ) );

require_once IBX_WP_PLUGIN_DIR . '/admin/classes/class-ibx-wp.php';
require_once IBX_WP_PLUGIN_DIR . '/admin/classes/class-ibx-wp-settings.php';

// Startup Notices.
require_once IBX_WP_PLUGIN_DIR . '/admin/ibx-notices/class-ibx-notices.php';

// 

require_once IBX_WP_PLUGIN_DIR . '/includes/class-ibx-wp-rest-client.php';
require_once IBX_WP_PLUGIN_DIR . '/includes/class-ibx-wp-rest-api.php';

require_once IBX_WP_PLUGIN_DIR . '/admin/pages/class-ibx-wp-admin-dashboard.php';
require_once IBX_WP_PLUGIN_DIR . '/admin/pages/class-ibx-wp-package-installer.php';
require_once IBX_WP_PLUGIN_DIR . '/admin/classes/class-ibx-wp-admin.php';

require_once IBX_WP_PLUGIN_DIR . '/admin/pages/class-ibx-wp-admin-settings.php';
require_once IBX_WP_PLUGIN_DIR . '/admin/pages/class-ibx-wp-data-import.php';
require_once IBX_WP_PLUGIN_DIR . '/admin/pages/class-ibx-wp-backup-restore.php';

require_once IBX_WP_PLUGIN_DIR . '/load.php';


// Step 1: Register custom endpoint
function custom_endpoint_init() {
  add_rewrite_rule('^iboxindia-package/?$', 'index.php?iboxindia-package=1', 'top');
}
add_action('init', 'custom_endpoint_init');

// Step 2: Flush rewrite rules on activation
function custom_endpoint_activation() {
  custom_endpoint_init();
  flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'custom_endpoint_activation');

// Step 3: Handle the endpoint
function custom_endpoint_template_redirect() {
  global $wp_query;
  if (isset($wp_query->query_vars['iboxindia-package'])) {
    echo "dd";
      exit;
  }
}
add_action('template_redirect', 'custom_endpoint_template_redirect');