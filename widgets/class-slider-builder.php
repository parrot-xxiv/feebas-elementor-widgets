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
        return 'Slider Builder(BROKEN)';
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

        $repeater->add_control(
            'slide_title',
            [
                'label' => __('Slide Title', 'custom-slider-widget'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Slide', 'custom-slider-widget'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __('Slides', 'custom-slider-widget'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'slide_title' => __('Slide #1', 'custom-slider-widget'),
                    ],
                    [
                        'slide_title' => __('Slide #2', 'custom-slider-widget'),
                    ],
                    [
                        'slide_title' => __('Slide #3', 'custom-slider-widget'),
                    ],
                ],
                'title_field' => '{{{ slide_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $id_int = substr($this->get_id_int(), 0, 3);

        $this->add_render_attribute([
            'slider' => [
                'class' => 'custom-slider-widget-container swiper',
                'data-slider-id' => $id_int,
            ],
            'slider-wrapper' => [
                'class' => 'swiper-wrapper',
            ],
        ]);

?>
        <div <?php echo $this->get_render_attribute_string('slider'); ?>>
            <div <?php echo $this->get_render_attribute_string('slider-wrapper'); ?>>
                <?php foreach ($settings['slides'] as $index => $slide) :
                    $slide_id = $this->get_id() . '-' . $index;
                    $this->add_render_attribute('slide-' . $index, [
                        'class' => 'swiper-slide elementor-repeater-item-' . $slide['_id'],
                        'id' => $slide_id,
                    ]);
                ?>
                    <div <?php echo $this->get_render_attribute_string('slide-' . $index); ?>>
                        <div class="elementor-container">
                            <div class="elementor-row">
                                <div class="elementor-column elementor-col-100">
                                    <div class="elementor-column-wrap elementor-element-populated">
                                        <div class="elementor-widget-wrap">
                                            <?php
                                            $this->add_inline_editing_attributes('slide_title_' . $index);
                                            ?>
                                            <h4 class="slide-title"><?php echo $slide['slide_title']; ?></h4>

                                            <?php
                                            // This properly sets up the container for inner content
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                var sliderContainer = $('[data-slider-id="<?php echo $id_int; ?>"]');

                if (sliderContainer.length) {
                    var swiper = new Swiper(sliderContainer[0], {
                        slidesPerView: 1,
                        spaceBetween: 30,
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                    });
                }
            });
        </script>
    <?php
    }

    /**
     * Render widget as plain content.
     */
    protected function content_template()
    {
    ?>
        <#
            var id_int=view.getIDInt().toString().substr(0, 3);
            #>
            <div class="custom-slider-widget-container swiper" data-slider-id="{{ id_int }}">
                <div class="swiper-wrapper">
                    <# _.each(settings.slides, function(slide, index) { #>
                        <div class="swiper-slide elementor-repeater-item-{{ slide._id }}">
                            <div class="elementor-container">
                                <div class="elementor-row">
                                    <div class="elementor-column elementor-col-100">
                                        <div class="elementor-column-wrap elementor-element-populated">
                                            <div class="elementor-widget-wrap">
                                                <h4 class="slide-title">{{{ slide.slide_title }}}</h4>
                                                <!-- This properly sets up the container for inner content -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <# }); #>
                </div>

                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
    <?php
    }
}
