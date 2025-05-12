<?php

/**
 * Swiper Elementor Widget.
 *
 * @package Feebas_Elementor_Widgets
 */
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Feebas_Swiper_Widget extends Widget_Base
{
    public function get_name(): string
    {
        return 'feebas_swiper';
    }

    public function get_title(): string
    {
        return esc_html__('Swiper Camera', 'feebas-elementor-widgets');
    }

    public function get_icon(): string
    {
        return 'eicon-post-slider';
    }

    public function get_categories(): array
    {
        return ['basic'];
    }

    public function get_style_depends(): array
    {
        return ['feebas-tailwind-css'];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'section_items',
            [
                'label' => __('Items', 'feebas-elementor-widgets'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Load all cameras for selection
        $camera_posts = get_posts([
            'post_type'      => 'camera',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        $options = [];
        foreach ($camera_posts as $post) {
            $options[$post->ID] = get_the_title($post);
        }

        $this->add_control(
            'selected_cameras',
            [
                'label'       => __('Select Cameras', 'feebas-elementor-widgets'),
                'type'        => Controls_Manager::SELECT2,
                'options'     => $options,
                'multiple'    => true,
                'label_block' => true,
                'description' => __('Choose cameras to display in slider.', 'feebas-elementor-widgets'),
            ]
        );

        $this->add_control(
            'select_display',
            [
                'label'       => __('Select Display', 'feebas-elementor-widgets'),
                'type'        => Controls_Manager::SELECT,
                'options'     => [
                    'template_1' => esc_html__('Template 1'),
                    'template_2' => esc_html__('Template 2'),
                ],
                'default' => 'template_1',
                'multiple'    => true,
                'label_block' => true,
                'description' => __('Choose how the items are displayed', 'feebas-elementor-widgets'),
            ]
        );

        $this->add_responsive_control(
            'offset_before',
            [
                'type' => Controls_Manager::SLIDER,
                'label' => esc_html__('Offset Before', 'feebas-elementor-widgets'),
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'size' => 200,
                ],
            ]
        );

        $this->add_responsive_control(
            'title_size',
            [
                'type' => Controls_Manager::NUMBER,
                'label' => esc_html__('Title size', 'feebas-elementor-widgets'),
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'default' => 0,
                'selectors' => [
                    '{{WRAPPER}} .ftitle-size' => 'font-size: {{SIZE}}px;',
                ]
            ]
        );

        $this->add_responsive_control(
            'desc_size',
            [
                'type' => Controls_Manager::NUMBER,
                'label' => esc_html__('Description size', 'feebas-elementor-widgets'),
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'default' => 8,
                'selectors' => [
                    '{{WRAPPER}} .fdesc-size' => 'font-size: {{SIZE}}px;',
                ]
            ]
        );

        $this->add_responsive_control(
            'space_between',
            [
                'label' => esc_html__('Space Between', 'feebas-elementor-widgets'),
                'type'  => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px']
            ]
        );

        $this->add_responsive_control(
            'card_height',
            [
                'label' => esc_html__('Card Height', 'feebas-elementor-widgets'),
                'type'  => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 250,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-card-dim' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_width',
            [
                'label' => esc_html__('Card Width', 'feebas-elementor-widgets'),
                'type'  => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 250,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .swiper-card-dim' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => esc_html__('Image Width', 'feebas-elementor-widgets'),
                'type'  => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 250,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .image-card-control' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => esc_html__('Image Height', 'feebas-elementor-widgets'),
                'type'  => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 250,
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .image-card-control' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_responsive_setting($setting_key, $is_slider = true)
    {
        $settings = $this->get_settings_for_display();
        $value = [];

        // For slider controls
        if ($is_slider) {
            $value['desktop'] = isset($settings[$setting_key]['size']) ? $settings[$setting_key]['size'] : 0;
            $value['tablet'] = isset($settings[$setting_key . '_tablet']['size']) ? $settings[$setting_key . '_tablet']['size'] : $value['desktop'];
            $value['mobile'] = isset($settings[$setting_key . '_mobile']['size']) ? $settings[$setting_key . '_mobile']['size'] : $value['tablet'];
        }
        // For number controls
        else {
            $value['desktop'] = isset($settings[$setting_key]) ? $settings[$setting_key] : 0;
            $value['tablet'] = isset($settings[$setting_key . '_tablet']) ? $settings[$setting_key . '_tablet'] : $value['desktop'];
            $value['mobile'] = isset($settings[$setting_key . '_mobile']) ? $settings[$setting_key . '_mobile'] : $value['tablet'];
        }

        return $value;
    }

    private function get_cameras(array $selected_ids): array
    {
        return get_posts([
            'post_type'           => 'camera',
            'post__in'            => $selected_ids,
            'orderby'             => 'post__in',
            'posts_per_page'      => count($selected_ids),
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
        ]);
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $template = $settings['select_display'];
        $selected_ids = is_array($settings['selected_cameras']) ? array_map('absint', $settings['selected_cameras']) : [];
        $offset_values = $this->get_responsive_setting('offset_before', true);
        $slides_per_view = $this->get_responsive_setting('slides_per_view', false);
        $space_between = $this->get_responsive_setting('space_between', true);

        $posts = $this->get_cameras($selected_ids);

        if (empty($posts)) {
            echo '<p>' . esc_html__('Selected cameras not found.', 'feebas-elementor-widgets') . '</p>';
            return;
        }

        $widget_id = esc_attr($this->get_id());
?>
        <div class="swiper swiper-<?php echo $widget_id; ?>">
            <div class="swiper-wrapper">
                <?php foreach ($posts as $post) :
                    $image_id = get_post_meta($post->ID, '_camera_image', true);
                    $image_url = wp_get_attachment_image_url($image_id, 'medium'); // or 'medium', 'thumbnail', etc.
                    $placeholder = "https://placehold.co/600x400";
                    echo '<div class="swiper-slide swiper-card-dim">';
                    switch ($template) {
                        case 'template_2':
                ?>
                            <div class="flex flex-col items-center text-white">
                                <img
                                    src="<?php echo esc_url($image_url ? $image_url : $placeholder); ?>"
                                    alt="<?php echo esc_html(get_the_title($post)) ?> "
                                    class="image-card-control object-center object-cover">
                                <!-- style="height:200px;"> -->
                                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                    <h2 class="ftitle-size font-bold text-center"><?php echo esc_html(get_the_title($post)); ?> </h2>
                                </a>
                            </div>
                        <?php
                            break;

                        case 'template_1':
                        default:
                        ?>
                            <div class="flex bg-[#151515] h-full rounded text-white p-2 md:p-5">
                                <img
                                    class="image-card-control object-center object-center object-cover"
                                    src="<?php echo esc_url($image_url ? $image_url : $placeholder); ?>"
                                    alt="<?php echo esc_html(get_the_title($post)) ?> ">
                                <div class="flex flex-col ml-5">
                                    <h2 class="ftitle-size font-bold mb-1 md:mb-4"><?php echo esc_html(get_the_title($post)); ?> </h2>
                                    <p class="fdesc-size"><?php echo esc_html(get_post_meta($post->ID, '_camera_description', true)) ?></p>
                                    <a
                                        class="text-xs md:text-base mt-auto w-max px-2 py-1 md:px-4 md:py-2 bg-orange-500 rounded-full"
                                        href="<?php echo esc_url(get_permalink($post)); ?>">
                                        Book now
                                    </a>
                                </div>
                            </div>
                            <?php break; ?>
                    <?php } ?>
            </div> <!--.swiper-slide -->
        <?php endforeach; ?>
        </div>
        </div>
        <script>
            (function() {
                new Swiper('.swiper-<?php echo $widget_id; ?>', {
                    // Optional parameters
                    direction: 'horizontal',
                    centeredSlides: false,
                    grabCursor: true,
                    slidesPerView: 'auto',
                    spaceBetween: <?php echo intval($space_between['mobile']) ?>,
                    slidesOffsetBefore: <?php echo intval($offset_values['mobile']) ?>,
                    mousewheel: {
                        enabled: true,
                        forceToAxis: true
                    },
                    breakpoints: {
                        768: {
                            spaceBetween: <?php echo intval($space_between['tablet']) ?>,
                            slidesOffsetBefore: <?php echo intval($offset_values['tablet']) ?>,
                        },
                        1024: {
                            spaceBetween: <?php echo intval($space_between['desktop']) ?>,
                            slidesOffsetBefore: <?php echo intval($offset_values['desktop']) ?>,
                        }
                    }
                });
            })();
        </script>
<?php
    }
}
