<?php
/**
 * Plugin Name: Forms by Influactive
 * Description: A plugin to create custom forms and display them anywhere on your website.
 * Version: 1.5.1
 * Author: Influactive
 * Author URI: https://influactive.com
 * Text Domain: influactive-forms
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @throws RuntimeException If the WordPress environment is not loaded.
 * @package Forms by Influactive
 **/

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

require_once plugin_dir_path( __FILE__ ) . 'back-end/post-type/definitions.php';
require_once plugin_dir_path( __FILE__ ) . 'back-end/post-type/edit.php';
require_once plugin_dir_path( __FILE__ ) . 'back-end/post-type/listing.php';
require_once plugin_dir_path( __FILE__ ) . 'back-end/settings/captchas.php';
require_once plugin_dir_path( __FILE__ ) . 'front-end/shortcode.php';

/**
 * Adds a settings link to the plugin page.
 *
 * @param array $links An array of existing links on the plugin page.
 *
 * @return array An updated array of links including the new settings link.
 */
function influactive_forms_add_settings_link( array $links ): array {
	$link          = 'edit.php?post_type=influactive-forms&page=influactive-form-settings';
	$link_text     = __( 'Captchas', 'influactive-forms' );
	$settings_link = '<a href="' . $link . '">' . $link_text . '</a>';
	$links[]       = $settings_link;

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'influactive_forms_add_settings_link' );

/**
 * Enqueues scripts and styles for editing an Influactive form.
 *
 * @param string $hook The current admin page hook.
 *
 * @return void
 */
function influactive_form_edit( string $hook ): void {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	wp_enqueue_script(
		'influactive-form',
		plugin_dir_url( __FILE__ ) . 'dist/backEndForm.bundled.js',
		array(
			'wp-tinymce',
			'influactive-tabs',
			'influactive-form-layout',
		),
		'1.5.1',
		true
	);
	wp_localize_script(
		'influactive-form',
		'influactiveFormsTranslations',
		array(
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
		)
	);
	// Global CSS.
	wp_enqueue_style(
		'influactive-form-style',
		plugin_dir_url( __FILE__ ) . 'dist/style.bundled.css',
		array(),
		'1.5.1'
	);
	// Form CSS and JS.
	wp_enqueue_script(
		'influactive-form',
		plugin_dir_url( __FILE__ )
		. 'dist/backEndForm.bundled.js',
		array(),
		'1.5.1',
		true
	);
	wp_enqueue_style(
		'influactive-form',
		plugin_dir_url( __FILE__ )
		. 'dist/backForm.bundled.css',
		array(),
		'1.5.1'
	);
	// Tabs CSS and JS.
	wp_enqueue_script(
		'influactive-tabs',
		plugin_dir_url( __FILE__ )
		. 'dist/backEndTab.bundled.js',
		array(),
		'1.5.1',
		true
	);
	wp_enqueue_style(
		'influactive-tabs',
		plugin_dir_url( __FILE__ )
		. 'dist/tab.bundled.css',
		array(),
		'1.5.1'
	);
	// Layout CSS and JS.
	wp_enqueue_style(
		'influactive-form-layout',
		plugin_dir_url( __FILE__ )
		. 'dist/layout.bundled.css',
		array(),
		'1.5.1'
	);
	wp_enqueue_script(
		'influactive-form-layout',
		plugin_dir_url( __FILE__ ) . 'dist/backEndLayout.bundled.js',
		array(),
		'1.5.1',
		true
	);
	wp_localize_script(
		'influactive-form-layout',
		'influactiveFormsTranslations',
		array(
			'delete_layout' => __( 'Delete layout', 'influactive-forms' ),
		)
	);
}

add_action( 'admin_enqueue_scripts', 'influactive_form_edit' );

/**
 * Enqueues the necessary scripts and styles for the Influactive form shortcode.
 *
 * @return void
 */
function influactive_form_shortcode_enqueue(): void {
	if ( is_admin() ) {
		return;
	}

	$options_captcha = get_option( 'influactive-forms-captcha-fields' ) ?? array();
	$public_site_key = $options_captcha['google-captcha']['public-site-key'] ?? null;
	$secret_site_key = $options_captcha['google-captcha']['secret-site-key'] ?? null;

	if ( ! empty( $public_site_key ) && ! empty( $secret_site_key ) ) {
		wp_enqueue_script(
			'google-captcha',
			"https://www.google.com/recaptcha/api.js?render=$public_site_key",
			array(),
			'1.5.1',
			true
		);
		$script_handle = array( 'google-captcha' );
	} else {
		$script_handle = array();
	}

	// Form CSS and JS.
	wp_enqueue_script(
		'influactive-form',
		plugin_dir_url( __FILE__ ) .
		'dist/frontEnd.bundled.js',
		$script_handle,
		'1.5.1',
		true
	);
	wp_enqueue_style(
		'influactive-form',
		plugin_dir_url( __FILE__ ) . 'dist/frontForm.bundled.css',
		array(),
		'1.5.1'
	);
	wp_localize_script(
		'influactive-form',
		'ajaxObject',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}

add_action( 'wp_enqueue_scripts', 'influactive_form_shortcode_enqueue' );

/**
 * Loads the Forms by Influactive text domain for localization.
 *
 * @return void
 */
function influactive_load_forms_textdomain(): void {
	load_plugin_textdomain(
		'influactive-forms',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}

add_action( 'plugins_loaded', 'influactive_load_forms_textdomain' );
