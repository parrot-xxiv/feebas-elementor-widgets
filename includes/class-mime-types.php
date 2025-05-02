<?php

namespace Feebas;

class Mime_Types {

    public static function register() {
        add_filter( 'upload_mimes', [ __CLASS__, 'add_mime_types' ] );
        add_filter( 'wp_check_filetype_and_ext', [ __CLASS__, 'fix_mime_types' ], 10, 3 );
        add_filter( 'ajax_query_attachments_args', [ __CLASS__, 'show_all_mimes' ] );
    }

    public static function add_mime_types( $mimes ) {
        $mimes['glb'] = 'model/gltf-binary';
        $mimes['gltf'] = 'model/gltf+json';
        return $mimes;
    }

    public static function fix_mime_types( $data, $file, $filename ) {
        $ext = pathinfo( $filename, PATHINFO_EXTENSION );
        if ( $ext === 'glb' ) {
            $data['ext'] = 'glb';
            $data['type'] = 'model/gltf-binary';
        } elseif ( $ext === 'gltf' ) {
            $data['ext'] = 'gltf';
            $data['type'] = 'model/gltf+json';
        }
        return $data;
    }

    public static function show_all_mimes( $query ) {
        if ( isset( $query['post_type'] ) && $query['post_type'] === 'attachment' ) {
            unset( $query['post_mime_type'] );
        }
        return $query;
    }
}
