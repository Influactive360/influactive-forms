<?php
/**
 * Description: This file is responsible for displaying the edit screen for the
 * Influactive Form post-type.
 *
 * @throws RuntimeException If WordPress environment is not loaded.
 * @package Forms by Influactive
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

/**
 * Display the email layout settings for Influactive Form.
 *
 * @param WP_Post $post The current post object.
 *
 * @return void
 */
function influactive_form_email_layout( WP_Post $post ): void {
	$email_layout = get_post_meta( $post->ID, '_influactive_form_email_layout', true );
	$fields       = get_post_meta( $post->ID, '_influactive_form_fields', true ) ?? array();
	if ( ! is_array( $fields ) && ! is_object( $fields ) ) {
		$fields = array(
			0 => array(
				'name'  => 'Name',
				'type'  => 'text',
				'label' => 'Name',
			),
			1 => array(
				'name'  => 'Email',
				'type'  => 'email',
				'label' => 'Email',
			),
			2 => array(
				'name'  => 'Message',
				'type'  => 'textarea',
				'label' => 'Message',
			),
		);
	}
	if ( ! is_array( $email_layout ) && ! is_object( $email_layout ) ) {
		$email_layout = array(
			0 => array(
				'sender'    => get_bloginfo( 'admin_email' ),
				'recipient' => get_bloginfo( 'admin_email' ),
				'subject'   => esc_html__( 'New subject', 'influactive-forms' ),
				'content'   => esc_html__( 'New message', 'influactive-forms' ),
			),
		);
	}
	?>
	<p>
		<strong><?php echo esc_html__( 'Fields available in the email', 'influactive-forms' ); ?></strong>
	</p>
	<ul>
		<?php foreach ( $fields as $field ) : ?>
			<?php if ( 'select' === $field['type'] ) : ?>
				<li>
					<code>
						{<?php echo esc_html( strtolower( $field['name'] ) ); ?>:label}
					</code>
				</li>
				<li>
					<code>
						{<?php echo esc_html( strtolower( $field['name'] ) ); ?>:value}
					</code>
				</li>
			<?php else : ?>
				<li>
					<code>
						{<?php echo esc_html( strtolower( $field['name'] ) ); ?>}</code>
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
				'content'   => esc_html__( 'New message', 'influactive-forms' ),
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
							name="influactive_form_email_layout[<?php echo esc_html( $key ); ?>][recipient]"
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
						$content   = $layout['content'] ?? esc_html__( 'New message', 'influactive-forms' );
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
					<button
						class="delete_layout"
						type="button"
					>
						<?php echo esc_html__( 'Delete layout', 'influactive-forms' ); ?>
					</button>
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
