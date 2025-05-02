<?php

namespace Feebas;

class Widget_Loader {

    public static function register() {
        add_action( 'elementor/widgets/widgets_registered', [ __CLASS__, 'load_widgets' ] );
    }

    public static function load_widgets() {
        $widgets = [
            'class-asset-viewer.php',
            'class-horizontal-cards.php',
            'class-simple.php',
            'class-slider-builder.php',
            'class-swiper.php',
            // .. add other widgets
        ];

        foreach ( $widgets as $file ) {
            require_once FEW_PLUGIN_PATH . 'widgets/' . $file;
        }

        $manager = \Elementor\Plugin::instance()->widgets_manager;
        $manager->register_widget_type( new \Feebas_Simple_Widget() );
        $manager->register_widget_type( new \Feebas_Asset_Viewer_Widget() );
        $manager->register_widget_type( new \Feebas_Horizontal_Cards_Widget() );
        $manager->register_widget_type( new \Feebas_Swiper_Widget() );
        $manager->register_widget_type( new \Feebas_Slider_Builder_Widget() );
        // .. add other widgets
    }
}
