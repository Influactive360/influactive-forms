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
            <li><a href="#preview">Form preview</a></li>
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
            <div id="preview" class="tab">
                <!-- Form preview content -->
                <h2>Form preview</h2>
                <?php do_shortcode('[influactive_form id="' . $post->ID . '"]'); ?>
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
            echo '<p><label>Type <select name="influactive_form_fields[' . (int)$key . '][type]" class="field_type">';
            echo '<option value="text" ' . (isset($field['type']) && $field['type'] === 'text' ? 'selected' : '') . '>Text</option>';
            echo '<option value="email" ' . (isset($field['type']) && $field['type'] === 'email' ? 'selected' : '') . '>Email</option>';
            echo '<option value="number" ' . (isset($field['type']) && $field['type'] === 'number' ? 'selected' : '') . '>Number</option>';
            echo '<option value="textarea" ' . (isset($field['type']) && $field['type'] === 'textarea' ? 'selected' : '') . '>Textarea</option>';
            echo '<option value="select" ' . (isset($field['type']) && $field['type'] === 'select' ? 'selected' : '') . '>Select</option>';
            echo '<option value="gdpr" ' . (isset($field['type']) && $field['type'] === 'gdpr' ? 'selected' : '') . '>GDPR</option>';
            echo '</select></label>';
            if (isset($field['type']) && $field['type'] !== 'gdpr') {
                echo '<label>Label <input type="text" name="influactive_form_fields[' . (int)$key . '][label]" value="' . esc_attr($field['label']) . '" class="influactive_form_fields_label"></label> ';
                echo '<label>Name <input type="text" name="influactive_form_fields[' . (int)$key . '][name]" value="' . strtolower(esc_attr($field['name'])) . '" class="influactive_form_fields_name"></label> ';
            } else {
                echo '<label>Text <input type="text" name="influactive_form_fields[' . (int)$key . '][label]" value="' . esc_attr($field['label']) . '" class="influactive_form_fields_label"></label> ';
                echo '<label><input type="hidden" name="influactive_form_fields[' . (int)$key . '][name]" value="gdpr" class="influactive_form_fields_name"></label>';
            }
            if (isset($field['type']) && $field['type'] === 'select') {
                echo '<div class="options_container">';
                if (is_array($field['options'])) {
                    foreach ($field['options'] as $option_index => $option) {
                        echo '<p class="option-field" data-index="' . $option_index . '">';
                        echo '<label>Option Label';
                        echo '<input type="text" class="option-label" name="influactive_form_fields[' . (int)$key . '][options][' . (int)$option_index . '][label]" value="' . esc_attr($option['label']) . '">';
                        echo '</label>';
                        echo '<label>Option Value';
                        echo '<input type="text" class="option-value" name="influactive_form_fields[' . (int)$key . '][options][' . (int)$option_index . '][value]" value="' . esc_attr($option['value']) . '">';
                        echo '</label>';
                        echo '<a href="#" class="remove_option">Remove option</a>';
                        echo '</p>';
                    }
                }
                echo '</div>';
                echo '<p><a href="#" class="add_option">Add option</a></p>';
            }

            echo '<input type="hidden" name="influactive_form_fields[' . (int)$key . '][order]" value="' . (int)$key . '" class="influactive_form_fields_order">';
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
	$email_style['form']['border_style'] ?? $email_style['form']['border_style'] = 'solid';
	$email_style['label']['font_weight'] ?? $email_style['label']['font_weight'] = 'normal';
    $email_style['input']['font_weight'] ?? $email_style['input']['font_weight'] = 'normal';
	$email_style['input']['border_style'] ?? $email_style['input']['border_style'] = 'solid';
    $email_style['submit']['font_weight'] ?? $email_style['submit']['font_weight'] = 'normal';
    $email_style['submit']['border_style'] ?? $email_style['submit']['border_style'] = 'solid';
    ?>
    <div id="influactive_form_style_container">
        <p>
            <label>
                Form Background color
                <input type="color" name="influactive_form_email_style[form][background_color]"
                       value="<?= $email_style['form']['background_color'] ?? '#f6f6f6' ?>">
            </label>
            <label>
                Form Padding
                <input type="text" name="influactive_form_email_style[form][padding]"
                       value="<?= esc_attr($email_style['form']['padding'] ?? '20px') ?>">
            </label>
            <label>
                Form Border width
                <input type="text" name="influactive_form_email_style[form][border_width]"
                       value="<?= esc_attr($email_style['form']['border_width'] ?? '1px') ?>">
            </label>
            <label>
                Form Border style
                <select name="influactive_form_email_style[form][border_style]">
                    <option value="solid" <?= $email_style['form']['border_style'] === "solid" ? "selected" : "" ?>>Solid
                    </option>
                    <option value="dashed" <?= $email_style['form']['border_style'] === "dashed" ? "selected" : "" ?>>Dashed
                    </option>
                    <option value="dotted" <?= $email_style['form']['border_style'] === "dotted" ? "selected" : "" ?>>Dotted
                    </option>
                    <option value="double" <?= $email_style['form']['border_style'] === "double" ? "selected" : "" ?>>Double
                    </option>
                    <option value="groove" <?= $email_style['form']['border_style'] === "groove" ? "selected" : "" ?>>Groove
                    </option>
                    <option value="ridge" <?= $email_style['form']['border_style'] === "ridge" ? "selected" : "" ?>>Ridge
                    </option>
                    <option value="inset" <?= $email_style['form']['border_style'] === "inset" ? "selected" : "" ?>>Inset
                    </option>
                    <option value="outset" <?= $email_style['form']['border_style'] === "outset" ? "selected" : "" ?>>Outset
                    </option>
                    <option value="none" <?= $email_style['form']['border_style'] === "none" ? "selected" : "" ?>>None</option>
                    <option value="hidden" <?= $email_style['form']['border_style'] === "hidden" ? "selected" : "" ?>>Hidden
                    </option>
                </select>
            </label>
            <label>
                Form Border color
                <input type="color" name="influactive_form_email_style[form][border_color]"
                       value="<?= esc_attr($email_style['form']['border_color'] ?? '#cccccc') ?>">
            </label>
        </p>
        <p>
            <label>
                Label Font family
                <input type="text" name="influactive_form_email_style[label][font_family]"
                       value="<?= esc_attr($email_style['label']['font_family'] ?? 'Arial, Helvetica, sans-serif') ?>">
            </label>
            <label>
                Label font size
                <input type="text" name="influactive_form_email_style[label][font_size]"
                       value="<?= esc_attr($email_style['label']['font_size'] ?? '14px') ?>">
            </label>
            <label>
                Label font color
                <input type="color" name="influactive_form_email_style[label][font_color]"
                       value="<?= esc_attr($email_style['label']['font_color'] ?? '#333333') ?>">
            </label>
            <label>
                Label font weight
                <select name="influactive_form_email_style[label][font_weight]">
                    <option value="normal" <?= $email_style['label']['font_weight'] === "normal" ? "selected" : "" ?>>
                        Normal
                    </option>
                    <option value="bold" <?= $email_style['label']['font_weight'] === "bold" ? "selected" : "" ?>>
                        Bold
                    </option>
                    <option value="bolder" <?= $email_style['label']['font_weight'] === "bolder" ? "selected" : "" ?>>
                        Bolder
                    </option>
                    <option value="medium" <?= $email_style['label']['font_weight'] === "medium" ? "selected" : "" ?>>
                        Medium
                    </option>
                    <option value="lighter" <?= $email_style['label']['font_weight'] === "lighter" ? "selected" : "" ?>>
                        Lighter
                    </option>
                </select>
            </label>
            <label>
                Label line height
                <input type="text" name="influactive_form_email_style[label][line_height]"
                       value="<?= esc_attr($email_style['label']['line_height'] ?? '1.5') ?>">
            </label>
        </p>
        <p>
            <label>
                Input font family
                <input type="text" name="influactive_form_email_style[input][font_family]"
                       value="<?= esc_attr($email_style['input']['font_family'] ?? 'Arial, Helvetica, sans-serif') ?>">
            </label>
            <label>
                Input font size
                <input type="text" name="influactive_form_email_style[input][font_size]"
                       value="<?= esc_attr($email_style['input']['font_size'] ?? '14px') ?>">
            </label>
            <label>
                Input font color
                <input type="color" name="influactive_form_email_style[input][font_color]"
                       value="<?= esc_attr($email_style['input']['font_color'] ?? '#333333') ?>">
            </label>
            <label>
                Input font weight
                <select name="influactive_form_email_style[input][font_weight]">
                    <option value="normal" <?= $email_style['input']['font_weight'] === "normal" ? "selected" : "" ?>>
                        Normal
                    </option>
                    <option value="bold" <?= $email_style['input']['font_weight'] === "bold" ? "selected" : "" ?>>
                        Bold
                    </option>
                    <option value="bolder" <?= $email_style['input']['font_weight'] === "bolder" ? "selected" : "" ?>>
                        Bolder
                    </option>
                    <option value="medium" <?= $email_style['input']['font_weight'] === "medium" ? "selected" : "" ?>>
                        Medium
                    </option>
                    <option value="lighter" <?= $email_style['input']['font_weight'] === "lighter" ? "selected" : "" ?>>
                        Lighter
                    </option>
                </select>
            </label>
            <label>
                Input line height
                <input type="text" name="influactive_form_email_style[input][line_height]"
                       value="<?= esc_attr($email_style['input']['line_height'] ?? '1.5') ?>">
            </label>
            <label>
                Input background color
                <input type="color" name="influactive_form_email_style[input][background_color]"
                       value="<?= esc_attr($email_style['input']['background_color'] ?? '#ffffff') ?>">
            </label>
            <label>
                Input border width
                <input type="text" name="influactive_form_email_style[input][border_width]"
                       value="<?= esc_attr($email_style['input']['border_width'] ?? '1px') ?>">
            </label>
            <label>
                Input border style
                <select name="influactive_form_email_style[input][border_style]">
                    <option value="solid" <?= $email_style['input']['border_style'] === "solid" ? "selected" : "" ?>>
                        Solid
                    </option>
                    <option value="dashed" <?= $email_style['input']['border_style'] === "dashed" ? "selected" : "" ?>>
                        Dashed
                    </option>
                    <option value="dotted" <?= $email_style['input']['border_style'] === "dotted" ? "selected" : "" ?>>
                        Dotted
                    </option>
                    <option value="double" <?= $email_style['input']['border_style'] === "double" ? "selected" : "" ?>>
                        Double
                    </option>
                    <option value="groove" <?= $email_style['input']['border_style'] === "groove" ? "selected" : "" ?>>
                        Groove
                    </option>
                    <option value="ridge" <?= $email_style['input']['border_style'] === "ridge" ? "selected" : "" ?>>
                        Ridge
                    </option>
                    <option value="inset" <?= $email_style['input']['border_style'] === "inset" ? "selected" : "" ?>>
                        Inset
                    </option>
                    <option value="outset" <?= $email_style['input']['border_style'] === "outset" ? "selected" : "" ?>>
                        Outset
                    </option>
                    <option value="hidden" <?= $email_style['input']['border_style'] === "hidden" ? "selected" : "" ?>>
                        Hidden
                    </option>
                </select>
            </label>
            <label>
                Input border color
                <input type="color" name="influactive_form_email_style[input][border_color]"
                       value="<?= esc_attr($email_style['input']['border_color'] ?? '#cccccc') ?>">
            </label>
            <label>
                Input border radius
                <input type="text" name="influactive_form_email_style[input][border_radius]"
                       value="<?= esc_attr($email_style['input']['border_radius'] ?? '0') ?>">
            </label>
            <label>
                Input padding
                <input type="text" name="influactive_form_email_style[input][padding]"
                       value="<?= esc_attr($email_style['input']['padding'] ?? '10px') ?>">
            </label>
        </p>
        <p>
            <label>
                Submit font family
                <input type="text" name="influactive_form_email_style[submit][font_family]"
                       value="<?= esc_attr($email_style['submit']['font_family'] ?? 'Arial, Helvetica, sans-serif') ?>">
            </label>
            <label>
                Submit font size
                <input type="text" name="influactive_form_email_style[submit][font_size]"
                       value="<?= esc_attr($email_style['submit']['font_size'] ?? '14px') ?>">
            </label>
            <label>
                Submit font color
                <input type="color" name="influactive_form_email_style[submit][font_color]"
                       value="<?= esc_attr($email_style['submit']['font_color'] ?? '#ffffff') ?>">
            </label>
            <label>
                Submit font hover color
                <input type="color" name="influactive_form_email_style[submit][font_hover_color]"
                       value="<?= esc_attr($email_style['submit']['font_hover_color'] ?? '#ffffff') ?>">
            </label>
            <label>
                Submit font weight
                <select name="influactive_form_email_style[submit][font_weight]">
                    <option value="normal" <?= $email_style['submit']['font_weight'] === "normal" ? "selected" : "" ?>>Normal
                    </option>
                    <option value="bold" <?= $email_style['submit']['font_weight'] === "bold" ? "selected" : "" ?>>Bold</option>
                    <option value="bolder" <?= $email_style['submit']['font_weight'] === "bolder" ? "selected" : "" ?>>Bolder
                    </option>
                    <option value="lighter" <?= $email_style['submit']['font_weight'] === "lighter" ? "selected" : "" ?>>
                        Lighter
                    </option>
                </select>
            </label>
            <label>
                Submit line height
                <input type="text" name="influactive_form_email_style[submit][line_height]"
                       value="<?= esc_attr($email_style['submit']['line_height'] ?? '1.5') ?>">
            </label>
            <label>
                Submit background color
                <input type="color" name="influactive_form_email_style[submit][background_color]"
                       value="<?= esc_attr($email_style['submit']['background_color'] ?? '#333333') ?>">
            </label>
            <label>
                Submit background hover color
                <input type="color" name="influactive_form_email_style[submit][background_hover_color]"
                       value="<?= esc_attr($email_style['submit']['background_hover_color'] ?? '#333333') ?>">
            </label>
            <label>
                Submit border color
                <input type="color" name="influactive_form_email_style[submit][border_color]"
                       value="<?= esc_attr($email_style['submit']['border_color'] ?? '#333333') ?>">
            </label>
            <label>
                Submit border style
                <select name="influactive_form_email_style[submit][border_style]">
                    <option value="solid" <?= $email_style['submit']['border_style'] === "solid" ? "selected" : "" ?>>
                        Solid
                    </option>
                    <option value="dashed" <?= $email_style['submit']['border_style'] === "dashed" ? "selected" : "" ?>>
                        Dashed
                    </option>
                    <option value="dotted" <?= $email_style['submit']['border_style'] === "dotted" ? "selected" : "" ?>>
                        Dotted
                    </option>
                    <option value="double" <?= $email_style['submit']['border_style'] === "double" ? "selected" : "" ?>>
                        Double
                    </option>
                    <option value="groove" <?= $email_style['submit']['border_style'] === "groove" ? "selected" : "" ?>>
                        Groove
                    </option>
                    <option value="ridge" <?= $email_style['submit']['border_style'] === "ridge" ? "selected" : "" ?>>
                        Ridge
                    </option>
                    <option value="inset" <?= $email_style['submit']['border_style'] === "inset" ? "selected" : "" ?>>
                        Inset
                    </option>
                    <option value="outset" <?= $email_style['submit']['border_style'] === "outset" ? "selected" : "" ?>>
                        Outset
                    </option>
                    <option value="hidden" <?= $email_style['submit']['border_style'] === "hidden" ? "selected" : "" ?>>
                        Hidden
                    </option>
                </select>
            </label>
            <label>
                Submit border width
                <input type="text" name="influactive_form_email_style[submit][border_width]"
                       value="<?= esc_attr($email_style['submit']['border_width'] ?? '1px') ?>">
            </label>
            <label>
                Submit border radius
                <input type="text" name="influactive_form_email_style[submit][border_radius]"
                       value="<?= esc_attr($email_style['submit']['border_radius'] ?? '0') ?>">
            </label>
            <label>
                Submit padding
                <input type="text" name="influactive_form_email_style[submit][padding]"
                       value="<?= esc_attr($email_style['submit']['padding'] ?? '10px 20px') ?>">
            </label>
        </p>
    </div>
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
    $fields = get_post_meta($post->ID, '_influactive_form_fields', true) ?? [];
    ?>
    <p><strong>Fields available in the email</strong></p>
    <ul>
        <?php foreach ($fields as $field): ?>
            <?php if ($field['type'] === 'select'): ?>
                <li>
                    <code>
                        {<?= strtolower($field['name']) ?>:label}
                    </code>
                </li>
                <li>
                    <code>
                        {<?= strtolower($field['name']) ?>:value}
                    </code>
                </li>
            <?php else: ?>
                <li><code>{<?= strtolower($field['name']) ?>}</code></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php
}

