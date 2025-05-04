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
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'size' => 300,
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_responsive_control(
            'slides_per_view',
            [
                'type' => Controls_Manager::NUMBER,
                'label' => esc_html__('Slides per view', 'feebas-elementor-widgets'),
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 3
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
                    '{{WRAPPER}} .swiper-card-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
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
                    '{{WRAPPER}} .template-1' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_responsive_setting($setting_key, $default = '', $is_slider = true)
    {
        $settings = $this->get_settings_for_display();
        $value = [];

        // For slider controls
        if ($is_slider) {
            $value['desktop'] = isset($settings[$setting_key]['size']) ? $settings[$setting_key]['size'] : $default;
            $value['tablet'] = isset($settings[$setting_key . '_tablet']['size']) ? $settings[$setting_key . '_tablet']['size'] : $value['desktop'];
            $value['mobile'] = isset($settings[$setting_key . '_mobile']['size']) ? $settings[$setting_key . '_mobile']['size'] : $value['tablet'];
        }
        // For number controls
        else {
            $value['desktop'] = isset($settings[$setting_key]) ? $settings[$setting_key] : $default;
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
        $offset_values = $this->get_responsive_setting('offset_before', 300, true);
        $slides_per_view = $this->get_responsive_setting('slides_per_view', 3, false);

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
                    echo '<div class="swiper-slide">';
                    switch ($template) {
                        case 'template_2':
                ?>
                            <div class="flex flex-col items-center text-white">
                                <img
                                    src="<?php echo esc_url($image_url ? $image_url : $placeholder); ?>"
                                    alt="<?php echo esc_html(get_the_title($post)) ?> "
                                    class="w-full object-center object-cover swiper-card-image">
                                    <!-- style="height:200px;"> -->
                                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                    <h2 class="text-3xl font-bold mb-4"><?php echo esc_html(get_the_title($post)); ?> </h2>
                                </a>
                            </div>
                        <?php
                            break;

                        case 'template_1':
                        default:
                        ?>
                            <div class="flex bg-[#151515] text-white template-1">
                                <img
                                    style="height:200px"
                                    src="<?php echo esc_url($image_url ? $image_url : $placeholder); ?>"
                                    alt="<?php echo esc_html(get_the_title($post)) ?> ">
                                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                                    <h2 class="text-3xl font-bold mb-4"><?php echo esc_html(get_the_title($post)); ?> </h2>
                                </a>
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
                    spaceBetween: 30,
                    grabCursor: true,
                    slidesPerView: <?php echo intval($slides_per_view['mobile']) ?>,
                    slidesOffsetBefore: <?php echo intval($offset_values['mobile']) ?>,
                    mousewheel: {
                        enabled: true,
                        forceToAxis: true
                    },
                    breakpoints: {
                        768: {
                            slidesOffsetBefore: <?php echo intval($offset_values['tablet']) ?>,
                            slidesPerView: <?php echo intval($slides_per_view['tablet']) ?>
                        },
                        1024: {
                            slidesOffsetBefore: <?php echo intval($offset_values['desktop']) ?>,
                            slidesPerView: <?php echo intval($slides_per_view['desktop']) ?>
                        }
                    }
                });
            })();
        </script>
<?php
    }
}
