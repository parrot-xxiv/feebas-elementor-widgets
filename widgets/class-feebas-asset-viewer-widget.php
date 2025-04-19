<?php
/**
 * 3D Asset Viewer Elementor Widget.
 *
 * @package Feebas_Elementor_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

class Feebas_Asset_Viewer_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'feebas_3d_asset_viewer';
    }

    public function get_title() {
        return __( '3D Asset Viewer', 'feebas-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-globe';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

    public function get_script_depends() {
        return [ 'feebas-asset-viewer' ];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_asset',
            [
                'label' => __( '3D Asset', 'feebas-elementor-widgets' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'asset',
            [
                'label'      => __( 'Select GLTF/GLB File', 'feebas-elementor-widgets' ),
                'type'       => Controls_Manager::MEDIA,
                'media_type' => 'file',
                'dynamic'    => [ 'active' => true ],
                'label_block'=> true,
            ]
        );

        $this->add_control(
            'height',
            [
                'label'   => __( 'Viewer Height (px)', 'feebas-elementor-widgets' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 400,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if ( empty( $settings['asset']['url'] ) ) {
            echo '<p>' . esc_html__( 'Please select a 3D model file.', 'feebas-elementor-widgets' ) . '</p>';
            return;
        }
        $container_id = 'feebas-asset-viewer-' . $this->get_id();
        $this->add_render_attribute( 'container', 'id', $container_id );
        $this->add_render_attribute( 'container', 'class', 'feebas-asset-viewer-container' );
        $this->add_render_attribute( 'container', 'data-asset-url', esc_url( $settings['asset']['url'] ) );
        $height = intval( $settings['height'] );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'container' ); ?> style="width:100%; height:<?php echo $height; ?>px;"></div>
        <?php
    }
}