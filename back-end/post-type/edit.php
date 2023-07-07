<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('add_meta_boxes', 'influactive_form_add_meta_boxes');
function influactive_form_add_meta_boxes(): void
{
    add_meta_box('influactive_form_shortcode', 'Shortcode', 'influactive_form_shortcode_metabox', 'influactive-forms');
}


function influactive_form_shortcode_metabox($post): void
{
    echo '[influactive_form id="' . $post->ID . '"]';
}

// Ajouter le métabox pour les champs de texte
add_action('add_meta_boxes', 'influactive_form_add_form');
function influactive_form_add_form(): void
{
    add_meta_box('influactive_form_fields', 'Form Fields', 'influactive_form_fields_listing', 'influactive-forms');
}

function influactive_form_fields_listing($post): void
{
    $fields = get_post_meta($post->ID, '_influactive_form_fields', true);

    var_dump($fields);

    echo '<div id="influactive_form_fields_container">';

    if (is_array($fields)) {
        foreach ($fields as $key => $field) {
            echo '<div class="influactive_form_field">';
            echo '<p><label>Type <select name="influactive_form_fields_type[]" class="field_type">';
            echo '<option value="text" ' . (isset($field['type']) && $field['type'] === 'text' ? 'selected' : '') . '>Text</option>';
            echo '<option value="email" ' . (isset($field['type']) && $field['type'] === 'email' ? 'selected' : '') . '>Email</option>';
            echo '<option value="number" ' . (isset($field['type']) && $field['type'] === 'number' ? 'selected' : '') . '>Number</option>';
            echo '<option value="textarea" ' . (isset($field['type']) && $field['type'] === 'textarea' ? 'selected' : '') . '>Textarea</option>';
            echo '<option value="select" ' . (isset($field['type']) && $field['type'] === 'select' ? 'selected' : '') . '>Select</option>';
            echo '</select></label>';
            echo '<label>Label <input type="text" name="influactive_form_fields_label[' . $key . ']" value="' . esc_attr($field['label']) . '"></label> ';
            echo '<label>Name <input type="text" name="influactive_form_fields_name[' . $key . ']" value="' . esc_attr($field['name']) . '"></label> ';
            if (isset($field['type']) && $field['type'] === 'select') {
                echo '<div class="options_container">';
                if (is_array($field['options'])) {
                    foreach ($field['options'] as $option) {
                        echo '<p class="option-field"><label>Option <input type="text" name="influactive_form_fields_option[' . esc_attr($field['name']) . '][]" value="' . esc_attr($option) . '"></label> <a href="#" class="remove_option">Remove option</a></p>';
                    }
                }
                echo '</div>';
                echo '<p><a href="#" class="add_option">Add option</a></p>';
            }
            echo '<a href="#" class="remove_field">Remove</a></p>';
            echo '</div>';
        }
    }

    echo '</div>';

    echo '<p><a href="#" id="add_field">Add Field</a></p>';
}

// Enregistrement des champs
add_action('save_post', 'influactive_form_save_post');
function influactive_form_save_post($post_id): void
{
    if (get_post_type($post_id) === 'influactive-forms') {
        $fields_label = $_POST['influactive_form_fields_label'] ?? [];
        $fields_name = $_POST['influactive_form_fields_name'] ?? [];
        $fields_type = $_POST['influactive_form_fields_type'] ?? [];
        $fields_options = $_POST['influactive_form_fields_option'] ?? [];
        $fields = [];

        for ($i = 0, $iMax = count($fields_label); $i < $iMax; $i++) {
            $options = [];
            if (isset($fields_options[$fields_name[$i]])) {
                $options = is_array($fields_options[$fields_name[$i]])
                    ? array_map('sanitize_text_field', $fields_options[$fields_name[$i]])
                    : [sanitize_text_field($fields_options[$fields_name[$i]])];
            }

            $fields[] = [
                'type' => sanitize_text_field($fields_type[$i]),
                'label' => sanitize_text_field($fields_label[$i]),
                'name' => sanitize_text_field($fields_name[$i]),
                'options' => $options
            ];
        }

        update_post_meta($post_id, '_influactive_form_fields', $fields);
    }
}
