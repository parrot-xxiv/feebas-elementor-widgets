<?php

namespace Feebas;

class Ajax_Handler {

    public static function register() {
        add_action( 'wp_ajax_feebas_get_posts_by_type', [ __CLASS__, 'get_posts_by_type' ] );
    }

    public static function get_posts_by_type() {
        check_ajax_referer( 'feebas_widget_nonce', 'nonce' );

        $post_type = sanitize_text_field( wp_unslash( $_POST['post_type'] ?? '' ) );
        if ( ! post_type_exists( $post_type ) ) {
            wp_send_json_error();
        }

        $posts = get_posts( [ 'post_type' => $post_type, 'posts_per_page' => -1 ] );
        $options = [];
        foreach ( $posts as $post ) {
            $options[ $post->ID ] = get_the_title( $post );
        }

        wp_send_json_success( $options );
    }
}
