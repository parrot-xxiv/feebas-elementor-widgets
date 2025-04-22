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
function feebas_register_widgets() {
    // Include and register the simple widget
    require_once plugin_dir_path( __FILE__ ) . 'widgets/class-feebas-simple-widget.php';
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Feebas_Simple_Widget() );
    // Include and register the 3D Asset Viewer widget
    require_once plugin_dir_path( __FILE__ ) . 'widgets/class-feebas-asset-viewer-widget.php';
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Feebas_Asset_Viewer_Widget() );
    // Include and register the Horizontal Cards widget
    require_once plugin_dir_path( __FILE__ ) . 'widgets/class-feebas-horizontal-cards-widget.php';
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Feebas_Horizontal_Cards_Widget() );
    // 
    require_once plugin_dir_path( __FILE__ ) . 'widgets/class-feebas-swiper-widget.php';
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Feebas_Swiper_Widget() );
}
add_action( 'elementor/widgets/widgets_registered', 'feebas_register_widgets' );
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
    // Register and enqueue the horizontal cards editor script for dynamic controls
    // Register the horizontal cards editor script (enqueued below)
    wp_register_script(
        'feebas-horizontal-cards',
        plugin_dir_url( __FILE__ ) . 'widgets/js/horizontal-cards.js',
        array( 'jquery' ),
        null,
        true
    );
    // Register stylesheet for Tailwind CSS
    wp_register_style(
        'feebas-tailwind-css',
        plugin_dir_url( __FILE__ ) . 'widgets/css/style.css',
        array(),
        null
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

// AJAX handler to provide posts for the Horizontal Cards widget select control
add_action( 'wp_ajax_feebas_get_posts_by_type', 'feebas_get_posts_by_type' );
/**
 * AJAX callback: fetch posts based on selected post type and return option list.
 */
function feebas_get_posts_by_type() {
    $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : '';
    if ( ! post_type_exists( $post_type ) ) {
        wp_send_json_error();
    }
    $posts = get_posts( array(
        'post_type'      => $post_type,
        'posts_per_page' => -1,
    ) );
    $options = array();
    foreach ( $posts as $post ) {
        $options[ $post->ID ] = get_the_title( $post );
    }
    wp_send_json_success( $options );
}
/**
 * Enqueue and localize Horizontal Cards editor script.
 */
function feebas_enqueue_horizontal_cards_script() {
    wp_enqueue_script( 'feebas-horizontal-cards' );
    wp_localize_script(
        'feebas-horizontal-cards',
        'FeebasHorizontalCardsSettings',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce('feebas_widget_nonce')
        )
    );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'feebas_enqueue_horizontal_cards_script' );

add_action('wp_head', function(){
    ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <?php
})

?>
