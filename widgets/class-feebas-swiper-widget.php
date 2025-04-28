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

class Feebas_Swiper_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'feebas_swiper';
    }

    public function get_title()
    {
        return __('Swiper', 'feebas-elementor-widgets');
    }

    public function get_icon()
    {
        return 'eicon-post-slider';
    }

    public function get_categories()
    {
        return ['basic'];
    }

    protected function _register_controls()
    {
        // Query section
        $this->start_controls_section(
            'section_items',
            [
                'label' => __('Items', 'feebas-elementor-widgets'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $default_type = 'camera';
        // Prepare initial selected_posts options based on default post type
        $post_options = array();
        if ($default_type) {
            $initial_posts = get_posts(array(
                'post_type'      => $default_type,
                'posts_per_page' => -1,
            ));
            foreach ($initial_posts as $p) {
                $post_options[$p->ID] = get_the_title($p);
            }
        }

        $this->add_control(
            'selected_cameras',
            [
                'label'       => __('Select Items', 'feebas-elementor-widgets'),
                'type'        => Controls_Manager::SELECT2,
                'options'     => $post_options,
                'multiple'    => true,
                'label_block' => true,
                'description' => __('Choose specific Camera to display.', 'feebas-elementor-widgets'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $selected_post_ids = $settings['selected_cameras'];
        $post_type = 'camera'; // Get the selected post type
        $widget_id = $this->get_id();

        $posts = []; // Initialize $posts as an empty array

        // Ensure we have an array of IDs and a post type before querying
        if (! empty($selected_post_ids) && is_array($selected_post_ids) && ! empty($post_type)) {

            $posts = get_posts(
                [
                    'post__in'        => $selected_post_ids,
                    'post_type'       => $post_type, // *** Add the selected post type ***
                    'orderby'         => 'post__in',
                    'posts_per_page'  => count($selected_post_ids), // Fetch exactly the selected count
                    // 'posts_per_page'  => -1, // Alternative: Fetch all matching selected posts
                    'post_status'     => 'publish',     // Ensure only published posts are shown
                    'ignore_sticky_posts' => 1 // Typically needed for custom queries
                ]
            );
        } else {
            if (! empty($selected_post_ids) && ! is_array($selected_post_ids)) {
                error_log('Feebas Horizontal Cards: selected_posts setting is not an array. Value: ' . print_r($selected_post_ids, true));
            }
        }

        // Check if the query returned any posts
        if (empty($posts)) {
            // You might want different messages depending on why $posts is empty
            if (empty($selected_post_ids)) {
                echo '<p>' . esc_html__('No posts selected to display.', 'feebas-elementor-widgets') . '</p>';
            } else {
                // Posts were selected, but the query didn't return them (e.g., wrong type saved, posts deleted/unpublished)
                echo '<p>' . esc_html__('Selected posts could not be found or displayed.', 'feebas-elementor-widgets') . '</p>';
            }
            return; // Exit rendering if no posts
        }

?>
        <div class="swiper swiper-<?php echo $widget_id; ?>">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <!-- Slides -->
                <?php foreach ($posts as $post): ?>
                    <div class="swiper-slide">
                        <div class="flex bg-black/90 space-x-5 text-white p-5 rounded ">
                            <div class="rounded">
                                <img class="w-[300px]" src="https://placehold.co/600x400">
                            </div>
                            <div class="flex flex-col">
                                <h3 class="font-semibold text-4xl"><?php echo esc_html(get_the_title($post)); ?></h3>
                                <p class="mb-auto mt-5">description</p>
                                <button class="px-3 py-2 rounded bg-orange-400 text-white">book now</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
        <script>
            const swiper<?php echo $widget_id; ?> = new Swiper('.swiper-<?php echo $widget_id; ?>', {
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
                // Navigation arrows
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },

            });
        </script>

<?php
    }
    /**
     * Specify frontend style dependencies.
     *
     * @return string[]
     */
    public function get_style_depends()
    {
        return ['feebas-tailwind-css'];
    }
}
