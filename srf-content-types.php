<?php
/**
 * Plugin Name: SRF Content Types
 * Plugin URI: https://syngapresearchfund.org/
 * Description: Custom content types for the SRF website
 * Author: Daniel W. Robert
 * Author URI: https://dwr.io/
 * Text Domain: srf-content-types
 * Domain Path: /languages
 * Requires WP: 5.6
 * Version: 1.0.0
 *
 * @package SRF\Content_Types
 */

// Disable direct file access
defined( 'ABSPATH' ) || exit;

// Load text domain
function srf_load_textdomain() {

	load_plugin_textdomain( 'srf-content-types', false, plugin_dir_path( __FILE__ ) . 'languages/' );

}
add_action( 'plugins_loaded', 'srf_load_textdomain' );

// Require CPT classes
require_once __DIR__ . '/includes/class-srf-people.php';
require_once __DIR__ . '/includes/class-srf-events.php';