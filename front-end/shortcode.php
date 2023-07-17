<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Ajouter un shortcode pour chaque post
add_shortcode('influactive_form', 'influactive_form_shortcode_handler');
function influactive_form_shortcode_handler($atts): bool|string
{
    ob_start(); // Start output buffering

    $atts = shortcode_atts(
        array('id' => '0'),
        $atts,
        'influactive_form'
    );

    $form_id = (int)$atts['id'];

    if (!$form_id) {
        return false;
    }

    // Showing the form if it exists
    $form = get_post($form_id);


    if ($form) {
        update_post_meta(get_the_ID(), 'influactive_form_id', $form_id);

        $fields = get_post_meta($form_id, '_influactive_form_fields', true);

        echo '<div class="influactive-form-wrapper">';

        echo '<form id="influactive-form-' . $form_id . '" class="influactive-form">';

        wp_nonce_field('influactive_send_email', 'nonce');

        echo '<input type="hidden" name="form_id" value="' . $form_id . '">';

        $options_captcha = get_option('influactive-forms-capcha-fields') ?? [];
        $public_site_key = $options_captcha['google-captcha']['public-site-key'] ?? '';
        $secret_site_key = $options_captcha['google-captcha']['secret-site-key'] ?? '';

        if (!empty($public_site_key) && !empty($secret_site_key)) {
            echo '<input type="hidden" id="recaptchaResponse-' . $form_id . '" name="recaptcha_response">';
            echo '<input type="hidden" id="recaptchaSiteKey-' . $form_id . '" name="recaptcha_site_key" value="' . $public_site_key . '">';
        }

        foreach ($fields as $field) {
            if (isset($field['required']) && $field['required'] === '1') {
                $required = 'required';
            } else {
                $required = '';
            }

            switch ($field['type']) {
                case 'text':
                    echo '<label>' . $field['label'] . ': <input type="text" ' . $required . ' name="' . esc_attr($field['name']) . '"></label>';
                    break;
                case 'email':
                    echo '<label>' . $field['label'] . ': <input type="email" ' . $required . ' name="' . esc_attr($field['name']) . '" autocomplete="email"></label>';
                    break;
                case 'number':
                    echo '<label>' . $field['label'] . ': <input type="number" ' . $required . ' name="' . esc_attr($field['name']) . '"></label>';
                    break;
                case 'textarea':
                    echo '<label>' . $field['label'] . ': <textarea ' . $required . ' name="' . esc_attr($field['name']) . '" rows="10"></textarea></label>';
                    break;
                case 'select':
                    echo '<label>' . $field['label'] . ': <select ' . $required . ' name="' . esc_attr($field['name']) . '">';
                    foreach ($field['options'] as $option) {
                        echo '<option value="' . esc_attr($option['value']) . ':' . esc_attr($option['label']) . '">' . esc_attr($option['label']) . '</option>';
                    }
                    echo '</select></label>';
                    break;
                case 'gdpr':
                    $pp = get_privacy_policy_url() ? '<a href="' . get_privacy_policy_url() . '" target="_blank" title="Privacy Policy">' . __('Check our Privacy Policy', 'influactive-forms') . '</a>' : '';
                    echo '<label><input type="checkbox" name="' . esc_attr($field['name']) . '" required> ' . $field['label'] . ' ' . $pp . '</label>';
                    break;
                case 'free_text':
                    echo '<div class="free-text">' . $field['label'] . '</div>';
                    echo '<input type="hidden" name="' . esc_attr($field['name']) . '" value="' . esc_attr($field['label']) . '">'; // Hidden field to get the label
                    break;
            }
        }

        echo '<input type="submit">';

        echo '<div class="influactive-form-message"></div>';
        echo '</form>';
        echo '</div>';
    }

    return ob_get_clean(); // End output buffering and return buffered output
}

add_action('wp_enqueue_scripts', 'enqueue_form_dynamic_style');
function enqueue_form_dynamic_style(): void
{
    if (is_admin()) {
        return;
    }

    $form_id = get_post_meta(get_the_ID(), 'influactive_form_id', true);
    if (!$form_id) {
        return;
    }

    // Enqueue du fichier dynamic-style.php
    wp_enqueue_style('influactive-form-dynamic-style', plugin_dir_url(__FILE__) . '/dynamic-style.php?post_id=' . $form_id, [], '1.0.0');
}


add_action('wp_ajax_send_email', 'influactive_send_email');
add_action('wp_ajax_nopriv_send_email', 'influactive_send_email');

/**
 * @throws JsonException
 */
