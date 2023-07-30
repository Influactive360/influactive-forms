<?php
/**
 * Description: This file is responsible for displaying the edit screen for the
 * Influactive Form post-type.
 *
 * @throws RuntimeException If the WordPress environment is not loaded.
 * @package Influactive Forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

/**
 * Add the Influactive Form metabox to the post-editor screen.
 *
 * @return void
 */
function influactive_form_add_metaboxes(): void {
	add_meta_box(
		'influactive_form_metabox',
		__( 'Influactive Form', 'influactive-forms' ),
		'influactive_form_metabox',
		'influactive-forms'
	);
}

add_action( 'add_meta_boxes', 'influactive_form_add_metaboxes' );

/**
 * Display the metabox for Influactive Form settings.
 *
 * @param WP_Post $post The current post object.
 *
 * @return void
 * @throws RuntimeException If the WordPress environment is not loaded.
 */
function influactive_form_metabox( WP_Post $post ): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
	}

	influactive_form_shortcode( $post );
	?>
	<div class='"tabs'>
		<ul class='tab-links'>
			<li>
				<a href='#style'>
					<?php echo esc_html__( 'Form Style', 'influactive-forms' ); ?>
				</a>
			</li>
			<li>
				<a href='#email'>
					<?php echo esc_html__( 'Email Layout', 'influactive-forms' ); ?>
				</a>
			</li>
		</ul>

		<div class='tab-content'>
			<div id='fields' class='tab active'>
				<!-- Form fields content -->
				<h2><?php echo esc_html__( 'Form Fields', 'influactive-forms' ); ?></h2>
				<?php influactive_form_fields_listing( $post ); ?>
			</div>
			<div id='style' class='tab'>
				<!-- Email style content -->
				<h2><?php echo esc_html__( 'Form Style', 'influactive-forms' ); ?></h2>
				<?php influactive_form_email_style( $post ); ?>
			</div>
			<div id='email' class='tab'>
				<!-- Email style content -->
				<h2><?php echo esc_html__( 'Email Layout', 'influactive-forms' ); ?></h2>
				<?php influactive_form_email_layout( $post ); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Generate the shortcode for displaying the Influactive Form.
 *
 * @param WP_Post $post The current post object.
 *
 * @return void
 */
function influactive_form_shortcode( WP_Post $post ): void {
	echo '<code>[influactive_form id="' . esc_attr( $post->ID ) . '"]</code>';
}

/**
 * Display the form fields listing for the Influactive form metabox.
 *
 * @param WP_Post $post The current post object.
 *
 * @return void
 */
