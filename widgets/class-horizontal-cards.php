<?php

/**
 * Horizontal Cards Elementor Widget.
 *
 * @package Feebas_Elementor_Widgets
 */
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

class Feebas_Horizontal_Cards_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'feebas_horizontal_cards';
    }

    public function get_title()
    {
        return __('Horizontal Cards', 'feebas-elementor-widgets');
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
            'section_query',
            [
                'label' => __('Query', 'feebas-elementor-widgets'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Prepare post type options
        $post_types = get_post_types(array('public' => true), 'objects');
        $type_options = array();
        foreach ($post_types as $pt) {
            $type_options[$pt->name] = $pt->label;
        }
        $default_type = 'camera';
        $this->add_control(
            'post_type',
            [
                'label'   => __('Select Post Type', 'feebas-elementor-widgets'),
                'type'    => Controls_Manager::SELECT,
                'options' => $type_options,
                'default' => $default_type,
            ]
        );
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
            'selected_posts',
            [
                'label'       => __('Select Posts', 'feebas-elementor-widgets'),
                'type'        => Controls_Manager::SELECT2,
                'options'     => $post_options,
                'multiple'    => true,
                'label_block' => true,
                'description' => __('Choose specific posts to display. If none selected, posts of chosen post type will not be shown.', 'feebas-elementor-widgets'),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'additional_option',
            [
                'label' => __('Additional Options', 'feebas-elementor-widgets'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'post_type',
            [
                'label'   => __('Select Post Type', 'feebas-elementor-widgets'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
					'' => esc_html__( 'Default', 'feebas-elementor-widgets' ),
					'none' => esc_html__( 'None', 'feebas-elementor-widgets' ),
					'solid'  => esc_html__( 'Solid', 'feebas-elementor-widgets' ),
					'dashed' => esc_html__( 'Dashed', 'feebas-elementor-widgets' ),
					'dotted' => esc_html__( 'Dotted', 'feebas-elementor-widgets' ),
					'double' => esc_html__( 'Double', 'feebas-elementor-widgets' ),
				],
                'default' => ''
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $selected_post_ids = $settings['selected_posts'];
        $post_type = $settings['post_type']; // Get the selected post type

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
            // Log or handle cases where selection might be invalid (optional)
            if (empty($post_type)) {
                error_log('Feebas Horizontal Cards: Post Type setting is empty.');
            }
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

        <div class="horizontal-card-container flex flex space-x-4 overflow-x-auto scroll-smooth snap-x snap-mandatory px-4 hide-scrollbar">
            <?php foreach ($posts as $post): ?>
                <div class="horizontal-card min-w-[200px] sm:min-w-[250px] md:min-w-[300px] snap-start bg-white rounded-lg shadow p-4 flex-shrink-0">
                    <img src="https://placehold.co/600x400" class="min-w-[200px]" alt="Placeholder Image">
                    <h2><?php echo esc_html(get_the_title($post)); ?></h2>
                    <p>placeholder description</p>
                </div>
            <?php endforeach; ?>
        </div>

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
