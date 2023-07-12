<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Ajouter un shortcode pour chaque post
add_shortcode('influactive_form', 'influactive_form_shortcode_handler');
function influactive_form_shortcode_handler($atts): void
{
    $atts = shortcode_atts(
        array('id' => '0'),
        $atts,
        'influactive_form'
    );

    $form_id = (int)$atts['id'];

    if (!$form_id) {
        return;
    }

    $GLOBALS['influactive_form_id'] = $form_id;

    // Showing the form if it exists
    $form = get_post($form_id);


    if ($form) {
        $fields = get_post_meta($form_id, '_influactive_form_fields', true);

        echo '<form id="influactive-form-' . $form_id . '" class="influactive-form">';

        wp_nonce_field('influactive_send_email', 'nonce');

        echo '<input type="hidden" name="form_id" value="' . $form_id . '">';

        foreach ($fields as $field) {
            switch ($field['type']) {
                case 'text':
                    echo '<label>' . $field['label'] . ': <input type="text" name="' . esc_attr($field['name']) . '"></label>';
                    break;
                case 'email':
                    echo '<label>' . $field['label'] . ': <input type="email" name="' . esc_attr($field['name']) . '" autocomplete="email"></label>';
                    break;
                case 'number':
                    echo '<label>' . $field['label'] . ': <input type="number" name="' . esc_attr($field['name']) . '"></label>';
                    break;
                case 'textarea':
                    echo '<label>' . $field['label'] . ': <textarea name="' . esc_attr($field['name']) . '" rows="10"></textarea></label>';
                    break;
                case 'select':
                    echo '<label>' . $field['label'] . ': <select name="' . esc_attr($field['name']) . '">';
                    foreach ($field['options'] as $option) {
                        echo '<option value="' . esc_attr($option['value']) . ':' . esc_attr($option['label']) . '">' . esc_attr($option['label']) . '</option>';
                    }
                    echo '</select></label>';
                    break;
                case 'gdpr':
                    $pp = get_privacy_policy_url() ? '<a href="' . get_privacy_policy_url() . '" target="_self" title="Privacy Policy">Check our Privacy Policy<a>' : '';
                    echo '<label><input type="checkbox" name="' . esc_attr($field['name']) . '"> ' . $field['label'] . ' ' . $pp . '</label>';
                    break;
            }
        }

        echo '<input type="submit">';


        echo '<div class="influactive-form-message"></div>';
        echo '</form>';

    }
}

add_action('wp_enqueue_scripts', 'enqueue_form_dynamic_style');
function enqueue_form_dynamic_style(): void
{
    if (is_admin()) {
        return;
    }

    $post_id = $GLOBALS['influactive_form_id'] ?? '';

    // Enqueue du fichier dynamic-style.php
    wp_enqueue_style('influactive-form-dynamic-style', plugin_dir_url(__FILE__) . '/dynamic-style.php?post_id=' . $post_id);
}


add_action('wp_ajax_send_email', 'influactive_send_email');
add_action('wp_ajax_nopriv_send_email', 'influactive_send_email');

function influactive_send_email(): void
{
    // Check if our nonce is set and verify it.
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'influactive_send_email')) {
        wp_send_json_error(['message' => 'Nonce verification failed']);
        return;
    }

    // Check form fields
    if (!isset($_POST['form_id'])) {
        wp_send_json_error(['message' => 'Form ID is required']);
        return;
    }

    // Get form fields
    $fields = get_post_meta($_POST['form_id'], '_influactive_form_fields', true);

    foreach ($fields as $field) {
        if (empty($_POST[$field['name']])) {
            wp_send_json_error(['message' => 'All fields are required']);
            return;
        }
    }

    // Get email layout
    $email_layout = get_post_meta($_POST['form_id'], '_influactive_form_email_layout', true);
    $content = str_replace("\n", '<br>', $email_layout['content'] ?? '');
    $subject = $email_layout['subject'] ?? '';
    $to = $email_layout['recipient'] ?? get_bloginfo('admin_email');
    $from = $email_layout['sender'] ?? get_bloginfo('admin_email');
    $sitename = get_bloginfo('name');

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

    // Send email
    if (wp_mail($to, $subject, $content, $headers)) {
        wp_send_json_success(['message' => 'Email sent successfully', 'sent' => true, 'to' => $to, 'subject' => $subject, 'content' => $content, 'headers' => $headers]);
    } else {
        wp_send_json_error(['message' => 'Failed to send email', 'to' => $to, 'subject' => $subject, 'content' => $content, 'headers' => $headers]);
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
