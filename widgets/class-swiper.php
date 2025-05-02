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

    public function get_script_depends(): array
    {
        return ['swiper'];
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

        $this->end_controls_section();
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
        $selected_ids = is_array($settings['selected_cameras']) ? array_map('absint', $settings['selected_cameras']) : [];

        if (empty($selected_ids)) {
            echo '<p>' . esc_html__('No cameras selected.', 'feebas-elementor-widgets') . '</p>';
            return;
        }

        $posts = $this->get_cameras($selected_ids); 

        if (empty($posts)) {
            echo '<p>' . esc_html__('Selected cameras not found.', 'feebas-elementor-widgets') . '</p>';
            return;
        }

        $widget_id = esc_attr($this->get_id());
?>
        <div class="swiper swiper-<?php echo $widget_id; ?>">
            <div class="swiper-wrapper">
                <?php foreach ($posts as $post) : ?>
                    <div class="swiper-slide">
                        <div class="flex bg-[#151515] space-x-5 text-white p-5 rounded">
                            <div class="flex flex-col">
                                <h3 class="font-semibold text-4xl"><?php echo esc_html(get_the_title($post)); ?></h3>
                                <p class="mt-5 mb-auto"><?php echo esc_html(get_the_excerpt($post)); ?></p>
                                <a href="<?php echo esc_url(get_permalink($post)); ?>" class="px-3 py-2 rounded bg-orange-400 text-white">
                                    <?php esc_html_e('Book now', 'feebas-elementor-widgets'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
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
                    slidesPerView: 2,
                    grabCursor: true,
                    slidesOffsetBefore: 300,
                    mousewheel: {
                        enabled: true,
                        forceToAxis: true
                    },
                });
            })();
        </script>
<?php
    }
}
