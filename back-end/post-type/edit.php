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
    <?php influactive_form_shortcode($post); ?>
    <div class="tabs">
        <ul class="tab-links">
            <li class="active"><a href="#fields">Form Fields</a></li>
            <li><a href="#style">Form Style</a></li>
            <li><a href="#email">Email Layout</a></li>
        </ul>

        <div class="tab-content">
            <div id="fields" class="tab active">
                <!-- Form fields content -->
                <h2>Form Fields</h2>
                <?php influactive_form_fields_listing($post); ?>
            </div>
            <div id="style" class="tab">
                <!-- Email style content -->
                <h2>Form Style</h2>
                <?php influactive_form_email_style($post); ?>
            </div>
            <div id="email" class="tab">
                <!-- Email style content -->
                <h2>Email Layout</h2>
                <?php influactive_form_email_layout($post); ?>
            </div>
        </div>
    </div>
    <?php
}

function influactive_form_shortcode($post): void
{
    echo '<code>[influactive_form id="' . $post->ID . '"]</code>';
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
            echo '<label>Name <input type="text" name="influactive_form_fields_name[' . $key . ']" value="' . strtolower(esc_attr($field['name'])) . '"></label> ';
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

function influactive_form_email_style($post): void
{
    $email_style = get_post_meta($post->ID, '_influactive_form_email_style', true);
    ?>
    <p>
        <label>
            Background color
            <input type="color" name="influactive_form_email_style[background_color]"
                   value="<?php echo esc_attr($email_style['background_color'] ?? '#f6f6f6'); ?>">
        </label>
    </p>
    <p>
        <label>
            Padding
            <input type="text" name="influactive_form_email_style[text_color]"
                   value="<?php echo esc_attr($email_style['text_color'] ?? '20px'); ?>">
        </label>
    </p>
    <p>
        <label>Border width
            <input type="number" name="influactive_form_email_style[link_color]"
                   value="<?php echo esc_attr($email_style['link_color'] ?? '1'); ?>">px
        </label>
        <label>Border style
            <select name="influactive_form_email_style[link_color]">
                <option value="solid" selected>Solid</option>
                <option value="dashed">Dashed</option>
                <option value="dotted">Dotted</option>
                <option value="double">Double</option>
                <option value="groove">Groove</option>
                <option value="ridge">Ridge</option>
                <option value="inset">Inset</option>
                <option value="outset">Outset</option>
                <option value="none">None</option>
                <option value="hidden">Hidden</option>
            </select>
        </label>
        <label>Border color
            <input type="color" name="influactive_form_email_style[link_color]"
                   value="<?php echo esc_attr($email_style['link_color'] ?? '#ccc'); ?>">
        </label>
    </p>
    <?php
}

function influactive_form_email_layout($post): void
{
    $email_layout = get_post_meta($post->ID, '_influactive_form_email_layout', true);
    ?>
    <div id="influactive_form_layout_container">
        <p>
            <label>
                Email sender
                <input type="text" name="influactive_form_email_layout[sender]"
                       value="<?= esc_attr($email_layout['sender'] ?? get_bloginfo('admin_email')) ?>">
            </label>
        </p>
        <p>
            <label>
                Email recipient
                <input type="text" name="influactive_form_email_layout[recipient]"
                       value="<?= esc_attr($email_layout['recipient'] ?? get_bloginfo('admin_email')) ?>">
            </label>
        <p>
            <label>
                Subject of the email
                <input type="text" name="influactive_form_email_layout[subject]"
                       value="<?= esc_attr($email_layout['subject'] ?? '') ?>">
            </label>
        </p>
        <p>
            <label>
                Content of the email
                <textarea name="influactive_form_email_layout[content]" cols="30"
                          rows="15"><?= esc_attr($email_layout['content'] ?? '') ?></textarea>

            </label>
        </p>
    </div>
    <?php

// List all influactive_form_fields_name like "{field_name}"
    $fields = get_post_meta($post->ID, '_influactive_form_fields', true);
    $fields_name = [];
    if (is_array($fields)) {
        foreach ($fields as $field) {
            $fields_name[] = '{' . $field['name'] . '}';
        }
    }
    ?>
    <p><strong>Fields available in the email</strong></p>
    <ul>
        <?php foreach ($fields_name as $field_name): ?>
            <li><code><?= strtolower($field_name) ?></code></li>
        <?php endforeach; ?>
    </ul>
    <?php
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

        for ($i = 0, $iMax = count($fields_name); $i < $iMax; $i++) {
            $options = [];
            if (isset($fields_options[$field_order[$i]])) {
                $options = is_array($fields_options[$field_order[$i]])
                    ? array_map('sanitize_text_field', $fields_options[$field_order[$i]])
                    : [sanitize_text_field($fields_options[$field_order[$i]])];
            }

            $fields[] = [
                'type' => sanitize_text_field($fields_type[$i]),
                'label' => sanitize_text_field($fields_label[$i]),
                'name' => strtolower(sanitize_text_field($fields_name[$i])),
                'order' => (int)$field_order[$i],
            ];

            if ($fields[$i]['type'] === 'select' && empty($fields[$i]['options'])) {
                $fields[$i]['options'] = $options;
            }
        }

        update_post_meta($post_id, '_influactive_form_fields', $fields);

        $email_style = $_POST['influactive_form_email_style'] ?? [];
        $email_layout = $_POST['influactive_form_email_layout'] ?? [];

        // Sanitize email layout content
        if (isset($email_layout['content'])) {
            $email_layout['content'] = wp_kses_post($email_layout['content']);
        }
        // Sanitize email layout subject
        if (isset($email_layout['subject'])) {
            $email_layout['subject'] = sanitize_text_field($email_layout['subject']);
        }

        update_post_meta($post_id, '_influactive_form_email_style', $email_style);
        update_post_meta($post_id, '_influactive_form_email_layout', $email_layout);
    }
}
