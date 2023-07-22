<?php
/**
 * Plugin Name: Forms everywhere by Influactive
 * Description: A plugin to create custom forms and display them anywhere on your website.
 * Version: 1.0
 * Author: Influactive
 * Author URI: https://influactive.com
 * Text Domain: influactive-forms
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

include( plugin_dir_path( __FILE__ ) . 'back-end/post-type/definitions.php' );
include( plugin_dir_path( __FILE__ ) . 'back-end/post-type/listing.php' );
include( plugin_dir_path( __FILE__ ) . 'back-end/post-type/edit.php' );
include( plugin_dir_path( __FILE__ ) . 'back-end/settings/captchas.php' );
include( plugin_dir_path( __FILE__ ) . 'front-end/shortcode.php' );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'influactive_forms_add_settings_link' );
function influactive_forms_add_settings_link( $links ): array {
	$settings_link = '<a href="edit.php?post_type=influactive-forms&page=influactive-form-settings">' . __( 'Captchas', 'influactive-forms' ) . '</a>';
	$links[]       = $settings_link;

	return $links;
}

add_action( 'admin_enqueue_scripts', 'influactive_form_edit' );
function influactive_form_edit( $hook ): void {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	wp_enqueue_script( 'influactive-form', plugin_dir_url( __FILE__ ) . 'back-end/post-type/form/form.min.js', array(
		'influactive-form-sortable',
		'wp-tinymce',
		'influactive-tabs',
		'influactive-form-layout'
	), '1.0', true );
	wp_localize_script( 'influactive-form', 'influactiveFormsTranslations', array(
		'addOptionText'        => __( 'Add option', 'influactive-forms' ),
		'removeOptionText'     => __( 'Remove option', 'influactive-forms' ),
		'removeFieldText'      => __( 'Remove the field', 'influactive-forms' ),
		'typeLabelText'        => __( 'Type', 'influactive-forms' ),
		'labelLabelText'       => __( 'Label', 'influactive-forms' ),
		'nameLabelText'        => __( 'Name', 'influactive-forms' ),
		'optionLabelLabelText' => __( 'Option Label', 'influactive-forms' ),
		'optionValueLabelText' => __( 'Option Value', 'influactive-forms' ),
		'gdprTextLabelText'    => __( 'Text', 'influactive-forms' ),
		'fieldAddedText'       => __( 'Field added!', 'influactive-forms' ),
		'optionAddedText'      => __( 'Option added!', 'influactive-forms' ),
		'optionRemovedText'    => __( 'Option removed!', 'influactive-forms' ),
		'Text'                 => __( 'Text', 'influactive-forms' ),
		'Textarea'             => __( 'Textarea', 'influactive-forms' ),
		'Select'               => __( 'Select', 'influactive-forms' ),
		'Email'                => __( 'Email', 'influactive-forms' ),
		'GDPR'                 => __( 'GDPR', 'influactive-forms' ),
		'Number'               => __( 'Number', 'influactive-forms' ),
		'Freetext'             => __( 'Free text', 'influactive-forms' ),
	) );
	wp_enqueue_script( 'influactive-form-sortable', 'https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js', array(), '1.0', true );
	wp_enqueue_style( 'influactive-form', plugin_dir_url( __FILE__ ) . 'back-end/post-type/form/form.min.css', array(), '1.0' );

	wp_enqueue_script( 'influactive-tabs', plugin_dir_url( __FILE__ ) . 'back-end/post-type/tab/tab.min.js', array(), '1.0', true );
	wp_enqueue_style( 'influactive-tabs', plugin_dir_url( __FILE__ ) . 'back-end/post-type/tab/tab.min.css', array(), '1.0' );

	wp_enqueue_style( 'influactive-form-layout', plugin_dir_url( __FILE__ ) . 'back-end/post-type/layout/layout.min.css', array(), '1.0' );
	wp_enqueue_script( 'influactive-form-layout', plugin_dir_url( __FILE__ ) . 'back-end/post-type/layout/layout.min.js', array(), '1.0', true );
	wp_localize_script( 'influactive-form-layout', 'influactiveFormsTranslations', array(
		'delete_layout' => __( 'Delete layout', 'influactive-forms' ),
	) );

	wp_enqueue_style( 'influactive-form-style', plugin_dir_url( __FILE__ ) . 'back-end/post-type/style.min.css', array(), '1.0' );

	wp_enqueue_style( 'influactive-form-preview', plugin_dir_url( __FILE__ ) . 'front-end/form.min.css', array(), '1.0' );

	$form_id = get_post_meta( get_the_ID(), 'influactive_form_id', true );
	if ( ! $form_id ) {
		return;
	}
	wp_enqueue_style( 'influactive-form-dynamic-style', plugin_dir_url( __FILE__ ) . 'front-end/dynamic-style.php?post_id=' . $form_id, [], '1.0.0' );
}

add_action( 'wp_enqueue_scripts', 'influactive_form_shortcode_enqueue' );
function influactive_form_shortcode_enqueue(): void {
	if ( is_admin() ) {
		return;
	}

	wp_enqueue_script( 'influactive-form', plugin_dir_url( __FILE__ ) . 'front-end/form.min.js', array( 'google-captcha' ), '1.0', true );
	wp_enqueue_style( 'influactive-form', plugin_dir_url( __FILE__ ) . 'front-end/form.min.css', array(), '1.0' );

	wp_localize_script( 'influactive-form', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'plugins_loaded', 'load_influactive_forms_textdomain' );
function load_influactive_forms_textdomain(): void {
	load_plugin_textdomain( 'influactive-forms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
