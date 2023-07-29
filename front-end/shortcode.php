<?php
/**
 * Description: This file contains the shortcode handler for the
 * 'influactive_form' shortcode.
 *
 * @package Influactive Forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( "WordPress environment not loaded. Exiting..." );
}

/**
 * Registers the 'influactive_form' shortcode.
 */
function register_influactive_form_shortcode(): void {
	add_shortcode( 'influactive_form', 'influactive_form_shortcode_handler' );
}

add_action( 'init', 'register_influactive_form_shortcode', 1 );

/**
 * Sends an email containing the form data.
 *
 * @param array $atts An array of attributes passed to the shortcode handler.
 *
 * @return bool|string Returns false if the form ID is not provided. Returns
 *     the form HTML content otherwise.
 *
 */
function influactive_form_shortcode_handler( array $atts ): bool|string {
	ob_start(); // Start output buffering

	$atts = shortcode_atts(
		[ 'id' => '0' ],
		$atts,
		'influactive_form'
	);

	$form_id = (int) $atts['id'];

	if ( ! $form_id ) {
		throw new RuntimeException( "Form ID not found. Exiting..." );
	}

	// Showing the form if it exists
	$form = get_post( $form_id );

	if ( $form ) {
		update_post_meta( get_the_ID(), 'influactive_form_id', $form_id );

		$fields = get_post_meta( $form_id, '_influactive_form_fields', TRUE ) ?? [];

		echo '<div class="influactive-form-wrapper">';

		echo '<form id="influactive-form-' . $form_id . '" class="influactive-form">';

		wp_nonce_field( 'influactive_send_email', 'nonce' );

		echo '<input type="hidden" name="form_id" value="' . $form_id . '">';

		$options_captcha = get_option( 'influactive-forms-captcha-fields' ) ?? [];
		$public_site_key = $options_captcha['google-captcha']['public-site-key'] ?? '';
		$secret_site_key = $options_captcha['google-captcha']['secret-site-key'] ?? '';

		if ( ! empty( $public_site_key ) && ! empty( $secret_site_key ) ) {
			echo '<input type="hidden" id="recaptchaResponse-' . $form_id . '" name="recaptcha_response">';
			echo '<input type="hidden" 
            id="recaptchaSiteKey-' . $form_id . '" 
            name="recaptcha_site_key" 
            value="' . $public_site_key . '">';
		}

		if ( is_plugin_active( 'influactive-forms/functions.php' ) && get_option( 'modal_form_select' ) ) {
			echo '<input type="hidden" name="brochure" value="' . get_option( 'modal_form_file_select' ) . '">';
		}

		foreach ( $fields as $field ) {
			if ( isset( $field['required'] ) && $field['required'] === '1' ) {
				$required = 'required';
			} else {
				$required = '';
			}

			switch ( $field['type'] ) {
				case 'text':
					echo '<label>' .
					     $field['label']
					     . ': <input type="text" ' . $required . ' name="' . esc_attr( $field['name'] ) . '"></label>';
					break;
				case 'email':
					echo '<label>' .
					     $field['label']
					     . ': <input type="email" ' .
					     $required
					     . ' name="' . esc_attr( $field['name'] ) . '" autocomplete="email"></label>';
					break;
				case 'number':
					echo '<label>' .
					     $field['label']
					     . ': <input type="number" ' . $required . ' name="' . esc_attr( $field['name'] ) . '"></label>';
					break;
				case 'textarea':
					echo '<label>' .
					     $field['label']
					     . ': <textarea ' .
					     $required
					     . ' name="' . esc_attr( $field['name'] ) . '" rows="10"></textarea></label>';
					break;
				case 'select':
					echo '<label>' .
					     $field['label']
					     . ': <select ' . $required . ' name="' . esc_attr( $field['name'] ) . '">';
					foreach ( $field['options'] as $option ) {
						echo '<option value="' .
						     esc_attr( $option['value'] )
						     . ':' . esc_attr( $option['label'] ) . '">' . esc_attr( $option['label'] ) . '</option>';
					}
					echo '</select></label>';
					break;
				case 'gdpr':
					$pp = get_privacy_policy_url() ? '<a href="' . get_privacy_policy_url() . '" 
                    target="_blank" title="Privacy Policy">' .
					                                 __( 'Check our Privacy Policy', 'influactive-forms' )
					                                 . '</a>' : '';
					echo '<label>
						<input type="checkbox" name="' . esc_attr( $field['name'] ) . '" required>
						 ' . $field['label'] . ' ' . $pp . '
						</label>';
					break;
				case 'free_text':
					echo '<div class="free-text">' . $field['label'] . '</div>';
					echo '<input type="hidden" 
                    name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $field['label'] ) . '">';
					break;
			}
		}

		echo '<input type="submit">';

		echo '<div class="influactive-form-message"></div>';
		echo '</form>';
		echo '</div>';
	}

	return ob_get_clean(); // End output buffering and return buffered output
}

