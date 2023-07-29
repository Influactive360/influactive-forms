<?php
/**
 * Description: This file contains the definitions for the Influactive Form
 * post-type.
 *
 * @throws RuntimeException If the WordPress environment is not loaded.
 * @package Influactive Forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

/**
 * Register a custom post type for forms.
 *
 * @return void
 */
function influactive_form_custom_post_type(): void {
	$labels = [
		'name'                  => _x( 'Forms', 'Post Type General Name', 'influactive-forms' ),
		'singular_name'         => _x( 'Form', 'Post Type Singular Name', 'influactive-forms' ),
		'menu_name'             => __( 'Forms', 'influactive-forms' ),
		'name_admin_bar'        => __( 'Form', 'influactive-forms' ),
		'archives'              => __( 'Form Archives', 'influactive-forms' ),
		'attributes'            => __( 'Form Attributes', 'influactive-forms' ),
		'parent_item_colon'     => __( 'Parent Form:', 'influactive-forms' ),
		'all_items'             => __( 'All Forms', 'influactive-forms' ),
		'add_new_item'          => __( 'Add New Form', 'influactive-forms' ),
		'add_new'               => __( 'Add New', 'influactive-forms' ),
		'new_item'              => __( 'New Form', 'influactive-forms' ),
		'edit_item'             => __( 'Edit Form', 'influactive-forms' ),
		'update_item'           => __( 'Update Form', 'influactive-forms' ),
		'view_item'             => __( 'View Form', 'influactive-forms' ),
		'view_items'            => __( 'View Forms', 'influactive-forms' ),
		'search_items'          => __( 'Search Form', 'influactive-forms' ),
		'not_found'             => __( 'Not found', 'influactive-forms' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'influactive-forms' ),
		'featured_image'        => __( 'Featured Image', 'influactive-forms' ),
		'set_featured_image'    => __( 'Set featured image', 'influactive-forms' ),
		'remove_featured_image' => __( 'Remove featured image', 'influactive-forms' ),
		'use_featured_image'    => __( 'Use as featured image', 'influactive-forms' ),
		'insert_into_item'      => __( 'Inserts into Form', 'influactive-forms' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Form', 'influactive-forms' ),
		'items_list'            => __( 'Forms list', 'influactive-forms' ),
		'items_list_navigation' => __( 'Forms list navigation', 'influactive-forms' ),
		'filter_items_list'     => __( 'Filter Forms list', 'influactive-forms' ),
	];
	$args   = [
		'label'               => __( 'Form', 'influactive-forms' ),
		'description'         => __( 'Custom post type for forms', 'influactive-forms' ),
		'labels'              => $labels,
		'supports'            => [ 'title', 'revisions', 'custom-fields' ],
		'taxonomies'          => [],
		'hierarchical'        => FALSE,
		'public'              => FALSE,
		'show_ui'             => TRUE,
		'show_in_menu'        => TRUE,
		'show_in_admin_bar'   => FALSE,
		'show_in_nav_menus'   => FALSE,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-email',
		'show_in_rest'        => TRUE,
		'has_archive'         => FALSE,
		'rewrite'             => FALSE,
		'can_export'          => TRUE,
		'exclude_from_search' => FALSE,
		'publicly_queryable'  => TRUE,
		'capability_type'     => 'post',
	];
	register_post_type( 'influactive-forms', $args );
}

add_action( 'init', 'influactive_form_custom_post_type' );
