<?php
/**
 * Description: Listing of all fields
 *
 * @throws RuntimeException If WordPress environment is not loaded.
 * @package InfluactiveForm
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

/**
 * Renders the form fields listing HTML for a given post.
 *
 * @param WP_Post $post The post object.
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
					<?php elseif ( isset( $field['type'] ) && 'select' === $field['type'] ) : ?>
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
						<?php influactive_container_options( $field, $key ); ?>
						<a href="#" class="add_option">
							<?php echo esc_html__( 'Add option', 'influactive-forms' ); ?>
						</a>
						<label>Required
							<input
								type="checkbox"
								name="influactive_form_fields[<?php echo (int) $key; ?>][required]"
								value="1" <?php echo isset( $field['required'] ) && '1' === $field['required'] ? 'checked' : ''; ?>
								class="influactive_form_fields_required"
							>
						</label>
					<?php elseif ( isset( $field['type'] ) && 'free_text' === $field['type'] ) : ?>
						<?php
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
		<?php endforeach; ?>

	<?php endif; ?>

	<?php echo '</div>'; ?>

	<?php echo '<p><a href="#" id="add_field">' . esc_html__( 'Add Field', 'influactive-forms' ) . '</a></p>'; ?>
	<?php
}

/**
 * Renders the options container HTML for a given field.
 *
 * @param array $field The field options.
 * @param int   $key   The field key.
 *
 * @return void
 */
function influactive_container_options( array $field, int $key ): void {
	ob_start();
	?>
	<div class="options_container">
		<?php foreach ( $field['options'] as $option_index => $option ) : ?>
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
				<a href="#" class="remove_option">
					<?php echo esc_html__( 'Remove option', 'influactive-forms' ); ?>
				</a>
			</p>
		<?php endforeach; ?>
	</div>
	<?php
	$html = ob_get_clean();
	echo esc_html( $html );
}
