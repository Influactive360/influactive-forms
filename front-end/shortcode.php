<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Ajouter un shortcode pour chaque post
add_shortcode('influactive_form', 'influactive_form_shortcode_handler');
function influactive_form_shortcode_handler($atts): string
{
    $atts = shortcode_atts(
        array('id' => '0'),
        $atts,
        'influactive_form'
    );

    $form_id = (int)$atts['id'];

    if (!$form_id) {
        return '';
    }

    // Do something with $form_id to display the form
    return "Form Output for ID {$form_id}";
}
