<?php
/**
 * Plugin Name: Google Analytics 4 Extensions
 * Plugin URI: https://github.com/xwp/ga4-extensions
 * Description: Adds custom data to GA4 analytics.
 * Version: 1.0
 * Author: XWP
 * Author URI: https://xwp.co
 * License: GPLv2+
 * Text Domain: ga4-extensions
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Initialize the plugin.
 */
function ga4_ext_init() {

	// Outputs the GA4 tag.
	require_once plugin_dir_path( __FILE__ ) . 'includes/ga4-tag.php';
}
add_action( 'plugins_loaded', 'ga4_ext_init' );
