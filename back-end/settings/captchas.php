<?php

if (!defined('ABSPATH')) {
    throw new RuntimeException("WordPress environment not loaded. Exiting...");
}

/**
 * Adds a submenu page for the Influactive Forms plugin.
 *
 * This function uses the WordPress `add_submenu_page()` function to add a submenu page.
 * The submenu page is added under the "edit.php?post_type=influactive-forms" menu item and has the title "Captchas".
 * The required capability to access the submenu page is "manage_options".
 * The callback function to render the submenu page is "influactive_form_settings_page".
 *
 * @return void
 */
function influactive_form_menu(): void
{
    // Ajout de la sous-page
    add_submenu_page('edit.php?post_type=influactive-forms', __('Captchas', 'influactive-forms'), __('Captchas', 'influactive-forms'), 'manage_options', 'influactive-form-settings', 'influactive_form_settings_page');
}

add_action('admin_menu', 'influactive_form_menu');

/**
 * Renders the submenu page for the Influactive Forms plugin settings.
 *
 * This function retrieves the "influactive-forms-capcha-fields" option using the `get_option()` function.
 * It then outputs the HTML markup for the submenu page, including a heading, a form with settings fields,
 * and a submitted button.
 * The HTML markup is echoed directly to the browser.
 *
 * @return void
 */
function influactive_form_settings_page(): void
{
    if (!current_user_can('edit_posts')) {
        return;
    }
    ?>
    <div class="influactive-form-settings">
        <h1><?= __("Settings", "influactive-forms") ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields('influactive-forms-capcha-fields'); ?>
            <?php do_settings_sections('influactive-forms-capcha-fields'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Initializes the settings for the Influactive Forms plugin.
 *
 * This function is called by the WordPress hook `admin_init`.
 * It registers the `influactive-forms-capcha-fields` setting,
 * adds the settings section and fields for the Google Captcha,
 * and specifies the callback functions for rendering the settings section and fields.
 *
 * @return void
 */
function influactive_forms_settings_init(): void
{
    register_setting('influactive-forms-capcha-fields', 'influactive-forms-capcha-fields');

    add_settings_section(
            'influactive-forms-capcha-fields-section',
            __('Google Captcha', 'influactive-forms'),
            'influactive_forms_settings_section_callback',
            'influactive-forms-capcha-fields'
    );

    add_settings_field(
            'influactive-forms-capcha-fields-google-captcha-site-key-public',
            __('Public Site key', 'influactive-forms'),
            'influactive_forms_settings_field_callback_public',
            'influactive-forms-capcha-fields',
            'influactive-forms-capcha-fields-section'
    );

    add_settings_field(
            'influactive-forms-capcha-fields-google-captcha-site-key-secret',
            __('Secret Site key', 'influactive-forms'),
            'influactive_forms_settings_field_callback_secret',
            'influactive-forms-capcha-fields',
            'influactive-forms-capcha-fields-section'
    );
}

add_action('admin_init', 'influactive_forms_settings_init');

/**
 * Callback function for the "influactive-forms" settings section.
 *
 * This function outputs a description message for the Google Captcha site key settings field.
 * The description message includes a link to generate a key.
 * The HTML markup is echoed directly to the browser.
 *
 * @return void
 */
function influactive_forms_settings_section_callback(): void
{
    echo __('Enter the Google Captcha site key. Here is the link to generate a key <a href="https://www.google.com/recaptcha/admin" target="_blank" title="reCAPTCHA">reCAPTCHA</a>', 'influactive-forms');
}

/**
 * Callback function for the "influactive-forms" public settings field.
 *
 * This function outputs an input field for the Google Captcha public site key.
 * The value of the input field is retrieved from the "influactive-forms-capcha-fields" option.
 * If the option doesn't exist, an empty array is used by default.
 * The public site key is retrieved from the "google-captcha" sub-array.
 * If the sub-array doesn't exist or the public site key is not set, an empty string is used by default.
 * The HTML markup for the input field is echoed directly to the browser.
 *
 * @return void
 */
function influactive_forms_settings_field_callback_public(): void
{
    $options = get_option('influactive-forms-capcha-fields') ?? [];
    $public_site_key = $options['google-captcha']['public-site-key'] ?? '';

    echo '<input type="text" id="influactive-forms-capcha-fields-google-captcha-site-key-public" name="influactive-forms-capcha-fields[google-captcha][public-site-key]" value="' . $public_site_key . '">';
}

/**
 * Callback function for the "influactive-forms" settings field.
 *
 * This function outputs the input field for the Google Captcha secret site key settings.
 * The input field type is determined based on whether a secret site key is present in the options.
 * The secret site key is retrieved from the options using the 'influactive-forms-capcha-fields' key.
 * If the secret site key is found, the input field type is set to 'password', otherwise it is set to 'text'.
 * The input field is echoed directly to the browser.
 *
 * @return void
 */
function influactive_forms_settings_field_callback_secret(): void
{
    $options = get_option('influactive-forms-capcha-fields') ?? [];
    $secret_site_key = $options['google-captcha']['secret-site-key'] ?? '';
    $type = $secret_site_key !== '' ? 'password' : 'text';

    echo '<input type="' . $type . '" id="influactive-forms-capcha-fields-google-captcha-site-key-secret" name="influactive-forms-capcha-fields[google-captcha][secret-site-key]" value="' . $secret_site_key . '">';
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'influactive_forms_add_settings_link');