function influactive_send_email(): void
{
    // Check if our nonce is set and verify it.
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'influactive_send_email')) {
        wp_send_json_error(['message' => __('Nonce verification failed', 'influactive-forms')]);
        return;
    }

    // Check form fields
    if (!isset($_POST['form_id'])) {
        wp_send_json_error(['message' => __('Form ID is required', 'influactive-forms')]);
        return;
    }

    // Get form fields
    $fields = get_post_meta($_POST['form_id'], '_influactive_form_fields', true);

    foreach ($fields as $field) {
        if (empty($_POST[$field['name']]) && $field['required'] === '1') {
            $name = $field['name'];
            $message = sprintf(__('The field %s is required', 'influactive-forms'), $name);
            wp_send_json_error(['message' => $message]);
            return;
        }
    }

    // Get email layout
    $email_layout = get_post_meta($_POST['form_id'], '_influactive_form_email_layout', true);
    $sitename = get_bloginfo('name');

    $options_captcha = get_option('influactive-forms-capcha-fields') ?? [];
    $secret_site_key = $options_captcha['google-captcha']['secret-site-key'] ?? '';
    $public_site_key = $_POST['recaptcha_site_key'] ?? '';

    if (!empty($secret_site_key) && !empty($public_site_key)) {
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_response = $_POST['recaptcha_response'];

        $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $secret_site_key . '&response=' . $recaptcha_response);
        $recaptcha = json_decode($recaptcha, false, 512, JSON_THROW_ON_ERROR);

        // Prendre une décision basée sur le score de reCAPTCHA.
        if ($recaptcha->score < 0.5) {
            // Not likely to be a human
            wp_send_json_error(['message' => __('Bot detected', 'influactive-forms')]);
            return;
        }
    }

    $layouts = $email_layout ?? [];
    $error = 0;
    foreach ($layouts as $layout) {
        $content = str_replace("\n", '<br>', $layout['content'] ?? '');
        $subject = $layout['subject'] ?? '';
        $to = $layout['recipient'] ?? get_bloginfo('admin_email');
        $from = $layout['sender'] ?? get_bloginfo('admin_email');

        foreach ($fields as $field) {
            // Convert textarea newlines to HTML breaks
            if ($field['type'] === 'textarea') {
                $_POST[$field['name']] = nl2br($_POST[$field['name']]);
                $allowed_html = array(
                    'br' => array()
                );
                $content = str_replace('{' . $field['name'] . '}', wp_kses($_POST[$field['name']], $allowed_html), $content);
                $subject = str_replace('{' . $field['name'] . '}', wp_kses($_POST[$field['name']], $allowed_html), $subject);
                $to = str_replace('{' . $field['name'] . '}', wp_kses($_POST[$field['name']], $allowed_html), $to);
                $from = str_replace('{' . $field['name'] . '}', wp_kses($_POST[$field['name']], $allowed_html), $from);
            } else if ($field['type'] === 'select') {
                $content = replace_field_placeholder($content, $field['name'], explode(':', $_POST[$field['name']]));
                $subject = replace_field_placeholder($subject, $field['name'], explode(':', $_POST[$field['name']]));
                $to = replace_field_placeholder($to, $field['name'], explode(':', $_POST[$field['name']]));
                $from = replace_field_placeholder($from, $field['name'], explode(':', $_POST[$field['name']]));
            } else if ($field['type'] === 'email') {
                $content = str_replace('{' . $field['name'] . '}', sanitize_email($_POST[$field['name']]), $content);
                $subject = str_replace('{' . $field['name'] . '}', sanitize_email($_POST[$field['name']]), $subject);
                $to = str_replace('{' . $field['name'] . '}', sanitize_email($_POST[$field['name']]), $to);
                $from = str_replace('{' . $field['name'] . '}', sanitize_email($_POST[$field['name']]), $from);
            } else {
                $content = str_replace('{' . $field['name'] . '}', sanitize_text_field($_POST[$field['name']]), $content);
                $subject = str_replace('{' . $field['name'] . '}', sanitize_text_field($_POST[$field['name']]), $subject);
                $to = str_replace('{' . $field['name'] . '}', sanitize_text_field($_POST[$field['name']]), $to);
                $from = str_replace('{' . $field['name'] . '}', sanitize_text_field($_POST[$field['name']]), $from);
            }
        }

        $from = sanitize_email($from);
        $to = sanitize_email($to);

        // Email details
        $headers = ['Content-Type: text/html; charset=UTF-8', 'From: ' . $sitename . ' <' . $from . '>', 'Reply-To: ' . $from];

        if (!wp_mail($to, $subject, $content, $headers)) {
            $error++;
        }
    }

    if ($error === 0) {
        wp_send_json_success([
            'message' => __('Email sent successfully', 'influactive-forms'),
        ]);
    } else {
        wp_send_json_error([
            'message' => __('Failed to send email', 'influactive-forms'),
        ]);
    }

    wp_die();
}

/**
 * Replace placeholders in email details based on selected value or label
 *
 * @param string $string The string where placeholders will be replaced
 * @param string $field_name The name of the field to replace
 * @param array $label_value An array containing the label and value
 *
 * @return string The string with replaced placeholders
 */
function replace_field_placeholder(string $string, string $field_name, array $label_value): string
{
    // Replace label placeholder if it exists
    if (str_contains($string, '{' . $field_name . ':label}')) {
        $string = str_replace('{' . $field_name . ':label}', $label_value[1], $string);
    }

    // Replace value placeholder if it exists
    if (str_contains($string, '{' . $field_name . ':value}')) {
        $string = str_replace('{' . $field_name . ':value}', $label_value[0], $string);
    }

    return $string;
}

add_action('wp_enqueue_scripts', 'enqueue_google_captcha_script');
function enqueue_google_captcha_script(): void
{
    $options_captcha = get_option('influactive-forms-capcha-fields') ?? [];
    $public_site_key = $options_captcha['google-captcha']['public-site-key'] ?? '';
    $secret_site_key = $options_captcha['google-captcha']['secret-site-key'] ?? '';

    if (is_admin()) {
        return;
    }

    if (wp_script_is('google-captcha')) {
        return;
    }

    if (!empty($public_site_key) && !empty($secret_site_key)) {
        wp_enqueue_script('google-captcha', "https://www.google.com/recaptcha/api.js?render=$public_site_key", [], null, true);
    }
}
