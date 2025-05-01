<?php

/**
 * Swiper Elementor Widget.
 *
 * @package Feebas_Elementor_Widgets
 */
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Feebas_Slider_Builder_Widget extends \Elementor\Widget_Base
{
    public function get_name(): string
    {
        return 'feebas_slider_builder';
    }
    public function get_title()
    {
        return 'Slider Builder';
    }

    public function get_icon()
    {
        return 'eicon-slider-push';
    }

    public function get_categories()
    {
        return ['basic'];
    }

    protected function register_controls()
    {

        // Slides Section
        $this->start_controls_section(
            'section_slides',
            [
                'label' => 'Slides',
            ]
        );

        $repeater = new \Elementor\Repeater();

        // The key part - inner section for widget containers
        // $repeater->add_control(
        //     'slide_content',
        //     [
        //         'label' => 'Slide Content',
        //         'type' => \Elementor\Controls_Manager::INNER_SECTION,
        //         'show_label' => false,
        //     ]
        // );

        $this->add_control(
            'slides',
            [
                'label' => 'Slides',
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [],
                    [],
                ],
                'title_field' => 'Slide #{{{ _id }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $slider_id = 'slider-' . $this->get_id();
?>

        <div id="<?php echo esc_attr($slider_id); ?>" class="simple-slider swiper">
            alksjdlsakjdlasjdlaksjl
        </div>

<?php
    }
}
