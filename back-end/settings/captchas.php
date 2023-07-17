<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('admin_menu', 'influactive_form_menu');

function influactive_form_menu(): void
{
    // Ajout de la sous-page
    add_submenu_page('edit.php?post_type=influactive-forms', __('Captchas', 'influactive-forms'), __('Captchas', 'influactive-forms'), 'manage_options', 'influactive-form-settings', 'influactive_form_settings_page');
}

function influactive_form_settings_page(): void
{
    $fields = get_option('influactive-forms-capcha-fields');
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

add_action('admin_init', 'influactive_forms_settings_init');

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

function influactive_forms_settings_section_callback(): void
{
    echo __('Enter the Google Captcha site key. Here the link to generate a key <a href="https://www.google.com/recaptcha/admin" target="_blank" title="reCAPTCHA">reCAPTCHA</a>', 'influactive-forms');
}

function influactive_forms_settings_field_callback_public(): void
{
    $options = get_option('influactive-forms-capcha-fields') ?? [];
    $public_site_key = $options['google-captcha']['public-site-key'] ?? '';

    echo '<input type="text" id="influactive-forms-capcha-fields-google-captcha-site-key-public" name="influactive-forms-capcha-fields[google-captcha][public-site-key]" value="' . $public_site_key . '">';
}

function influactive_forms_settings_field_callback_secret(): void
{
    $options = get_option('influactive-forms-capcha-fields') ?? [];
    $secret_site_key = $options['google-captcha']['secret-site-key'] ?? '';
    $type = $secret_site_key !== '' ? 'password' : 'text';

    echo '<input type="' . $type . '" id="influactive-forms-capcha-fields-google-captcha-site-key-secret" name="influactive-forms-capcha-fields[google-captcha][secret-site-key]" value="' . $secret_site_key . '">';
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'influactive_forms_add_settings_link');
