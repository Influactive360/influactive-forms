<?php
/**
 * Description: This file is responsible for generating the dynamic CSS for the
 * email style.
 *
 * @package Forms by Influactive
 */

if ( ! defined( 'ABSPATH' ) ) {
	require_wordpress_core(
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
	padding: <?php echo esc_html( $form['padding'] ); ?>;
	background-color: <?php echo esc_html( $form['background_color'] ); ?>;
	border-width: <?php echo esc_html( $form['border_width'] ); ?>;
	border-style: <?php echo esc_html( $form['border_style'] ); ?>;
	border-color: <?php echo esc_html( $form['border_color'] ); ?>;
	}

	.influactive-form-wrapper label {
	font-weight: <?php echo esc_html( $label['font_weight'] ); ?>;
	font-family: <?php echo esc_html( $label['font_family'] ); ?>;
	font-size: <?php echo esc_html( $label['font_size'] ); ?>;
	color: <?php echo esc_html( $label['font_color'] ); ?>;
	line-height: <?php echo esc_html( $label['line_height'] ); ?>;
	}

	.influactive-form-wrapper input[type="text"],
	.influactive-form-wrapper input[type="email"],
	.influactive-form-wrapper input[type="number"],
	.influactive-form-wrapper textarea,
	.influactive-form-wrapper select {
	padding: <?php echo esc_html( $input['padding'] ); ?>;
	border-width: <?php echo esc_html( $input['border_width'] ); ?>;
	border-style: <?php echo esc_html( $input['border_style'] ); ?>;
	border-color: <?php echo esc_html( $input['border_color'] ); ?>;
	border-radius: <?php echo esc_html( $input['border_radius'] ); ?>;
	background-color: <?php echo esc_html( $input['background_color'] ); ?>;
	color: <?php echo esc_html( $input['font_color'] ); ?>;
	font-size: <?php echo esc_html( $input['font_size'] ); ?>;
	font-weight: <?php echo esc_html( $input['font_weight'] ); ?>;
	font-family: <?php echo esc_html( $input['font_family'] ); ?>;
	line-height: <?php echo esc_html( $input['line_height'] ); ?>;
	}

	.influactive-form-wrapper input[type="checkbox"] {
	color: <?php echo esc_html( $input['font_color'] ); ?>;
	font-size: <?php echo esc_html( $input['font_size'] ); ?>;
	font-weight: <?php echo esc_html( $input['font_weight'] ); ?>;
	font-family: <?php echo esc_html( $input['font_family'] ); ?>;
	line-height: <?php echo esc_html( $input['line_height'] ); ?>;
	}

	.influactive-form-wrapper input[type="submit"] {
	padding: <?php echo esc_html( $submit['padding'] ); ?>;
	color: <?php echo esc_html( $submit['font_color'] ); ?>;
	background-color: <?php echo esc_html( $submit['background_color'] ); ?>;
	border-radius: <?php echo esc_html( $submit['border_radius'] ); ?>;
	border-width: <?php echo esc_html( $submit['border_width'] ); ?>;
	border-style: <?php echo esc_html( $submit['border_style'] ); ?>;
	border-color: <?php echo esc_html( $submit['border_color'] ); ?>;
	font-size: <?php echo esc_html( $submit['font_size'] ); ?>;
	font-weight: <?php echo esc_html( $submit['font_weight'] ); ?>;
	font-family: <?php echo esc_html( $submit['font_family'] ); ?>;
	line-height: <?php echo esc_html( $submit['line_height'] ); ?>;
	}

	.influactive-form-wrapper input[type="submit"]:hover {
	background-color: <?php echo esc_html( $submit['background_hover_color'] ); ?>;
	color: <?php echo esc_html( $submit['font_hover_color'] ); ?>;
	}

	.influactive-form-wrapper .free-text {
	font-size: <?php echo esc_html( $free_text['font_size'] ); ?>;
	font-weight: <?php echo esc_html( $free_text['font_weight'] ); ?>;
	font-family: <?php echo esc_html( $free_text['font_family'] ); ?>;
	line-height: <?php echo esc_html( $free_text['line_height'] ); ?>;
	color: <?php echo esc_html( $free_text['color'] ); ?>;
	}

<?php
$css = ob_get_clean();
echo esc_html( $css );
