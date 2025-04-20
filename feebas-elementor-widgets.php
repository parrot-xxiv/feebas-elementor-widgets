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

add_filter( 'upload_mimes', function( $mime_types ) {
    $mime_types['glb']  = 'model/gltf-binary';
    $mime_types['gltf'] = 'model/gltf+json';
    return $mime_types;
} );

add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {
    $ext = pathinfo( $filename, PATHINFO_EXTENSION );

    if ( $ext === 'glb' ) {
        $data['ext']  = 'glb';
        $data['type'] = 'model/gltf-binary';
    }

    if ( $ext === 'gltf' ) {
        $data['ext']  = 'gltf';
        $data['type'] = 'model/gltf+json';
    }

    return $data;
}, 10, 4 );

function feebas_show_all_mime_types($query) {
    if (isset($query['post_type']) && $query['post_type'] === 'attachment') {
        unset($query['post_mime_type']);
    }
    return $query;
}
add_filter('ajax_query_attachments_args', 'feebas_show_all_mime_types');

// Register simple Elementor widget.
function feebas_register_simple_widget() {
    // Include and register the simple widget
    require_once plugin_dir_path( __FILE__ ) . 'widgets/class-feebas-simple-widget.php';
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Feebas_Simple_Widget() );
    // Include and register the 3D Asset Viewer widget
    require_once plugin_dir_path( __FILE__ ) . 'widgets/class-feebas-asset-viewer-widget.php';
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Feebas_Asset_Viewer_Widget() );
}
add_action( 'elementor/widgets/widgets_registered', 'feebas_register_simple_widget' );
// Register and enqueue scripts for the 3D Asset Viewer widget
/**
 * Register the Asset Viewer module script as an ES module.
 */
function feebas_register_widget_scripts() {
    wp_register_script(
        'feebas-asset-viewer',
        plugin_dir_url( __FILE__ ) . 'widgets/js/asset-viewer.js',
        array( 'elementor-frontend' ),
        null,
        true
    );
}
add_action( 'elementor/frontend/after_register_scripts', 'feebas_register_widget_scripts' );
add_action( 'elementor/editor/after_enqueue_scripts', 'feebas_register_widget_scripts' );

// For the 3D Asset Viewer, output an import map and load as a module
add_filter( 'script_loader_tag', 'feebas_asset_viewer_module_tag', 10, 3 );
/**
 * Replace the default script tag for the asset viewer with an importmap + module script.
 */
function feebas_asset_viewer_module_tag( $tag, $handle, $src ) {
    if ( 'feebas-asset-viewer' === $handle ) {
        // Import map for bare-specifier 'three'
        $importmap = '<script type="importmap">'
            . '{"imports":{"three":"https://unpkg.com/three@0.152.2/build/three.module.js"}}'
            . '</script>';
        // Load our viewer as an ES module
        $module = sprintf( '<script type="module" src="%s"></script>', esc_url( $src ) );
        return $importmap . $module;
    }
    return $tag;
}