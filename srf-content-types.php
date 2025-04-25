<?php
/**
 * Plugin Name: SRF Forge
 * Plugin URI: https://syngapresearchfund.org/
 * Description: Custom-crafted tools and content types for the SRF website.
 * Author: Daniel W. Robert
 * Author URI: https://dwr.io/
 * Text Domain: srf-forge
 * Domain Path: /languages
 * Requires WP: 5.6
 * Version: 1.0.0
 *
 * @package srf-forge
 */

// Disable direct file access
defined( 'ABSPATH' ) || exit;

// Load text domain
function srf_load_textdomain() {

	load_plugin_textdomain( 'srf-forge', false, plugin_dir_path( __FILE__ ) . 'languages/' );

}
add_action( 'plugins_loaded', 'srf_load_textdomain' );

// Require CPT classes
require_once __DIR__ . '/includes/class-srf-warriors.php';
require_once __DIR__ . '/includes/class-srf-siblings.php';
require_once __DIR__ . '/includes/class-srf-team.php';
require_once __DIR__ . '/includes/class-srf-resources.php';
require_once __DIR__ . '/includes/class-srf-events.php';
require_once __DIR__ . '/includes/class-srf-podcasts.php';