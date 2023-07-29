<?php
/**
 * Description: This file is responsible for generating the dynamic CSS for the
 * email style.
 *
 * @package Influactive Forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	requireWordPressCore(
		array(
			'/wp-load.php', // WordPress.
			'/wordpress/wp-load.php', // WordPlate.
			'/wp/wp-load.php', // Radicle.
		)
	);
}

header( 'Content-type: text/css; charset: UTF-8' );

$my_post_id  = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
$email_style = get_post_meta( $my_post_id, '_influactive_form_email_style', true );
$form        = $email_style['form'];
$label       = $email_style['label'];
$input       = $email_style['input'];
$submit      = $email_style['submit'];
$free_text   = $email_style['free_text'];

ob_start();
?>
	.influactive-form-wrapper {
	padding: <?php echo $form['padding']; ?>;
	background-color: <?php echo $form['background_color']; ?>;
	border-width: <?php echo $form['border_width']; ?>;
	border-style: <?php echo $form['border_style']; ?>;
	border-color: <?php echo $form['border_color']; ?>;
	}

	.influactive-form-wrapper label {
	font-weight: <?php echo $label['font_weight']; ?>;
	font-family: <?php echo $label['font_family']; ?>;
	font-size: <?php echo $label['font_size']; ?>;
	color: <?php echo $label['font_color']; ?>;
	line-height: <?php echo $label['line_height']; ?>;
	}

	.influactive-form-wrapper input[type="text"],
	.influactive-form-wrapper input[type="email"],
	.influactive-form-wrapper input[type="number"],
	.influactive-form-wrapper textarea,
	.influactive-form-wrapper select {
	padding: <?php echo $input['padding']; ?>;
	border-width: <?php echo $input['border_width']; ?>;
	border-style: <?php echo $input['border_style']; ?>;
	border-color: <?php echo $input['border_color']; ?>;
	border-radius: <?php echo $input['border_radius']; ?>;
	background-color: <?php echo $input['background_color']; ?>;
	color: <?php echo $input['font_color']; ?>;
	font-size: <?php echo $input['font_size']; ?>;
	font-weight: <?php echo $input['font_weight']; ?>;
	font-family: <?php echo $input['font_family']; ?>;
	line-height: <?php echo $input['line_height']; ?>;
	}

	.influactive-form-wrapper input[type="checkbox"] {
	color: <?php echo $input['font_color']; ?>;
	font-size: <?php echo $input['font_size']; ?>;
	font-weight: <?php echo $input['font_weight']; ?>;
	font-family: <?php echo $input['font_family']; ?>;
	line-height: <?php echo $input['line_height']; ?>;
	}

	.influactive-form-wrapper input[type="submit"] {
	padding: <?php echo $submit['padding']; ?>;
	color: <?php echo $submit['font_color']; ?>;
	background-color: <?php echo $submit['background_color']; ?>;
	border-radius: <?php echo $submit['border_radius']; ?>;
	border-width: <?php echo $submit['border_width']; ?>;
	border-style: <?php echo $submit['border_style']; ?>;
	border-color: <?php echo $submit['border_color']; ?>;
	font-size: <?php echo $submit['font_size']; ?>;
	font-weight: <?php echo $submit['font_weight']; ?>;
	font-family: <?php echo $submit['font_family']; ?>;
	line-height: <?php echo $submit['line_height']; ?>;
	}

	.influactive-form-wrapper input[type="submit"]:hover {
	background-color: <?php echo $submit['background_hover_color']; ?>;
	color: <?php echo $submit['font_hover_color']; ?>;
	}

	.influactive-form-wrapper .free-text {
	font-size: <?php echo $free_text['font_size']; ?>;
	font-weight: <?php echo $free_text['font_weight']; ?>;
	font-family: <?php echo $free_text['font_family']; ?>;
	line-height: <?php echo $free_text['line_height']; ?>;
	color: <?php echo $free_text['color']; ?>;
	}

<?php
$css = ob_get_clean();
echo $css;
