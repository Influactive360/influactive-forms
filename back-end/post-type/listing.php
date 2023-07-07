<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Ajouter la colonne dans la page de la liste des posts
add_filter('manage_influactive-forms_posts_columns', 'influactive_form_posts_columns');

function influactive_form_posts_columns($columns): array
{
    $columns['shortcode'] = 'Shortcode';

    return $columns;
}

add_action('manage_influactive-forms_posts_custom_column', 'influactive_form_posts_custom_column', 10, 2);

function influactive_form_posts_custom_column($column, $post_id): void
{
    if ($column === 'shortcode') {
        echo '[influactive_form id="' . $post_id . '"]';
    }
}
