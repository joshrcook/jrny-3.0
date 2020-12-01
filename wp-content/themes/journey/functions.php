<?php 

/**
 * Theme CSS / JS
 */
function add_theme_scripts_styles() {
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap-4.5.3-custom.css');
    wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind.css');
}

add_action('wp_enqueue_scripts', 'add_theme_scripts_styles');

function jrny_register_nav_menus() {
    register_nav_menus([
        'main' => 'Main Menu',
        'footer' => 'Footer Menu',
    ]);
}

add_action('init', 'jrny_register_nav_menus');