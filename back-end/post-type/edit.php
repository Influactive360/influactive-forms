<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Add metabox for influactive forms
add_action('add_meta_boxes', 'influactive_form_add_metaboxes');
function influactive_form_add_metaboxes(): void
{
    add_meta_box('influactive_form_metabox', 'Influactive Form', 'influactive_form_metabox', 'influactive-forms');
}

function influactive_form_metabox($post): void
{
    ?>
    <div class="tabs">
        <ul class="tab-links">
            <li class="active"><a href="#shortcode">Shortcode</a></li>
            <li><a href="#fields">Form Fields</a></li>
            <li><a href="#style">Form Style</a></li>
            <li><a href="#email">Email Style</a></li>
        </ul>

        <div class="tab-content">
            <div id="shortcode" class="tab active">
                <!-- Shortcode content -->
                <h2>Shortcode</h2>
                <?php influactive_form_shortcode_metabox($post); ?>
            </div>
            <div id="fields" class="tab">
                <!-- Form fields content -->
                <h2>Form Fields</h2>
                <?php influactive_form_fields_listing($post); ?>
            </div>
            <div id="style" class="tab">
                <!-- Form style content -->
            </div>
            <div id="email" class="tab">
                <!-- Email style content -->
            </div>
        </div>
    </div>
    <?php
}


function influactive_form_shortcode_metabox($post): void
{
    echo '[influactive_form id="' . $post->ID . '"]';
}

function influactive_form_fields_listing($post): void
{
    $fields = get_post_meta($post->ID, '_influactive_form_fields', true);

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
                        echo '<p class="option-field"><label>Option <input type="text" name="influactive_form_fields_option[' . esc_attr($key) . '][]" value="' . esc_attr($option) . '"></label> <a href="#" class="remove_option">Remove option</a></p>';
                    }
                }
                echo '</div>';
                echo '<p><a href="#" class="add_option">Add option</a></p>';
            }
            echo '<input type="hidden" name="influactive_form_fields_order[' . $key . ']" value="' . $key . '">';
            echo '<a href="#" class="remove_field">Remove the field</a></p>';
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
        $field_order = $_POST['influactive_form_fields_order'] ?? [];
        $fields = [];

        for ($i = 0, $iMax = count($fields_label); $i < $iMax; $i++) {
            $options = [];
            if (isset($fields_options[$field_order[$i]])) {
                $options = is_array($fields_options[$field_order[$i]])
                    ? array_map('sanitize_text_field', $fields_options[$field_order[$i]])
                    : [sanitize_text_field($fields_options[$field_order[$i]])];
            }

            $fields[] = [
                'type' => sanitize_text_field($fields_type[$i]),
                'label' => sanitize_text_field($fields_label[$i]),
                'name' => sanitize_text_field($fields_name[$i]),
                'order' => (int)$field_order[$i],
            ];

            if ($fields[$i]['type'] === 'select' && empty($fields[$i]['options'])) {
                $fields[$i]['options'] = $options;
            }
        }

        update_post_meta($post_id, '_influactive_form_fields', $fields);
    }
}
