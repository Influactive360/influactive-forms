<?php
/**
 * Description: This file is responsible for displaying the edit screen for the
 * Influactive Form post-type.
 *
 * @throws RuntimeException If the WordPress environment is not loaded.
 * @package Forms by Influactive
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

require_once( plugin_dir_path( __FILE__ ) . 'form/influactive-form-email-layout.php' );
require_once( plugin_dir_path( __FILE__ ) . 'form/influactive-form-email-style.php' );
require_once( plugin_dir_path( __FILE__ ) . 'form/influactive-form-fields-listing.php' );

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
	wp_nonce_field( 'influactive_form_save_post', 'influactive_form_save_post' );
	?>
	<div class='tabs'>
		<ul class='tab-links'>
			<li>
				<a href='#fields'>
					<?php echo esc_html__( 'Form Fields', 'influactive-forms' ); ?>
				</a>
			</li>
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
 * Save the Influactive Form settings when a post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 *
 * @return void
 */
function influactive_form_save_post( int $post_id ): void {
	$data = wp_unslash( $_POST );

	if ( ! isset( $data['post_type'] ) || 'influactive-forms' !== sanitize_text_field( wp_unslash( $data['post_type'] ) ) ) {
		return;
	}

	if ( ! isset( $data['influactive_form_save_post'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $data['influactive_form_save_post'] ) ), 'influactive_form_save_post' ) ) {
		return;
	}
	if ( isset( $data['influactive_form_fields'] ) && is_array( $data['influactive_form_fields'] ) ) {
		$unslashed_fields = $data['influactive_form_fields'];
		$fields           = array_map(
			static function ( $field ) {
				return array(
					'type'     => sanitize_text_field( $field['type'] ),
					'label'    => wp_kses_post( $field['label'] ),
					'name'     => sanitize_text_field( $field['name'] ),
					'options'  => ( isset( $field['options'] ) && is_array( $field['options'] ) ) ?
						array_map(
							static function ( $option ) {
								return array_map( 'sanitize_text_field', $option );
							},
							$field['options']
						) : null,
					'required' => sanitize_text_field( $field['required'] ),
					'order'    => sanitize_text_field( $field['order'] ),
				);
			},
			$unslashed_fields
		);
	}

	if ( isset( $data['influactive_form_email_style'] ) && is_array( $data['influactive_form_email_style'] ) ) {
		$unslashed_fields = $data['influactive_form_email_style'];
		$form_email_style = array();

		foreach ( $unslashed_fields as $group_name => $group ) {
			if ( ! is_array( $group ) ) {
				continue;
			}

			foreach ( $group as $property => $value ) {
				$form_email_style[ $group_name ][ $property ] = sanitize_text_field( $value );
			}
		}
	}
	if ( isset( $data['influactive_form_email_layout'] ) && is_array( $data['influactive_form_email_layout'] ) ) {
		$values = array();
		foreach ( $data['influactive_form_email_layout'] as $index => $fields_array ) {
			if ( is_array( $fields_array ) ) {
				$values[ $index ] = $fields_array;
				foreach ( $fields_array as $key => $value ) {
					if ( isset( $data['influactive_form_email_layout'][ $index ][ $key ] ) && is_array( $data['influactive_form_email_layout'][ $index ] ) && $data['influactive_form_email_layout'][ $index ][ $key ] === $value ) {
						$values[ $index ][ $key ] = wp_kses_post( $value );
					}
				}
			}
		}
		$unslashed_fields  = $values;
		$form_email_layout = $unslashed_fields;
	}

	if ( isset( $data, $fields, $form_email_style, $form_email_layout ) && 'influactive-forms' === get_post_type( $post_id ) ) {
		$fields_type    = $fields['type'];
		$fields_label   = $fields['label'];
		$fields_name    = $fields['name'];
		$fields_options = $fields['options'];
		$field_order    = $fields['order'];

		foreach ( $fields_name as $i => $field_name ) {
			$options      = isset( $fields_options[ $field_order[ $i ] ] ) ? array_map( 'sanitize_text_field', $fields_options[ $field_order[ $i ] ] ) : array();
			$fields[ $i ] = influactive_create_field(
				$fields_type[ $i ],
				$field_name,
				$field_order[ $i ],
				influactive_sanitize_label( $fields_label[ $i ], $fields[ $i ]['type'] ),
				$options
			);
		}
		update_post_meta( $post_id, '_influactive_form_fields', $fields );
		update_post_meta( $post_id, '_influactive_form_email_style', $form_email_style );
		update_post_meta( $post_id, '_influactive_form_email_layout', $form_email_layout );
	}
}

add_action( 'save_post', 'influactive_form_save_post' );

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
	$field = array(
		'type'  => sanitize_text_field( $type ),
		'name'  => sanitize_text_field( strtolower( $name ) ),
		'order' => $order,
		'label' => $label,
	);

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
