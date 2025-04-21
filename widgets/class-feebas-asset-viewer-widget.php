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
        // Zoom distance limits: minimum and maximum
        $this->add_control(
            'min_zoom',
            [
                'label'       => __( 'Minimum Zoom Distance', 'feebas-elementor-widgets' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 0,
                'description' => __( 'Minimum distance camera can zoom (0 = no limit).', 'feebas-elementor-widgets' ),
            ]
        );
        $this->add_control(
            'max_zoom',
            [
                'label'       => __( 'Maximum Zoom Distance', 'feebas-elementor-widgets' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 0,
                'description' => __( 'Maximum distance camera can zoom out (0 = no limit).', 'feebas-elementor-widgets' ),
            ]
        );

        // Auto-rotate options
        $this->add_control(
            'auto_rotate',
            [
                'label'        => __( 'Auto Rotate', 'feebas-elementor-widgets' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'feebas-elementor-widgets' ),
                'label_off'    => __( 'No', 'feebas-elementor-widgets' ),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );
        $this->add_control(
            'auto_rotate_x',
            [
                'label'        => __( 'Rotate X Axis', 'feebas-elementor-widgets' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'feebas-elementor-widgets' ),
                'label_off'    => __( 'No', 'feebas-elementor-widgets' ),
                'return_value' => 'yes',
                'default'      => 'no',
                'condition'    => [
                    'auto_rotate' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'auto_rotate_y',
            [
                'label'        => __( 'Rotate Y Axis', 'feebas-elementor-widgets' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'feebas-elementor-widgets' ),
                'label_off'    => __( 'No', 'feebas-elementor-widgets' ),
                'return_value' => 'yes',
                'default'      => 'no',
                'condition'    => [
                    'auto_rotate' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'auto_rotate_z',
            [
                'label'        => __( 'Rotate Z Axis', 'feebas-elementor-widgets' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'feebas-elementor-widgets' ),
                'label_off'    => __( 'No', 'feebas-elementor-widgets' ),
                'return_value' => 'yes',
                'default'      => 'no',
                'condition'    => [
                    'auto_rotate' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'auto_rotate_speed',
            [
                'label'       => __( 'Auto Rotate Speed (deg/s)', 'feebas-elementor-widgets' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 30,
                'description' => __( 'Rotation speed in degrees per second.', 'feebas-elementor-widgets' ),
                'condition'   => [
                    'auto_rotate' => 'yes',
                ],
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
        // Pass zoom distance limits to viewer (0 = no limit)
        $this->add_render_attribute( 'container', 'data-min-zoom', floatval( $settings['min_zoom'] ) );
        $this->add_render_attribute( 'container', 'data-max-zoom', floatval( $settings['max_zoom'] ) );
        // Pass auto-rotate options
        $this->add_render_attribute( 'container', 'data-auto-rotate', $settings['auto_rotate'] === 'yes' ? 'true' : 'false' );
        $this->add_render_attribute( 'container', 'data-auto-rotate-x', $settings['auto_rotate_x'] === 'yes' ? 'true' : 'false' );
        $this->add_render_attribute( 'container', 'data-auto-rotate-y', $settings['auto_rotate_y'] === 'yes' ? 'true' : 'false' );
        $this->add_render_attribute( 'container', 'data-auto-rotate-z', $settings['auto_rotate_z'] === 'yes' ? 'true' : 'false' );
        $this->add_render_attribute( 'container', 'data-auto-rotate-speed', floatval( $settings['auto_rotate_speed'] ) );
        $height = intval( $settings['height'] );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'container' ); ?> style="width:100%; height:<?php echo $height; ?>px;"></div>
        <?php
    }
}