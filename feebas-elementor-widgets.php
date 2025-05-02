<?php
/**
 * Plugin Name:     Feebas Elementor Widgets
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     feebas-elementor-widgets
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Feebas_Elementor_Widgets
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'FEW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'FEW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoload core classes
require_once FEW_PLUGIN_PATH . 'includes/class-feebas-plugin.php';

// Run the plugin
add_action( 'plugins_loaded', [ 'Feebas\Plugin', 'init' ] );