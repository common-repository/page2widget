<?php

/*
Plugin Name: Page 2 Widget
Description: Provide a widget that can embed page. Go to Appearance -> Widgets and get "Page to widget"
Version: 1.0
Author: Dimitrov Adrian (dimitrov.adrian@gmail.com)
Tags: widget, sidebar, page
License: GPLv2 or later
*/

add_action('widgets_init', 'page2widget_widgets_init');

function page2widget_widgets_init() {
    register_widget('page2widget_Widget_Page');
}

class page2widget_Widget_Page extends WP_Widget {

    function page2widget_Widget_Page() {

        $widget_ops = array('classname' => '{page2widget-custom_class} page2widget', 'description' => __('Embed page to widget.'));
        $this->WP_Widget('widget-widget2page', __('Page to widget'), $widget_ops);
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'override_title' => FALSE, 'custom_class' => 'widget', 'post_id' => 0));

        $title = $instance['title'];
        $override_title = $instance['override_title'];
        $custom_class = $instance['custom_class'];
        $post_id = $instance['post_id'];

        echo '
            <p>
                <label for="'.$this->get_field_id('title').'">
                    '.__('Title').':
                    <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" />
                </label>
            </p>
            <p>
                <input class="checkbox" id="'.$this->get_field_id('override_title').'" name="'.$this->get_field_name('override_title').'" type="checkbox" value="1" '.checked(1, $override_title, FALSE).'" />
                <label for="'.$this->get_field_id('override_title').'">
                    '.__('Override title').'
                </label>
            </p>
            <p>
                <label for="'.$this->get_field_id('post_id').'">
                    '.__('Page').': ';
        wp_dropdown_pages(array(
            'post_type'        => 'page',
            'exclude_tree'     => NULL,
            'selected'         => $post_id,
            'name'             => $this->get_field_name('post_id'),
            'id'               => $this->get_field_id('post_id'),
            'show_option_none' => __('&mdash; Select &mdash;'),
            'sort_column'      => 'menu_order, post_title',
            'echo'             => 1));
        echo '
                </label>
            </p>
            <p>
                <label for="'.$this->get_field_id('custom_class').'">
                    '.__('CSS Class').':
                    <input class="widefat" id="'.$this->get_field_id('custom_class').'" name="'.$this->get_field_name('custom_class').'" type="text" value="'.esc_attr($custom_class).'" />
                </label>
            </p>';
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['override_title'] = $new_instance['override_title'];
        $instance['custom_class'] = $new_instance['custom_class'];
        $instance['post_id'] = $new_instance['post_id'];
        return $instance;
    }

    function widget($args, $instance) {
        extract($args, EXTR_SKIP);

        $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
        $override_title = empty($instance['override_title']) ? 0 : $instance['override_title'];
        $custom_class = empty($instance['custom_class']) ? '' : $instance['custom_class'];
        $post_id = empty($instance['post_id']) ? 0 : $instance['post_id'];

        if ($post_id && ( $page = wp_get_single_post( $post_id ))) {

            if (! $override_title) {
                $title = $page->post_title;
            }

            echo str_replace('{page2widget-custom_class}', trim($custom_class), $before_widget);

            if (!empty($title)) {
                echo $before_title . $title . $after_title;
            }

            $content = apply_filters('the_content', $page->post_content);
            $content = str_replace(']]>', ']]&gt;', $page->post_content);
            echo $content;

            echo $after_widget;
        }
    }
}
