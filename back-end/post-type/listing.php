<?php
/**
 * @package Influactive Forms
 */

if (! defined('ABSPATH')) {
    throw new RuntimeException("WordPress environment not loaded. Exiting...");
}

/**
 * Modifies the columns for the Influactive Form post-type.
 *
 * @param array $columns An array of the current columns.
 *
 * @return array Modified array of columns with 'shortcode' column added.
 */
function influactive_form_posts_columns(array $columns): array
{
    $columns['shortcode'] = 'Shortcode';

    return $columns;
}

add_filter('manage_influactive-forms_posts_columns', 'influactive_form_posts_columns');

/**
 * Displays the custom column content for the Influactive Form post-type.
 *
 * @param string $column The name of the custom column.
 * @param int $post_id The ID of the current post.
 *
 * @return void
 */
function influactive_form_posts_custom_column(string $column, int $post_id): void
{
    if ($column === 'shortcode') {
        echo '[influactive_form id="' . $post_id . '"]';
    }
}

add_action('manage_influactive-forms_posts_custom_column', 'influactive_form_posts_custom_column', 10, 2);
