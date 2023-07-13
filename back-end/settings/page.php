<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('admin_menu', 'influactive_form_menu');

function influactive_form_menu(): void
{
    // Ajout de la sous-page
    add_submenu_page('edit.php?post_type=influactive-forms', __('Settings', 'influactive-forms'), __('Settings', 'influactive-forms'), 'manage_options', 'influactive-form-settings', 'influactive_form_settings_page');
}

function influactive_form_settings_page(): void
{
    echo '<div class="wrap">';
    echo '<h1>' . __("Settings", "influactive-forms") . '</h1>';
    echo '</div>';
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'influactive_forms_add_settings_link');

// TODO: Add captcha settings