/**
 * Enqueues the dynamic style file for a specific form.
 */
function enqueue_form_dynamic_style(): void {
	if ( is_admin() ) {
		throw new RuntimeException( "WordPress environment not loaded. Exiting..." );
	}

	$form_id = get_post_meta( get_the_ID(), 'influactive_form_id', TRUE ) ?? 0;
	if ( ! $form_id ) {
		throw new RuntimeException( "Form ID not found. Exiting..." );
	}

	// Enqueue du fichier dynamic-style.php
	wp_enqueue_style(
		'influactive-form-dynamic-style',
		plugin_dir_url( __FILE__ ) . '/dynamic-style.php?post_id=' . $form_id,
		[],
		'1.2.6'
	);
}

add_action( 'wp_enqueue_scripts', 'enqueue_form_dynamic_style' );

/**
 * Sends an email based on the submitted form data.
 *
 * @return void
 */
function influactive_send_email(): void {
	$_POST = array_map( 'sanitize_text_field', $_POST );

	// Check if our nonce is set and verify it.
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'influactive_send_email' ) ) {
		wp_send_json_error( [ 'message' => __( 'Nonce verification failed', 'influactive-forms' ) ] );

		exit;
	}

	// Check form fields
	if ( empty( $_POST['form_id'] ) ) {
		wp_send_json_error( [ 'message' => __( 'Form ID is required', 'influactive-forms' ) ] );

		exit;
	}

	$form_id = (int) $_POST['form_id'];

	// Get form fields
	$fields = get_post_meta( $form_id, '_influactive_form_fields', TRUE ) ?? [];

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field['name'] ] ) && empty( $_POST[ $field['name'] ] ) && $field['required'] === '1' ) {
			$name = $field['name'];
			/* translators: %s is a placeholder for the field name */
			$message = sprintf( __( 'The field %s is required', 'influactive-forms' ), $name );
			wp_send_json_error( [ 'message' => $message ] );

			exit;
		}
	}

	// Get email layout
	$email_layout = get_post_meta( $form_id, '_influactive_form_email_layout', TRUE ) ?? [];
	$sitename     = get_bloginfo( 'name' );

	$options_captcha = get_option( 'influactive-forms-captcha-fields' ) ?? [];
	$secret_site_key = $options_captcha['google-captcha']['secret-site-key'] ?? '';
	$public_site_key = $_POST['recaptcha_site_key'] ?? '';

	if ( ! empty( $secret_site_key ) && ! empty( $public_site_key ) && isset( $_POST['recaptcha_response'] ) ) {
		$recaptcha_url      = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptcha_response = $_POST['recaptcha_response'];

		$url = $recaptcha_url
		       . '?secret=' .
		       urlencode( $secret_site_key )
		       . '&response=' . urlencode( $recaptcha_response );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		try {
			$response = curl_exec( $ch );
			if ( curl_errno( $ch ) ) {
				throw new RuntimeException( curl_error( $ch ) );
			}
		}
		catch ( RuntimeException $e ) {
			wp_send_json_error( [
				'message' => __( 'Failed to verify reCAPTCHA', 'influactive-forms' ),
				'error'   => $e->getMessage(),
			] );
			curl_close( $ch );
			exit;
		}
		curl_close( $ch );

		try {
			$recaptcha = json_decode( $response, FALSE, 512, JSON_THROW_ON_ERROR );

			if ( $recaptcha->score < 0.5 ) {
				// Not likely to be a human
				wp_send_json_error( [
					'message' => __( 'Bot detected', 'influactive-forms' ),
					'score'   => $recaptcha->score,
				] );

				exit;
			}
		}
		catch ( JsonException $e ) {
			wp_send_json_error( [
				'message' => __( 'Failed to verify reCAPTCHA', 'influactive-forms' ),
				'error'   => $e->getMessage(),
			] );

			exit;
		}
	}

	$layouts = $email_layout ?? [];
	$error   = 0;
	foreach ( $layouts as $layout ) {
		$content      = $layout['content'] ?? '';
		$subject      = $layout['subject'] ?? '';
		$to           = $layout['recipient'] ?? get_bloginfo( 'admin_email' );
		$from         = $layout['sender'] ?? get_bloginfo( 'admin_email' );
		$allowed_html = [
			'br'         => [],
			'p'          => [],
			'a'          => [
				'href'   => [],
				'title'  => [],
				'target' => [],
			],
			'h1'         => [],
			'h2'         => [],
			'h3'         => [],
			'h4'         => [],
			'h5'         => [],
			'h6'         => [],
			'strong'     => [],
			'em'         => [],
			'ul'         => [],
			'ol'         => [],
			'li'         => [],
			'blockquote' => [],
			'pre'        => [],
			'code'       => [],
			'img'        => [
				'src' => [],
				'alt' => [],
			],
		];

		foreach ( $fields as $field ) {
			// Convert textarea newlines to HTML breaks
			if ( $field['type'] === 'textarea' ) {
				$_POST[ $field['name'] ] = nl2br( $_POST[ $field['name'] ] );
				$content                 = str_replace(
					'{' . $field['name'] . '}',
					wp_kses( $_POST[ $field['name'] ], $allowed_html ),
					$content
				);
				$subject                 = str_replace(
					'{' . $field['name'] . '}',
					wp_kses( $_POST[ $field['name'] ], $allowed_html ),
					$subject
				);
				$to                      = str_replace(
					'{' . $field['name'] . '}',
					wp_kses( $_POST[ $field['name'] ], $allowed_html ),
					$to
				);
				$from                    = str_replace(
					'{' . $field['name'] . '}',
					wp_kses( $_POST[ $field['name'] ], $allowed_html ),
					$from
				);
			} elseif ( $field['type'] === 'select' ) {
				$content = replace_field_placeholder( $content, $field['name'], explode( ':', $_POST[ $field['name'] ] ) );
				$subject = replace_field_placeholder( $subject, $field['name'], explode( ':', $_POST[ $field['name'] ] ) );
				$to      = replace_field_placeholder( $to, $field['name'], explode( ':', $_POST[ $field['name'] ] ) );
				$from    = replace_field_placeholder( $from, $field['name'], explode( ':', $_POST[ $field['name'] ] ) );
			} elseif ( $field['type'] === 'email' ) {
				$content = str_replace( '{' . $field['name'] . '}', sanitize_email( $_POST[ $field['name'] ] ), $content );
				$subject = str_replace( '{' . $field['name'] . '}', sanitize_email( $_POST[ $field['name'] ] ), $subject );
				$to      = str_replace( '{' . $field['name'] . '}', sanitize_email( $_POST[ $field['name'] ] ), $to );
				$from    = str_replace( '{' . $field['name'] . '}', sanitize_email( $_POST[ $field['name'] ] ), $from );
			} else {
				$content = str_replace(
					'{' . $field['name'] . '}',
					sanitize_text_field( $_POST[ $field['name'] ] ),
					$content
				);
				$subject = str_replace(
					'{' . $field['name'] . '}',
					sanitize_text_field( $_POST[ $field['name'] ] ),
					$subject
				);
				$to      = str_replace(
					'{' . $field['name'] . '}',
					sanitize_text_field( $_POST[ $field['name'] ] ),
					$to
				);
				$from    = str_replace(
					'{' . $field['name'] . '}',
					sanitize_text_field( $_POST[ $field['name'] ] ),
					$from
				);
			}
		}

		if ( isset( $_POST['brochure'] )
		     && is_plugin_active( 'influactive-forms/functions.php' ) && get_option( 'modal_form_select' ) ) {
			$relative_url  = wp_get_attachment_url( $_POST['brochure'] );
			$file_url      = home_url( $relative_url );
			$download_link = sprintf(
			/* translators: %s is a placeholder for the file URL */
				__(
					"<a href='%s' target='_blank' title='Download our brochure'>Download our brochure</a>",
					'influactive-forms'
				),
				$file_url
			);
			$content       = str_replace( '{brochure}', $download_link, $content );
		}

		$from = sanitize_email( $from );
		$to   = sanitize_email( $to );

		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $sitename . ' <' . $from . '>',
			'Reply-To: ' . $from,
		];

		if ( ! wp_mail( $to, $subject, $content, $headers ) ) {
			$error ++;
		}
	}

	if ( $error === 0 ) {
		wp_send_json_success( [
			'message' => __( 'Email sent successfully', 'influactive-forms' ),
		] );
	} else {
		wp_send_json_error( [
			'message' => __( 'Failed to send email', 'influactive-forms' ),
		] );

		exit;
	}

	exit;
}

add_action( 'wp_ajax_send_email', 'influactive_send_email' );
add_action( 'wp_ajax_nopriv_send_email', 'influactive_send_email' );

/**
 * Replaces field placeholders in a string with the corresponding label and
 * value.
 *
 * @param string $string The string to replace placeholders in.
 * @param string $field_name The name of the field.
 * @param array $label_value An array containing the label and value of the
 *     field.
 *
 * @return string The string with replaced placeholders.
 */
function replace_field_placeholder( string $string, string $field_name, array $label_value ): string {
	// Replace label placeholder if it exists
	if ( str_contains( $string, '{' . $field_name . ':label}' ) ) {
		$string = str_replace( '{' . $field_name . ':label}', $label_value[1], $string );
	}

	// Replace value placeholder if it exists
	if ( str_contains( $string, '{' . $field_name . ':value}' ) ) {
		$string = str_replace( '{' . $field_name . ':value}', $label_value[0], $string );
	}

	return $string;
}
