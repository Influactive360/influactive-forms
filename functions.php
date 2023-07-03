<?php
/**
 * Plugin Name: Forms everywhere by Influactive
 * Description: Un plugin pour créer des formulaires personnalisés
 * Version: 1.0
 * Author: Influactive
 * Author URI: https://influactive.com
 * Text Domain: influactive-forms
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www. gnu.org/licenses/gpl-2.0.html
 **/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


add_action('admin_menu', 'influactive_form_menu');

function influactive_form_menu(): void
{
    // Ajout de la sous-page
    add_submenu_page('edit.php?post_type=influactive-forms', 'Settings', 'Settings', 'manage_options', 'influactive-form-settings', 'influactive_form_settings_page');
}

function influactive_form_settings_page(): void
{
    echo '<div class="wrap">';
    echo '<h1>Settings</h1>';
    echo '</div>';
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'influactive_forms_add_settings_link');

function influactive_forms_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=influactive-form-settings">' . __('Settings') . '</a>';
    $links[] = $settings_link;
    return $links;
}


// Add custom post type form_influactive
add_action('init', 'influactive_form_custom_post_type');

function influactive_form_custom_post_type(): void
{
    $labels = array(
        'name' => _x('Forms', 'Post Type General Name', 'influactive-forms'),
        'singular_name' => _x('Form', 'Post Type Singular Name', 'influactive-forms'),
        'menu_name' => __('Forms', 'influactive-forms'),
        'name_admin_bar' => __('Form', 'influactive-forms'),
        'archives' => __('Form Archives', 'influactive-forms'),
        'attributes' => __('Form Attributes', 'influactive-forms'),
        'parent_item_colon' => __('Parent Form:', 'influactive-forms'),
        'all_items' => __('All Forms', 'influactive-forms'),
        'add_new_item' => __('Add New Form', 'influactive-forms'),
        'add_new' => __('Add New', 'influactive-forms'),
        'new_item' => __('New Form', 'influactive-forms'),
        'edit_item' => __('Edit Form', 'influactive-forms'),
        'update_item' => __('Update Form', 'influactive-forms'),
        'view_item' => __('View Form', 'influactive-forms'),
        'view_items' => __('View Forms', 'influactive-forms'),
        'search_items' => __('Search Form', 'influactive-forms'),
        'not_found' => __('Not found', 'influactive-forms'),
        'not_found_in_trash' => __('Not found in Trash', 'influactive-forms'),
        'featured_image' => __('Featured Image', 'influactive-forms'),
        'set_featured_image' => __('Set featured image', 'influactive-forms'),
        'remove_featured_image' => __('Remove featured image', 'influactive-forms'),
        'use_featured_image' => __('Use as featured image', 'influactive-forms'),
        'insert_into_item' => __('Insert into Form', 'influactive-forms'),
        'uploaded_to_this_item' => __('Uploaded to this Form', 'influactive-forms'),
        'items_list' => __('Forms list', 'influactive-forms'),
        'items_list_navigation' => __('Forms list navigation', 'influactive-forms'),
        'filter_items_list' => __('Filter Forms list', 'influactive-forms'),
    );
    $args = array(
        'label' => __('Form', 'influactive-forms'),
        'description' => __('Custom post type for forms', 'influactive-forms'),
        'labels' => $labels,
        'supports' => array('title', 'revisions', 'custom-fields'),
        'taxonomies' => array(),
        'hierarchical' => false,
        'public' => true,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-format-aside',
        'show_in_rest' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'influactive-form'),
        'can_export' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );
    register_post_type('influactive-forms', $args);
}

// Ajouter un shortcode pour chaque post
add_shortcode('influactive_form', 'influactive_form_shortcode_handler');
function influactive_form_shortcode_handler($atts): string
{
    $atts = shortcode_atts(
        array('id' => '0'),
        $atts,
        'influactive_form'
    );

    $form_id = (int)$atts['id'];

    if (!$form_id) {
        return '';
    }

    // Do something with $form_id to display the form
    return "Form Output for ID {$form_id}";
}

// Ajouter le métabox pour afficher le shortcode
add_action('add_meta_boxes', 'influactive_form_add_meta_boxes');
function influactive_form_add_meta_boxes(): void
{
    add_meta_box('influactive_form_shortcode', 'Shortcode', 'influactive_form_shortcode_metabox', 'influactive-forms');
}

function influactive_form_shortcode_metabox($post): void
{
    echo '[influactive_form id="' . $post->ID . '"]';
}

// Ajouter la colonne dans la page de la liste des posts
add_filter('manage_influactive-forms_posts_columns', 'influactive_form_posts_columns');
function influactive_form_posts_columns($columns): array
{
    $columns['shortcode'] = 'Shortcode';

    return $columns;
}

add_action('manage_influactive-forms_posts_custom_column', 'influactive_form_posts_custom_column', 10, 2);
function influactive_form_posts_custom_column($column, $post_id): void
{
    if ($column === 'shortcode') {
        echo '[influactive_form id="' . $post_id . '"]';
    }
}
