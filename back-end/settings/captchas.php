<?php
/**
 * Description: This file is responsible for the settings of Influactive Forms
 *
 * @throws RuntimeException If the WordPress environment is not loaded.
 * @package Influactive Forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

/**
 * Adds a submenu page for the Influactive Forms plugin.
 *
 * This function uses the WordPress `add_submenu_page()` function to add a
 * submenu page. The submenu page is added under the
 * "edit.php?post_type=influactive-forms" menu item and has the title
 * "Captchas". The required capability to access the submenu page is
 * "manage_options". The callback function to render the submenu page is
 * "influactive_form_settings_page".
 *
 * @return void
 */
function influactive_form_menu(): void {
	add_submenu_page(
		'edit.php?post_type=influactive-forms',
		__( 'Captchas', 'influactive-forms' ),
		__( 'Captchas', 'influactive-forms' ),
		'manage_options',
		'influactive-form-settings',
		'influactive_form_settings_page'
	);
}

add_action( 'admin_menu', 'influactive_form_menu' );

/**
 * Renders the submenu page for the Influactive Forms plugin settings.
 *
 * This function retrieves the "influactive-forms-captcha-fields" option using
 * the `get_option()` function. It then outputs the HTML markup for the submenu
 * page, including a heading, a form with settings fields, and a submitted
 * button. The HTML markup is echoed directly to the browser.
 *
 * @return void
 */
function influactive_form_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	?>
	<div class="influactive-form-settings">
		<h1><?php echo esc_html__( 'Settings', 'influactive-forms' ); ?></h1>
		<form action="options.php" method="post">
			<?php settings_fields( 'influactive-forms-captcha-fields' ); ?>
			<?php do_settings_sections( 'influactive-forms-captcha-fields' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Initializes the settings for the Influactive Forms plugin.
 *
 * This function is called by the WordPress hook `admin_init`.
 * It registers the `influactive-forms-captcha-fields` setting,
 * adds the settings section and fields for the Google Captcha,
 * and specifies the callback functions for rendering the settings section and
 * fields.
 *
 * @return void
 */
function influactive_forms_settings_init(): void {
	register_setting( 'influactive-forms-captcha-fields', 'influactive-forms-captcha-fields' );

	add_settings_section(
		'influactive-forms-captcha-fields-section',
		__( 'Google Captcha', 'influactive-forms' ),
		'influactive_forms_settings_section_callback',
		'influactive-forms-captcha-fields'
	);

	add_settings_field(
		'influactive-forms-captcha-fields-google-captcha-site-key-public',
		__( 'Public Site key', 'influactive-forms' ),
		'influactive_forms_settings_field_callback_public',
		'influactive-forms-captcha-fields',
		'influactive-forms-captcha-fields-section'
	);

	add_settings_field(
		'influactive-forms-captcha-fields-google-captcha-site-key-secret',
		__( 'Secret Site key', 'influactive-forms' ),
		'influactive_forms_settings_field_callback_secret',
		'influactive-forms-captcha-fields',
		'influactive-forms-captcha-fields-section'
	);
}

add_action( 'admin_init', 'influactive_forms_settings_init' );

/**
 * Callback function for the "influactive-forms" settings section.
 *
 * This function outputs a description message for the Google Captcha site key
 * settings field. The description message includes a link to generate a key.
 * The HTML markup is echoed directly to the browser.
 *
 * @return void
 */
function influactive_forms_settings_section_callback(): void {
	$recaptcha_url  = 'https://www.google.com/recaptcha/admin';
	$recaptcha_link = sprintf(
		'<a href="%s" target="_blank" title="%s">%s</a>',
		esc_url( $recaptcha_url ),
		esc_attr__( 'reCAPTCHA', 'influactive-forms' ),
		esc_html__( 'reCAPTCHA', 'influactive-forms' )
	);

	/* translators: %s is a placeholder for a link to Google CAPTCHA admin */
	printf(
		wp_kses(
			__( 'Enter the Google Captcha site key. Here is the link to generate a key: %s', 'influactive-forms' ),
			array(
				'a' => array(
					'href'   => array(),
					'target' => array(),
					'title'  => array(),
				),
			)
		),
		$recaptcha_link
	);
}

/**
 * Callback function for the "influactive-forms" public settings field.
 *
 * This function outputs an input field for the Google Captcha public site key.
 * The value of the input field is retrieved from the
 * "influactive-forms-captcha-fields" option. If the option doesn't exist, an
 * empty array is used by default. The public site key is retrieved from the
 * "google-captcha" sub-array. If the sub-array doesn't exist or the public
 * site key is not set, an empty string is used by default. The HTML markup for
 * the input field is echoed directly to the browser.
 *
 * @return void
 */
function influactive_forms_settings_field_callback_public(): void {
	$options         = get_option( 'influactive-forms-captcha-fields' ) ?? array();
	$public_site_key = $options['google-captcha']['public-site-key'] ?? '';

	echo '<input type="text"
    id="influactive-forms-captcha-fields-google-captcha-site-key-public"
    name="influactive-forms-captcha-fields[google-captcha][public-site-key]"
    value="' . esc_attr( $public_site_key ) . '">';
}

/**
 * Callback function for the "influactive-forms" settings field.
 *
 * This function outputs the input field for the Google Captcha secret site key
 * settings. The input field type is determined based on whether a secret site
 * key is present in the options. The secret site key is retrieved from the
 * options using the 'influactive-forms-captcha-fields' key. If the secret site
 * key is found, the input field type is set to 'password', otherwise it is set
 * to 'text'. The input field is echoed directly to the browser.
 *
 * @return void
 */
function influactive_forms_settings_field_callback_secret(): void {
	$options         = get_option( 'influactive-forms-captcha-fields' ) ?? array();
	$secret_site_key = $options['google-captcha']['secret-site-key'] ?? '';
	$type            = '' !== $secret_site_key ? 'password' : 'text';

	echo '<input type="' . esc_attr( $type ) . '"
    id="influactive-forms-captcha-fields-google-captcha-site-key-secret"
    name="influactive-forms-captcha-fields[google-captcha][secret-site-key]"
    value="' . esc_attr( $secret_site_key ) . '">';
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'influactive_forms_add_settings_link' );
