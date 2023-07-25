<?php
/**
 * Plugin Name: Forms everywhere by Influactive
 * Description: A plugin to create custom forms and display them anywhere on your website.
 * Version: 1.1
 * Author: Influactive
 * Author URI: https://influactive.com
 * Text Domain: influactive-forms
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 **/

if (!defined('ABSPATH')) {
    throw new RuntimeException("WordPress environment not loaded. Exiting...");
}

include(plugin_dir_path(__FILE__) . 'back-end/post-type/definitions.php');
include(plugin_dir_path(__FILE__) . 'back-end/post-type/listing.php');
include(plugin_dir_path(__FILE__) . 'back-end/post-type/edit.php');
include(plugin_dir_path(__FILE__) . 'back-end/settings/captchas.php');
include(plugin_dir_path(__FILE__) . 'front-end/shortcode.php');

/**
 * Adds a settings link to the plugin page.
 *
 * @param array $links An array of existing links on the plugin page.
 *
 * @return array An updated array of links including the new settings link.
 */
function influactive_forms_add_settings_link(array $links): array
{
    $settings_link = '<a href="edit.php?post_type=influactive-forms&page=influactive-form-settings">' . __('Captchas', 'influactive-forms') . '</a>';
    $links[] = $settings_link;

    return $links; // Add the settings link to the plugin page.
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'influactive_forms_add_settings_link');

/**
 * Enqueues scripts and styles for editing an Influactive form.
 *
 * @param string $hook The current admin page hook.
 *
 * @return void
 */
function influactive_form_edit(string $hook): void
{
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
        return;
    }

    wp_enqueue_script('influactive-form', plugin_dir_url(__FILE__) . 'back-end/post-type/form/form.min.js', array(
        'influactive-form-sortable',
        'wp-tinymce',
        'influactive-tabs',
        'influactive-form-layout'
    ), '1.1', true);
    wp_localize_script('influactive-form', 'influactiveFormsTranslations', array(
        'addOptionText' => __('Add option', 'influactive-forms'),
        'removeOptionText' => __('Remove option', 'influactive-forms'),
        'removeFieldText' => __('Remove the field', 'influactive-forms'),
        'typeLabelText' => __('Type', 'influactive-forms'),
        'labelLabelText' => __('Label', 'influactive-forms'),
        'nameLabelText' => __('Name', 'influactive-forms'),
        'optionLabelLabelText' => __('Option Label', 'influactive-forms'),
        'optionValueLabelText' => __('Option Value', 'influactive-forms'),
        'gdprTextLabelText' => __('Text', 'influactive-forms'),
        'fieldAddedText' => __('Field added!', 'influactive-forms'),
        'optionAddedText' => __('Option added!', 'influactive-forms'),
        'optionRemovedText' => __('Option removed!', 'influactive-forms'),
        'Text' => __('Text', 'influactive-forms'),
        'Textarea' => __('Textarea', 'influactive-forms'),
        'Select' => __('Select', 'influactive-forms'),
        'Email' => __('Email', 'influactive-forms'),
        'GDPR' => __('GDPR', 'influactive-forms'),
        'Number' => __('Number', 'influactive-forms'),
        'Freetext' => __('Free text', 'influactive-forms'),
    ));
    wp_enqueue_script('influactive-form-sortable', 'https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js', array(), '1.1', true);
    wp_enqueue_style('influactive-form', plugin_dir_url(__FILE__) . 'back-end/post-type/form/form.min.css', array(), '1.1');

    wp_enqueue_script('influactive-tabs', plugin_dir_url(__FILE__) . 'back-end/post-type/tab/tab.min.js', array(), '1.1', true);
    wp_enqueue_style('influactive-tabs', plugin_dir_url(__FILE__) . 'back-end/post-type/tab/tab.min.css', array(), '1.1');

    wp_enqueue_style('influactive-form-layout', plugin_dir_url(__FILE__) . 'back-end/post-type/layout/layout.min.css', array(), '1.1');
    wp_enqueue_script('influactive-form-layout', plugin_dir_url(__FILE__) . 'back-end/post-type/layout/layout.min.js', array(), '1.1', true);
    wp_localize_script('influactive-form-layout', 'influactiveFormsTranslations', array(
        'delete_layout' => __('Delete layout', 'influactive-forms'),
    ));

    wp_enqueue_style('influactive-form-style', plugin_dir_url(__FILE__) . 'back-end/post-type/style.min.css', array(), '1.1');

    wp_enqueue_style('influactive-form-preview', plugin_dir_url(__FILE__) . 'front-end/form.min.css', array(), '1.1');

    $form_id = get_post_meta(get_the_ID(), 'influactive_form_id', true);
    if (!$form_id) {
        return;
    }
    wp_enqueue_style('influactive-form-dynamic-style', plugin_dir_url(__FILE__) . 'front-end/dynamic-style.php?post_id=' . $form_id, array(), '1.1');
}

add_action('admin_enqueue_scripts', 'influactive_form_edit');


/**
 * Enqueues the necessary scripts and styles for the Influactive form shortcode.
 *
 * @return void
 */
function influactive_form_shortcode_enqueue(): void
{
    if (is_admin()) {
        return;
    }

    if (wp_script_is('google-captcha') || wp_script_is('google-recaptcha')) {
        return;
    }

    $options_captcha = get_option('influactive-forms-capcha-fields') ?? [];
    $public_site_key = $options_captcha['google-captcha']['public-site-key'] ?? null;
    $secret_site_key = $options_captcha['google-captcha']['secret-site-key'] ?? null;

    if (!empty($public_site_key) && !empty($secret_site_key)) {
        wp_enqueue_script('google-captcha', "https://www.google.com/recaptcha/api.js?render=$public_site_key", [], null, true);
        $script_handle = ['google-captcha'];
    } else {
        $script_handle = [];
    }

    wp_enqueue_script('influactive-form', plugin_dir_url(__FILE__) . 'front-end/form.min.js', $script_handle, '1.1', true);
    wp_enqueue_style('influactive-form', plugin_dir_url(__FILE__) . 'front-end/form.min.css', [], '1.1');

    wp_localize_script('influactive-form', 'ajax_object', ['ajaxurl' => admin_url('admin-ajax.php')]);
}

add_action('wp_enqueue_scripts', 'influactive_form_shortcode_enqueue');

/**
 * Loads the Influactive Forms text domain for localization.
 *
 * @return void
 */
function load_influactive_forms_textdomain(): void
{
    load_plugin_textdomain('influactive-forms', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('plugins_loaded', 'load_influactive_forms_textdomain');

/**
 * Requires the WordPress core file from the given possible paths.
 *
 * @param array $possiblePaths An array of possible paths where the WordPress core file may exist.
 *
 * @return void
 */
function requireWordPressCore(array $possiblePaths): void
{
    $basePath = $_SERVER['DOCUMENT_ROOT'] ?? '';
    foreach ($possiblePaths as $possiblePath) {
        $fullPath = $basePath . DIRECTORY_SEPARATOR . ltrim($possiblePath, '/');
        if (file_exists($fullPath)) {
            require_once($fullPath);
            break;
        }
    }
}