function influactive_form_fields_listing( WP_Post $post ): void {
	$fields = get_post_meta( $post->ID, '_influactive_form_fields', true );
	echo '<div id="influactive_form_fields_container">';

	if ( is_array( $fields ) ) :
		foreach ( $fields as $key => $field ) :
			?>
			<div class='influactive_form_field'>
				<p>
					<label>Type
						<select
							name="influactive_form_fields[<?php echo (int) $key; ?>][type]"
							class='field_type'
						>
							<option
								value='text'
								<?php
								echo isset( $field['type'] ) && 'text' === $field['type'] ? 'selected' : '';
								?>
							>
								<?php
								echo esc_html__( 'Text', 'influactive-forms' );
								?>
							</option>
							<option
								value="email"
								<?php
								echo isset( $field['type'] ) && 'email' === $field['type'] ? 'selected' : '';
								?>
							>
								<?php
								echo esc_html__( 'Email', 'influactive-forms' );
								?>
							</option>
							<option
								value="number"
								<?php
								echo isset( $field['type'] ) && 'number' === $field['type'] ? 'selected' : '';
								?>
							>
								<?php
								echo esc_html__( 'Number', 'influactive-forms' );
								?>
							</option>
							<option
								value="textarea"
								<?php
								echo isset( $field['type'] ) && 'textarea' === $field['type'] ? 'selected' : '';
								?>
							>
								<?php
								echo esc_html__( 'Textarea', 'influactive-forms' );
								?>
							</option>
							<option
								value="select"
								<?php
								echo isset( $field['type'] ) && 'select' === $field['type'] ? 'selected' : '';
								?>
							>
								<?php
								echo esc_html__( 'Select', 'influactive-forms' );
								?>
							</option>
							<option
								value="gdpr"
								<?php
								echo isset( $field['type'] ) && 'gdpr' === $field['type'] ? 'selected' : '';
								?>
							>
								<?php
								echo esc_html__( 'GDPR', 'influactive-forms' );
								?>
							</option>
							<option
								value="free_text"
								<?php
								echo isset( $field['type'] ) && 'free_text' === $field['type'] ? 'selected' : '';
								?>
							>
								<?php
								echo esc_html__( 'Free text', 'influactive-forms' );
								?>
							</option>
						</select>
					</label>
					<?php
					if ( isset( $field['type'] ) && 'gdpr' === $field['type'] ) :
						?>
						<label>
							<?php
							echo esc_html__( 'Text', 'influactive-forms' );
							?>
							<input
								type="text"
								name="influactive_form_fields[<?php echo (int) $key; ?>][label]"
								value="<?php echo esc_attr( $field['label'] ); ?>"
								class="influactive_form_fields_label"
								required
							>
						</label>
						<label>
							<input
								type="hidden"
								name="influactive_form_fields[<?php echo (int) $key; ?>][name]"
								value="gdpr"
								class="influactive_form_fields_name"
							>
						</label>
					<?php
					elseif ( isset( $field['type'] ) && 'free_text' === $field['type'] ) :
						wp_editor(
							$field['label'],
							'influactive_form_fields_' . $key . '_label',
							array(
								'textarea_name' => 'influactive_form_fields[' . (int) $key . '][label]',
								'textarea_rows' => 10,
								'media_buttons' => false,
								'tinymce'       => array(
									'toolbar1' => 'bold,italic,underline,link,unlink,undo,
                                    redo,formatselect,backcolor,alignleft,aligncenter,alignright,
                                    alignjustify,bullist,numlist,outdent,indent,removeformat',
								),
								'editor_class'  => 'influactive_form_fields_label wysiwyg-editor',
							)
						);
						?>
						<label>
							<input
								type="hidden"
								name="influactive_form_fields[<?php echo (int) $key; ?>][name]"
								value="free_text"
								class="influactive_form_fields_name"
							>
						</label>
					<?php
					elseif ( isset( $field['type'] ) && 'select' === $field['type'] ) :
					?>
					<label>Label
						<input
							type="text"
							name="influactive_form_fields[<?php echo (int) $key; ?>][label]"
							value="<?php echo esc_attr( $field['label'] ); ?>"
							class="influactive_form_fields_label" required
						>
					</label>
					<label>Name
						<input
							type="text"
							name="influactive_form_fields[<?php echo (int) $key; ?>][name]"
							value="<?php echo esc_attr( strtolower( $field['name'] ) ); ?>"
							class="influactive_form_fields_name" required
						>
					</label>
				<div class="options_container">
					<?php
					if ( is_array( $field['options'] ) ) :
						foreach ( $field['options'] as $option_index => $option ) :
							?>
							<p
								class="option-field"
								data-index="<?php echo esc_attr( $option_index ); ?>"
							>
								<label>
									<?php echo esc_html__( 'Option Label', 'influactive-forms' ); ?>
									<input
										type="text"
										class="option-label"
										name="influactive_form_fields[<?php echo (int) $key; ?>][options][<?php echo (int) $option_index; ?>][label]"
										value="<?php echo esc_attr( $option['label'] ); ?>" required
									>
								</label>
								<label>
									<?php echo esc_html__( 'Option Value', 'influactive-forms' ); ?>
									<input
										type="text"
										class="option-value"
										name="influactive_form_fields[<?php echo (int) $key; ?>][options][<?php echo (int) $option_index; ?>][value]"
										value="<?php echo esc_attr( $option['value'] ); ?>" required
									>
								</label>
								<a
									href="#"
									class="remove_option"
								>
									<?php echo esc_html__( 'Remove option', 'influactive-forms' ); ?>
								</a>
							</p>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<p><a href="#"
							class="add_option"><?php echo esc_html__( 'Add option', 'influactive-forms' ); ?></a>
				</p>
				<label>Required
					<input
						type="checkbox"
						name="influactive_form_fields[<?php echo (int) $key; ?>][required]"
						value="1" <?php echo isset( $field['required'] ) && '1' === $field['required'] ? 'checked' : ''; ?>
						class="influactive_form_fields_required"
					>
				</label>
				<?php elseif ( isset( $field['type'] ) ) : ?>
					<label>Label
						<input
							type="text"
							name="influactive_form_fields[<?php echo (int) $key; ?>][label]"
							value="<?php echo esc_attr( $field['label'] ); ?>"
							class="influactive_form_fields_label" required
						>
					</label>
					<label>Name
						<input
							type="text"
							name="influactive_form_fields[<?php echo (int) $key; ?>][name]"
							value="<?php echo esc_attr( strtolower( $field['name'] ) ); ?>"
							class="influactive_form_fields_name" required
						>
					</label>
					<label>Required
						<input
							type="checkbox"
							name="influactive_form_fields[<?php echo (int) $key; ?>][required]"
							value="1" <?php echo isset( $field['required'] ) && '1' === $field['required'] ? 'checked' : ''; ?>
							class="influactive_form_fields_required"
						>
					</label>
				<?php endif; ?>
				<input
					type="hidden"
					name="influactive_form_fields[<?php echo (int) $key; ?>][order]"
					value="<?php echo (int) $key; ?>"
					class="influactive_form_fields_order"
				>
				<a
					href="#"
					class="remove_field"
				>
					<?php echo esc_html__( 'Remove the field', 'influactive-forms' ); ?>
				</a>
			</div>
		<?php
		endforeach;

	endif;

	echo '</div>';

	echo '<p><a href="#" id="add_field">' . esc_html__( 'Add Field', 'influactive-forms' ) . '</a></p>';
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
					value="<?php echo $email_style['form']['background_color'] ?? '#f6f6f6'; ?>"
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
						value="none" <?php echo $email_style['form']['border_style'] === 'none' ? 'selected' : ''; ?>>
						<?php echo esc_html__( 'None', 'influactive-forms' ); ?></option>
					<option
						value="hidden" <?php echo $email_style['form']['border_style']
																			=== 'hidden' ? 'selected' : ''; ?>>
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
						value="normal" <?php echo "normal" === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Normal', 'influactive-forms' ); ?>
					</option>
					<option
						value="bold" <?php echo "bold" === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Bold', 'influactive-forms' ) ?>
					</option>
					<option
						value="bolder" <?php echo "bolder" === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Bolder', 'influactive-forms' ); ?>
					</option>
					<option
						value="medium" <?php echo "medium" === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Medium', 'influactive-forms' ); ?>
					</option>
					<option
						value="lighter" <?php echo "lighter" === $email_style['input']['font_weight'] ? 'selected' : ''; ?>
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
					value="<?php echo esc_attr( $email_style['input']['line_height'] ?? '1.5' ) ?>"
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
						value="normal" <?php echo "normal" === $email_style['submit']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Normal', 'influactive-forms' ); ?>
					</option>
					<option
						value="bold" <?php echo "bold" === $email_style['submit']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Bold', 'influactive-forms' ); ?>
					</option>
					<option
						value="bolder" <?php echo "bolder" === $email_style['submit']['font_weight'] ? 'selected' : ''; ?>
					>
						<?php echo esc_html__( 'Bolder', 'influactive-forms' ); ?>
					</option>
					<option value="lighter"
						<?php echo "lighter" === $email_style['submit']['font_weight'] ? 'selected' : ''; ?>
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

/**
 * Display the email layout settings for Influactive Form.
 *
 * @param WP_Post $post The current post object.
 *
 * @return void
 */
function influactive_form_email_layout( WP_Post $post ): void {
	$email_layout = get_post_meta( $post->ID, '_influactive_form_email_layout', true ) ?? array();

	// List all influactive_form_fields_name like "{field_name}"
	$fields = get_post_meta( $post->ID, '_influactive_form_fields', true ) ?? array();
	?>
	<p>
		<strong><?php echo esc_html__( 'Fields available in the email', 'influactive-forms' ) ?></strong>
	</p>
	<ul>
		<?php foreach ( $fields as $field ) : ?>
			<?php if ( $field['type'] === 'select' ) : ?>
				<li>
					<code>
						{<?php echo strtolower( esc_html( $field['name'] ) ); ?>:label}
					</code>
				</li>
				<li>
					<code>
						{<?php echo strtolower( esc_html( $field['name'] ) ); ?>:value}
					</code>
				</li>
			<?php else : ?>
				<li>
					<code>
						{<?php echo strtolower( esc_html( $field['name'] ) ); ?>}</code>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ( is_plugin_active( 'influactive-forms/functions.php' ) ) : ?>
			<li><code>{brochure}</code></li>
		<?php endif; ?>
	</ul>
	<?php
	if ( count( $email_layout ) === 0 ) {
		$email_layout = array(
			0 => array(
				'sender'    => get_bloginfo( 'admin_email' ),
				'recipient' => get_bloginfo( 'admin_email' ),
				'subject'   => esc_html__( 'New subject', 'influactive-forms' ),
				'message'   => esc_html__( 'New message', 'influactive-forms' ),
			),
		);
	}
	?>
	<div id="layout_container">
		<?php foreach ( $email_layout as $key => $layout ) : ?>
			<div
				id="influactive_form_layout_container_<?php echo esc_html( $key ); ?>"
				class="influactive_form_layout_container"
				data-layout="<?php echo esc_html( $key ); ?>"
			>
				<p>
					<label>
						<?php echo esc_html__( 'Email sender', 'influactive-forms' ); ?>
						<input
							type="text"
							name="influactive_form_email_layout[<?php echo esc_html( $key ); ?>][sender]"
							value="<?php echo esc_attr( $layout['sender'] ?? get_bloginfo( 'admin_email' ) ); ?>"
						>
					</label>
				</p>
				<p>
					<label>
						<?php echo esc_html__( 'Email recipient', 'influactive-forms' ); ?>
						<input
							type="text"
							name="influactive_form_email_layout[<?php echo esc_html( $key );
							?>][recipient]"
							value="<?php echo esc_attr( $layout['recipient'] ?? get_bloginfo( 'admin_email' ) ); ?>"
						>
					</label>
				<p>
					<label>
						<?php echo esc_html__( 'Subject of the email', 'influactive-forms' ); ?>
						<input
							type="text"
							name="influactive_form_email_layout[<?php echo esc_html( $key ); ?>][subject]"
							value="<?php echo esc_attr( $layout['subject'] ?? esc_html__( 'New subject', 'influactive-forms' ) ); ?>"
						>
					</label>
				</p>
				<div>
					<label>
						<?php echo esc_html__( 'Content of the email', 'influactive-forms' ); ?>
						<?php
						$content   = esc_html( $layout['content'] ) ?? esc_html__( 'New message', 'influactive-forms' );
						$editor_id = 'influactive_form_email_editor_' . esc_html( $key );
						$settings  = array(
							'textarea_name' => 'influactive_form_email_layout[' . esc_html( $key ) . '][content]',
							'editor_height' => 425,
						);

						wp_editor( $content, $editor_id, $settings );
						?>
					</label>
				</div>
				<?php if ( $key > 0 ) : ?>
					<button class="delete_layout"
									type="button"><?php echo esc_html__( 'Delete layout', 'influactive-forms' ); ?></button>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<input
		type="hidden"
		id="layoutCount"
		name="layoutCount"
		value="<?php echo count( $email_layout ); ?>"
	>
	<button id="add_new_layout">
		<?php echo esc_html__( 'Add new layout', 'influactive-forms' ); ?>
	</button>
	<?php
}

