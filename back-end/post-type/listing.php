<?php
/**
 * Description: This file is responsible for the listing of Forms by Influactive
 * in the admin.
 *
 * @throws RuntimeException If the WordPress environment is not loaded.
 * @package Forms by Influactive
 */

if ( ! defined( 'ABSPATH' ) ) {
	throw new RuntimeException( 'WordPress environment not loaded. Exiting...' );
}

/**
 * Modifies the columns for the Influactive Form post-type.
 *
 * @param array $columns An array of the current columns.
 *
 * @return array Modified array of columns with 'shortcode' column added.
 */
function influactive_form_posts_columns( array $columns ): array {
	$columns['shortcode'] = 'Shortcode';

	return $columns;
}

add_filter( 'manage_influactive-forms_posts_columns', 'influactive_form_posts_columns' );

/**
 * Generates a custom column value for the "influactive_form" post type.
 *
 * @param string $column The name of the column.
 * @param int $post_id The ID of the post.
 *
 * @return void
 */
function influactive_form_posts_custom_column( string $column, int $post_id ): void {
	if ( 'shortcode' === $column ) {
		echo '[influactive_form id="' . esc_attr( $post_id ) . '"]';
	}
}

add_action( 'manage_influactive-forms_posts_custom_column', 'influactive_form_posts_custom_column', 10, 2 );
