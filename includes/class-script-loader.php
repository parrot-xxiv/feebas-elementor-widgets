<?php

namespace Feebas;

class Script_Loader {

    public static function register() {
        add_action( 'elementor/frontend/after_register_scripts', [ __CLASS__, 'register_scripts' ] );
        add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'register_scripts' ] );
        add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'enqueue_editor_scripts' ] );
        add_filter( 'script_loader_tag', [ __CLASS__, 'modify_script_tag' ], 10, 3 );
    }

    public static function register_scripts() {
        wp_register_script(
            'feebas-asset-viewer',
            FEW_PLUGIN_URL . 'widgets/js/asset-viewer.js',
            [ 'elementor-frontend' ],
            null,
            true
        );

        wp_register_script(
            'feebas-horizontal-cards',
            FEW_PLUGIN_URL . 'widgets/js/horizontal-cards.js',
            [ 'jquery' ],
            null,
            true
        );

        wp_register_style(
            'feebas-tailwind-css',
            FEW_PLUGIN_URL . 'widgets/css/style.css',
            [],
            null
        );
    }

    public static function enqueue_editor_scripts() {
        wp_enqueue_script( 'feebas-horizontal-cards' );
        wp_localize_script( 'feebas-horizontal-cards', 'FeebasHorizontalCardsSettings', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'feebas_widget_nonce' )
        ]);
    }

    public static function modify_script_tag( $tag, $handle, $src ) {
        if ( $handle === 'feebas-asset-viewer' ) {
            return '<script type="importmap">{"imports":{"three":"https://unpkg.com/three@0.152.2/build/three.module.js"}}</script>' .
                sprintf( '<script type="module" src="%s"></script>', esc_url( $src ) );
        }
        return $tag;
    }
}