/**
 * Save the Influactive Form settings when a post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return void
 */
function influactive_form_save_post( int $post_id ): void {
	if ( 'influactive-forms' === get_post_type( $post_id ) ) {
		$fields         = wp_unslash( $_POST['influactive_form_fields'] ?? array() );
		$fields_type    = $fields['type'] ?? array();
		$fields_label   = $fields['label'] ?? array();
		$fields_name    = $fields['name'] ?? array();
		$fields_options = $fields['options'] ?? array();
		$field_order    = $fields['order'] ?? array();
		$email_style    = wp_unslash( $_POST['influactive_form_email_style'] ?? array() );

		foreach ( $fields_name as $i => $field_name ) {
			$options = influactive_sanitize_options( $fields_options[ $field_order[ $i ] ] ?? array() );

			$fields[ $i ] = influactive_create_field(
				$fields_type[ $i ],
				$field_name,
				$field_order[ $i ],
				influactive_sanitize_label( $fields_label[ $i ], $fields[ $i ]['type'] ),
				$options
			);
		}

		update_post_meta( $post_id, '_influactive_form_fields', $fields );
		update_post_meta( $post_id, '_influactive_form_email_style', $email_style );

		$email_layout = ! empty( $_POST['influactive_form_email_layout'] ) ? wp_unslash( $_POST['influactive_form_email_layout'] ) : array();

		foreach ( $email_layout as $key => $layout ) {
			if ( isset( $layout['subject'] ) && is_string( $layout['subject'] ) && is_array( $layout ) ) {
				$layout['subject'] = sanitize_text_field( $layout['subject'] );
			}
			if ( isset( $layout['content'] ) && is_array( $layout ) ) {
				$layout['content'] = wp_kses_post( $layout['content'] );
			}
		}

		update_post_meta( $post_id, '_influactive_form_email_layout', $email_layout );
	}
}

