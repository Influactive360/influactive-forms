<?php

if (!defined('ABSPATH')) {
    $possible_paths = [
        '/wp-load.php', // WordPress standard
        '/wordpress/wp-load.php', // WordPlate
        '/wp/wp-load.php', // Radicle
    ];

    // Try to get the document root from the server
    $base_path = $_SERVER['DOCUMENT_ROOT'] ?? '';

    foreach ($possible_paths as $possible_path) {
        if (file_exists($base_path . $possible_path)) {
            require_once($base_path . $possible_path);
            break;
        }
    }
}

header("Content-type: text/css; charset: UTF-8");

$email_style = get_post_meta($_GET['post_id'], '_influactive_form_email_style', true);
$background_color = $email_style['background_color'];
$padding = $email_style['padding'];
$border_width = $email_style['border_width'];
$border_style = $email_style['border_style'];
$border_color = $email_style['border_color'];
?>
form.influactive-form {
padding: <?= $padding ?>;
background: <?= $background_color ?>;
border-width: <?= $border_width ?>;
border-style: <?= $border_style ?>;
border-color: <?= $border_color ?>;
}
