<?php

namespace Feebas;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Plugin {

    public static function init() {
        $instance = new self();
        $instance->load_dependencies();
        $instance->setup_hooks();
    }

    private function load_dependencies() {
        require_once FEW_PLUGIN_PATH . 'includes/class-mime-types.php';
        require_once FEW_PLUGIN_PATH . 'includes/class-script-loader.php';
        require_once FEW_PLUGIN_PATH . 'includes/class-widget-loader.php';
        require_once FEW_PLUGIN_PATH . 'includes/class-ajax-handler.php';
    }

    private function setup_hooks() {
        Mime_Types::register();
        Script_Loader::register();
        Ajax_Handler::register();
        Widget_Loader::register();
    }
}