add_action( 'save_post', 'influactive_form_save_post' );

/**
 * Sanitize options array.
 *
 * @param array $field_options The array containing the options to sanitize.
 *
 * @return array The sanitized options array.
 */
function influactive_sanitize_options( array $field_options ): array {
	return array_map(
		static function( $option ) {
			return is_array( $option )
				? array_map( 'sanitize_text_field', $option )
				: sanitize_text_field( $option );
		}, $field_options );
}

/**
 * Create a field for a form.
 *
 * @param string $type    The type of field.
 * @param string $name    The name of the field.
 * @param int    $order   The order of the field.
 * @param string $label   The label for the field.
 * @param array  $options The options for a select field (optional).
 *
 * @return array The created field.
 */
function influactive_create_field( string $type, string $name, int $order, string $label, array $options ): array {
	$field = [
		'type'  => sanitize_text_field( $type ),
		'name'  => strtolower( sanitize_text_field( $name ) ),
		'order' => $order,
		'label' => $label,
	];

	if ( 'select' === $type ) {
		$field['options'] = $options;
	}

	return $field;
}

/**
 * Sanitize a label based on its type.
 *
 * @param string $label The label to sanitize.
 * @param string $type  The type of the label.
 *
 * @return string The sanitized label.
 */
function influactive_sanitize_label( string $label, string $type ): string {
	return 'free_text' === $type
		? wp_kses_post( $label )
		: sanitize_text_field( $label );
}