// Enregistrement des champs
add_action('save_post', 'influactive_form_save_post');
function influactive_form_save_post($post_id): void
{
    if (get_post_type($post_id) === 'influactive-forms') {
        $fields = $_POST['influactive_form_fields'] ?? [];
        $fields_type = $_POST['influactive_form_fields']['type'] ?? [];
        $fields_label = $_POST['influactive_form_fields']['label'] ?? [];
        $fields_name = $_POST['influactive_form_fields']['name'] ?? [];
        $fields_options = $_POST['influactive_form_fields']['options'] ?? [];
        $field_order = $_POST['influactive_form_fields']['order'] ?? [];

        for ($i = 0, $iMax = count($fields_name); $i < $iMax; $i++) {
            $options = [
                'label' => '',
                'value' => '',
            ];
            if (isset($fields_options[$field_order[$i]])) {
                foreach ($fields_options[$field_order[$i]] as $key => $option) {
                    $options[$key] = is_array($option)
                        ? array_map('sanitize_text_field', $option)
                        : sanitize_text_field($option);
                }
            }


            $fields[$i] = [
                'type' => sanitize_text_field($fields_type[$i]),
                'label' => sanitize_text_field($fields_label[$i]),
                'name' => strtolower(sanitize_text_field($fields_name[$i])),
                'order' => (int)$field_order[$i],
            ];

            if ($fields[$i]['type'] === 'select' && isset($fields_options[$field_order[$i]])) {
                $fields[$i]['options']['label'] = $options['label'];
                $fields[$i]['options']['value'] = $options['value'];
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
