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
                    echo '<label>' . $field['label'] . ': <input type="email" name="' . esc_attr($field['name']) . '"></label>';
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
                        echo '<option value="' . esc_attr($option) . '">' . esc_attr($option) . '</option>';
                    }
                    echo '</select></label>';
                    break;
            }
        }

        echo '<input type="submit" value="Submit">';


        echo '<div class="influactive-form-message"></div>';
        echo '</form>';

    }
}

function enqueue_dynamic_style(): void
{
    if (is_admin()) {
        return;
    }

    $post_id = $GLOBALS['influactive_form_id'] ?? '';

    // Enqueue du fichier dynamic-style.php
    wp_enqueue_style('influactive-form-dynamic-style', plugin_dir_url(__FILE__) . 'front-end/dynamic-style.php?post_id=' . $post_id);
}

add_action('wp_enqueue_scripts', 'enqueue_dynamic_style');

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
    $content = $email_layout['content'] ?? '';
    $content = str_replace("\n", '<br>', $content);
    $subject = $email_layout['subject'] ?? '';
    $to = $email_layout['recipient'] ?? get_bloginfo('admin_email');
    $from = $email_layout['sender'] ?? get_bloginfo('admin_email');
    $sitename = get_bloginfo('name');

    foreach ($fields as $field) {
        $content = str_replace('{' . $field['name'] . '}', sanitize_text_field($_POST[$field['name']]), $content);
        $subject = str_replace('{' . $field['name'] . '}', sanitize_text_field($_POST[$field['name']]), $subject);
        $to = str_replace('{' . $field['name'] . '}', sanitize_text_field($_POST[$field['name']]), $to);
    }

    $from = sanitize_email($from);
    $to = sanitize_email($to);

    // Email details
    $headers = ['Content-Type: text/html; charset=UTF-8', 'From: ' . $sitename . ' <' . $from . '>'];

    // Send email
    if (wp_mail($to, $subject, $content, $headers)) {
        wp_send_json_success(['sent' => true, 'to' => $to, 'subject' => $subject, 'content' => $content, 'headers' => $headers]);

    } else {
        wp_send_json_error(['message' => 'Failed to send email', 'to' => $to, 'subject' => $subject, 'content' => $content, 'headers' => $headers]);
    }

    wp_die();
}
