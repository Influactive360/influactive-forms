<?php
/**
 * Description: This file is used to add the email style metabox to the form
 *
 * @throws RuntimeException If WordPress environment is not loaded.
 * @package Influactive Forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

/**
 * Display the email styles for the Influactive form.
 *
 * @param WP_Post $post The current post object.
 *
 * @return void
 */
function influactive_form_email_style( WP_Post $post ): void {
	$email_style = get_post_meta( $post->ID, '_influactive_form_email_style', true );
	if ( ! isset( $email_style['form']['border_style'] ) ) {
		$email_style['form']['border_style'] = 'solid';
	}
	if ( ! isset( $email_style['label']['font_weight'] ) ) {
		$email_style['label']['font_weight'] = 'normal';
	}
	if ( ! isset( $email_style['input']['font_weight'] ) ) {
		$email_style['input']['font_weight'] = 'normal';
	}
	if ( ! isset( $email_style['input']['border_style'] ) ) {
		$email_style['input']['border_style'] = 'solid';
	}
	if ( ! isset( $email_style['submit']['font_weight'] ) ) {
		$email_style['submit']['font_weight'] = 'normal';
	}
	if ( ! isset( $email_style['submit']['border_style'] ) ) {
		$email_style['submit']['border_style'] = 'solid';
	}
	?>
	<div id="influactive_form_style_container">
		<p>
			<label>
				<?php echo esc_html__( 'Form Background color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[form][background_color]"
					value="<?php echo esc_attr( $email_style['form']['background_color'] ) ?? '#f6f6f6'; ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Form Padding', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[form][padding]"
					value="<?php echo esc_attr( $email_style['form']['padding'] ?? '20px' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Form Border width', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[form][border_width]"
					value="<?php echo esc_attr( $email_style['form']['border_width'] ?? '1px' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Form Border style', 'influactive-forms' ); ?>
				<select name="influactive_form_email_style[form][border_style]">
					<option
						value="solid" <?php echo 'solid' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Solid', 'influactive-forms' ); ?>
					</option>
					<option
						value="dashed" <?php echo 'dashed' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Dashed', 'influactive-forms' ); ?>
					</option>
					<option
						value="dotted" <?php echo 'dotted' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Dotted', 'influactive-forms' ); ?>
					</option>
					<option
						value="double" <?php echo 'double' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Double', 'influactive-forms' ); ?>
					</option>
					<option
						value="groove" <?php echo ( 'groove' === $email_style['form']['border_style'] ) ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Groove', 'influactive-forms' ); ?>
					</option>
					<option
						value="ridge" <?php echo 'ridge' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Ridge', 'influactive-forms' ); ?>
					</option>
					<option
						value="inset" <?php echo 'inset' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Inset', 'influactive-forms' ); ?>
					</option>
					<option
						value="outset" <?php echo 'outset' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Outset', 'influactive-forms' ); ?>
					</option>
					<option
						value="none" <?php echo 'none' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'None', 'influactive-forms' ); ?></option>
					<option
						value="hidden" <?php echo 'hidden' === $email_style['form']['border_style'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Hidden', 'influactive-forms' ); ?>
					</option>
				</select>
			</label>
			<label>
				<?php echo esc_html__( 'Form Border color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[form][border_color]"
					value="<?php echo esc_attr( $email_style['form']['border_color'] ?? '#cccccc' ); ?>"
				>
			</label>
		</p>
		<p>
			<label>
				<?php echo esc_html__( 'Label Font family', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[label][font_family]"
					value="<?php echo esc_attr( $email_style['label']['font_family'] ?? 'Arial, Helvetica, sans-serif' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Label font size', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[label][font_size]"
					value="<?php echo esc_attr( $email_style['label']['font_size'] ?? '14px' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Label font color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[label][font_color]"
					value="<?php echo esc_attr( $email_style['label']['font_color'] ?? '#333333' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Label font weight', 'influactive-forms' ); ?>
				<select name="influactive_form_email_style[label][font_weight]">
					<option
						value="normal" <?php echo 'normal' === $email_style['label']['font_weight'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Normal', 'influactive-forms' ); ?>
					</option>
					<option
						value="bold" <?php echo 'bold' === $email_style['label']['font_weight'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Bold', 'influactive-forms' ); ?>
					</option>
					<option
						value="bolder" <?php echo 'bolder' === $email_style['label']['font_weight'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Bolder', 'influactive-forms' ); ?>
					</option>
					<option
						value="medium" <?php echo 'medium' === $email_style['label']['font_weight'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Medium', 'influactive-forms' ); ?>
					</option>
					<option
						value="lighter" <?php echo 'lighter' === $email_style['label']['font_weight'] ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'Lighter', 'influactive-forms' ); ?>
					</option>
				</select>
			</label>
			<label>
				<?php echo esc_html__( 'Label line height', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[label][line_height]"
					value="<?php echo esc_attr( $email_style['label']['line_height'] ?? '1.5' ); ?>"
				>
			</label>
		</p>
		<p>
			<label>
				<?php echo esc_html__( 'Input font family', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[input][font_family]"
					value="<?php echo esc_attr( $email_style['input']['font_family'] ?? 'Arial, Helvetica, sans-serif' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Input font size', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[input][font_size]"
					value="<?php echo esc_attr( $email_style['input']['font_size'] ?? '14px' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Input font color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[input][font_color]"
					value="<?php echo esc_attr( $email_style['input']['font_color'] ?? '#333333' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Input font weight', 'influactive-forms' ); ?>
				<select name="influactive_form_email_style[input][font_weight]">
					<option
						value="normal" <?php echo 'normal' === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Normal', 'influactive-forms' ); ?>
					</option>
					<option
						value="bold" <?php echo 'bold' === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Bold', 'influactive-forms' ); ?>
					</option>
					<option
						value="bolder" <?php echo 'bolder' === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Bolder', 'influactive-forms' ); ?>
					</option>
					<option
						value="medium" <?php echo 'medium' === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Medium', 'influactive-forms' ); ?>
					</option>
					<option
						value="lighter" <?php echo 'lighter' === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Lighter', 'influactive-forms' ); ?>
					</option>
				</select>
			</label>
			<label>
				<?php echo esc_html__( 'Input line height', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[input][line_height]"
					value="<?php echo esc_attr( $email_style['input']['line_height'] ?? '1.5' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Input background color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[input][background_color]"
					value="<?php echo esc_attr( $email_style['input']['background_color'] ?? '#ffffff' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Input border width', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[input][border_width]"
					value="<?php echo esc_attr( $email_style['input']['border_width'] ?? '1px' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Input border style', 'influactive-forms' ); ?>
				<select name="influactive_form_email_style[input][border_style]">
					<option
						value="solid" <?php echo 'solid' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Solid', 'influactive-forms' ); ?>
					</option>
					<option
						value="dashed" <?php echo 'dashed' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Dashed', 'influactive-forms' ); ?>
					</option>
					<option
						value="dotted" <?php echo 'dotted' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Dotted', 'influactive-forms' ); ?>
					</option>
					<option
						value="double" <?php echo 'double' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Double', 'influactive-forms' ); ?>
					</option>
					<option
						value="groove" <?php echo 'groove' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Groove', 'influactive-forms' ); ?>
					</option>
					<option
						value="ridge" <?php echo 'ridge' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Ridge', 'influactive-forms' ); ?>
					</option>
					<option
						value="inset" <?php echo 'inset' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Inset', 'influactive-forms' ); ?>
					</option>
					<option
						value="outset" <?php echo 'outset' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Outset', 'influactive-forms' ); ?>
					</option>
					<option
						value="hidden" <?php echo 'hidden' === $email_style['input']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Hidden', 'influactive-forms' ); ?>
					</option>
				</select>
			</label>
			<label>
				<?php echo esc_html__( 'Input border color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[input][border_color]"
					value="<?php echo esc_attr( $email_style['input']['border_color'] ?? '#cccccc' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Input border radius', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[input][border_radius]"
					value="<?php echo esc_attr( $email_style['input']['border_radius'] ?? '0' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Input padding', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[input][padding]"
					value="<?php echo esc_attr( $email_style['input']['padding'] ?? '10px' ); ?>"
				>
			</label>
		</p>
		<p>
			<label>
				<?php echo esc_html__( 'Submit font family', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[submit][font_family]"
					value="<?php echo esc_attr( $email_style['submit']['font_family'] ?? 'Arial, Helvetica, sans-serif' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit font size', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[submit][font_size]"
					value="<?php echo esc_attr( $email_style['submit']['font_size'] ?? '14px' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit font color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[submit][font_color]"
					value="<?php echo esc_attr( $email_style['submit']['font_color'] ?? '#ffffff' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit font hover color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[submit][font_hover_color]"
					value="<?php echo esc_attr( $email_style['submit']['font_hover_color'] ?? '#ffffff' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit font weight', 'influactive-forms' ); ?>
				<select name="influactive_form_email_style[submit][font_weight]">
					<option
						value="normal"
						<?php echo 'normal' === $email_style['submit']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Normal', 'influactive-forms' ); ?>
					</option>
					<option
						value="bold"
						<?php echo 'bold' === $email_style['submit']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Bold', 'influactive-forms' ); ?>
					</option>
					<option
						value="bolder"
						<?php echo 'bolder' === $email_style['submit']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Bolder', 'influactive-forms' ); ?>
					</option>
					<option value="lighter"
						<?php echo 'lighter' === $email_style['submit']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Lighter', 'influactive-forms' ); ?>
					</option>
				</select>
			</label>
			<label>
				<?php echo esc_html__( 'Submit line height', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[submit][line_height]"
					value="<?php echo esc_attr( $email_style['submit']['line_height'] ?? '1.5' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit background color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[submit][background_color]"
					value="<?php echo esc_attr( $email_style['submit']['background_color'] ?? '#333333' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit background hover color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[submit][background_hover_color]"
					value="<?php echo esc_attr( $email_style['submit']['background_hover_color'] ?? '#333333' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit border color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[submit][border_color]"
					value="<?php echo esc_attr( $email_style['submit']['border_color'] ?? '#333333' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit border style', 'influactive-forms' ); ?>
				<select name="influactive_form_email_style[submit][border_style]">
					<option
						value="solid" <?php echo 'solid' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Solid', 'influactive-forms' ); ?>
					</option>
					<option
						value="dashed" <?php echo 'dashed' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Dashed', 'influactive-forms' ); ?>
					</option>
					<option
						value="dotted" <?php echo 'dotted' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Dotted', 'influactive-forms' ); ?>
					</option>
					<option
						value="double" <?php echo 'double' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Double', 'influactive-forms' ); ?>
					</option>
					<option
						value="groove" <?php echo 'groove' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Groove', 'influactive-forms' ); ?>
					</option>
					<option
						value="ridge" <?php echo 'ridge' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Ridge', 'influactive-forms' ); ?>
					</option>
					<option
						value="inset" <?php echo 'inset' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Inset', 'influactive-forms' ); ?>
					</option>
					<option
						value="outset" <?php echo 'outset' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Outset', 'influactive-forms' ); ?>
					</option>
					<option
						value="hidden" <?php echo 'hidden' === $email_style['submit']['border_style'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Hidden', 'influactive-forms' ); ?>
					</option>
				</select>
			</label>
			<label>
				<?php echo esc_html__( 'Submit border width', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[submit][border_width]"
					value="<?php echo esc_attr( $email_style['submit']['border_width'] ?? '1px' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit border radius', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[submit][border_radius]"
					value="<?php echo esc_attr( $email_style['submit']['border_radius'] ?? '0' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Submit padding', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[submit][padding]"
					value="<?php echo esc_attr( $email_style['submit']['padding'] ?? '10px 20px' ); ?>"
				>
			</label>
		</p>
		<p>
			<label>
				<?php echo esc_html__( 'Free text font family', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[free_text][font_family]"
					value="<?php echo esc_attr( $email_style['free_text']['font_family'] ?? 'Arial, Helvetica, sans-serif' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Free text font size', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[free_text][font_size]"
					value="<?php echo esc_attr( $email_style['free_text']['font_size'] ?? '16px' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Free text font weight', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[free_text][font_weight]"
					value="<?php echo esc_attr( $email_style['free_text']['font_weight'] ?? 'normal' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Free text color', 'influactive-forms' ); ?>
				<input
					type="color"
					name="influactive_form_email_style[free_text][color]"
					value="<?php echo esc_attr( $email_style['free_text']['color'] ?? '#333333' ); ?>"
				>
			</label>
			<label>
				<?php echo esc_html__( 'Free text line height', 'influactive-forms' ); ?>
				<input
					type="text"
					name="influactive_form_email_style[free_text][line_height]"
					value="<?php echo esc_attr( $email_style['free_text']['line_height'] ?? '1.5' ); ?>"
				>
			</label>
		</p>
	</div>
	<?php
}
