<?php
/**
 * Plugin Name: Influactive Forms
 * Description: A plugin to create custom forms and display them anywhere on your website.
 * Version: 1.2.6
 * Author: Influactive
 * Author URI: https://influactive.com
 * Text Domain: influactive-forms
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 *  @package Influactive Forms
 */

if (! defined('ABSPATH')) {
    requireWordPressCore(array(
            '/wp-load.php', // WordPress
            '/wordpress/wp-load.php', // WordPlate
            '/wp/wp-load.php', // Radicle
    ));
}

header("Content-type: text/css; charset: UTF-8");

$post_id     = isset($_GET['post_id']) ? (int) $_GET['post_id'] : 0;
$email_style = get_post_meta($post_id, '_influactive_form_email_style', true);
$form        = $email_style['form'];
$label       = $email_style['label'];
$input       = $email_style['input'];
$submit      = $email_style['submit'];
$free_text   = $email_style['free_text'];

ob_start();
?>
    .influactive-form-wrapper {
    padding: <?= $form['padding'] ?>;
    background-color: <?= $form['background_color'] ?>;
    border-width: <?= $form['border_width'] ?>;
    border-style: <?= $form['border_style'] ?>;
    border-color: <?= $form['border_color'] ?>;
    }

    .influactive-form-wrapper label {
    font-weight: <?= $label['font_weight'] ?>;
    font-family: <?= $label['font_family'] ?>;
    font-size: <?= $label['font_size'] ?>;
    color: <?= $label['font_color'] ?>;
    line-height: <?= $label['line_height'] ?>;
    }

    .influactive-form-wrapper input[type="text"],
    .influactive-form-wrapper input[type="email"],
    .influactive-form-wrapper input[type="number"],
    .influactive-form-wrapper textarea,
    .influactive-form-wrapper select {
    padding: <?= $input['padding'] ?>;
    border-width: <?= $input['border_width'] ?>;
    border-style: <?= $input['border_style'] ?>;
    border-color: <?= $input['border_color'] ?>;
    border-radius: <?= $input['border_radius'] ?>;
    background-color: <?= $input['background_color'] ?>;
    color: <?= $input['font_color'] ?>;
    font-size: <?= $input['font_size'] ?>;
    font-weight: <?= $input['font_weight'] ?>;
    font-family: <?= $input['font_family'] ?>;
    line-height: <?= $input['line_height'] ?>;
    }

    .influactive-form-wrapper input[type="checkbox"] {
    color: <?= $input['font_color'] ?>;
    font-size: <?= $input['font_size'] ?>;
    font-weight: <?= $input['font_weight'] ?>;
    font-family: <?= $input['font_family'] ?>;
    line-height: <?= $input['line_height'] ?>;
    }

    .influactive-form-wrapper input[type="submit"] {
    padding: <?= $submit['padding'] ?>;
    color: <?= $submit['font_color'] ?>;
    background-color: <?= $submit['background_color'] ?>;
    border-radius: <?= $submit['border_radius'] ?>;
    border-width: <?= $submit['border_width'] ?>;
    border-style: <?= $submit['border_style'] ?>;
    border-color: <?= $submit['border_color'] ?>;
    font-size: <?= $submit['font_size'] ?>;
    font-weight: <?= $submit['font_weight'] ?>;
    font-family: <?= $submit['font_family'] ?>;
    line-height: <?= $submit['line_height'] ?>;
    }

    .influactive-form-wrapper input[type="submit"]:hover {
    background-color: <?= $submit['background_hover_color'] ?>;
    color: <?= $submit['font_hover_color'] ?>;
    }

    .influactive-form-wrapper .free-text {
    font-size: <?= $free_text['font_size'] ?>;
    font-weight: <?= $free_text['font_weight'] ?>;
    font-family: <?= $free_text['font_family'] ?>;
    line-height: <?= $free_text['line_height'] ?>;
    color: <?= $free_text['color'] ?>;
    }

<?php
$css = ob_get_clean();
echo $css;
