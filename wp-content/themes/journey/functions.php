<?php 

/**
 * TODO:
 * - copy custom field code into functions.php
 * - get google maps api from journey google cloud account
 */

/**
 * Helper functions
 */
function get_nav_menu_items_by_location($location) {
    $theme_locations = get_nav_menu_locations();
    $menu_obj = get_term( $theme_locations[$location], 'nav_menu');
    return wp_get_nav_menu_items($menu_obj->ID);

}


function jrny_timber_context( $context ) {
    $context['menus'] = [
        'main' => new \Timber\Menu('main'),
    ];

    return $context;
}

add_filter('timber/context', 'jrny_timber_context');


/**
 * Register support for Gutenberg wide images in your theme
 */
function jrny_site_setup() {
    add_theme_support( 'align-wide' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_image_size( 'featured-card', 1110, 500);
    add_image_size( 'featured-card-2x', 2220, 1000);
    add_image_size( 'card', 500, 500);
    add_image_size( 'card-2x', 1000, 1000);
}

add_action( 'after_setup_theme', 'jrny_site_setup' );


/**
 * Enable SVG support
 */
function jrny_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'jrny_mime_types');

/**
 * Theme CSS / JS
 */
function add_theme_scripts_styles() {
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap-4.5.3-custom.css');
    wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind.css');
    wp_enqueue_style('styles', get_template_directory_uri() . '/assets/css/styles.css'); 
}

add_action('wp_enqueue_scripts', 'add_theme_scripts_styles');

function jrny_enqueue_block_editor_assets() {
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap-4.5.3-custom.css');
    wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind.css');
    wp_enqueue_style('styles', get_template_directory_uri() . '/assets/css/styles.css'); 

}

add_action('enqueue_block_editor_assets', 'jrny_enqueue_block_editor_assets');


function jrny_register_nav_menus() {
    register_nav_menus([
        'main' => 'Main Menu',
        'footer' => 'Footer Menu',
    ]);
}

add_action('init', 'jrny_register_nav_menus');


/**
 * Change the Google Map api key
 */
function my_acf_google_map_api( $api ){
    $api['key'] = get_field('google_maps_api_key', 'option');
    return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

function acf_register_options_page() {
    if( function_exists('acf_add_options_page') ) {
        acf_add_options_page([
            'menu_title' => 'Theme Options',
            'page_title' => 'Theme Options',
            'menu_slug' => 'theme-options',
        ]);
    }
}

add_action( 'init', 'acf_register_options_page' );


function acf_register_local_field_groups() {
    if ( function_exists('acf_add_local_field_group') ) {

        acf_add_local_field_group([
            'key' => 'jrny_theme_options',
            'title' => 'Theme Options',
            'fields' => [
                [
                    'key' => 'google_maps_api_key',
                    'label' => 'Google Maps API Key',
                    'name' => 'google_maps_api_key',
                    'type' => 'text',
                ]
            ],
            'location' => [
                [
                    [
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'theme-options'
                    ]
                ]
            ]
        ]);
    }
}

add_action( 'init', 'acf_register_local_field_groups' );


function my_acf_init_block_types() {
    
    // Check function exists.
    if( function_exists('acf_register_block_type') ) {
        
            // register a testimonial block.
            acf_register_block_type(array(
                'name'              => 'featured-card',
                'title'             => 'Featured Card',
                'description'       => 'A custom featured card block.',
                'render_callback'   => 'timber_render_custom_acf_blocks',
                'category'          => 'formatting',
                'icon'              => 'admin-comments',
            ));

    }
}

add_action('acf/init', 'my_acf_init_block_types');

function timber_render_custom_acf_blocks( $block, $content = '', $is_preview = false ) {
    $context = Timber::context();

    $context['block'] = $block;

    $context['fields'] = get_fields();

    $context['is_preview'] = $is_preview;
    
    $name = explode('acf/', $block['name'])[1];
    
    Timber::render('blocks/' . $name . '.twig', $context);
}


/**
 * Register sermons custom post type
 */
function jrny_sermon_custom_post_type() {
    register_post_type('jrny_sermon',
        [
            'labels'      => [
                'name'                     => 'Sermons',
                'singular_name'            => 'Sermon',
                'add_new'                  => 'Add New',
                'add_new_item'             => 'Add New Sermon',
                'edit_item'                => 'Edit Sermon',
                'new_item'                 => 'New Sermon',
                'view_item'                => 'View Sermon',
                'view_items'               => 'View Sermons',
                'search_items'             => 'Search Sermons',
                'not_found'                => 'No sermons found.',
                'not_found_in_trash'       => 'No sermons found in Trash.',
                'parent_item_colon'        => 'Parent Sermon:',
                'all_items'                => 'All Sermons',
                'archives'                 => 'Sermon Archives',
                'attributes'               => 'Sermon Attributes',
                'insert_into_item'         => 'Insert into sermon',
                'uploaded_to_this_item'    => 'Uploaded to this sermon',
                'featured_image'           => 'Featured image',
                'set_featured_image'       => 'Set featured image',
                'remove_featured_image'    => 'Remove featured image',
                'use_featured_image'       => 'Use as featured image',
                'filter_items_list'        => 'Filter sermons list',
                'items_list_navigation'    => 'Sermons list navigation',
                'items_list'               => 'Sermons list',
                'item_published'           => 'Sermon published.',
                'item_published_privately' => 'Sermon published privately.',
                'item_reverted_to_draft'   => 'Sermon reverted to draft.',
                'item_scheduled'           => 'Sermon scheduled.',
                'item_updated'             => 'Sermon updated.',
            ],
            'public'      => true,
            'has_archive' => true,
            'rewrite' => [ 'slug' => 'sermons' ],
            'supports' => [ 'title', 'thumbnail' ]
        ]
    );
}

add_action('init', 'jrny_sermon_custom_post_type');

/**
 * Register the speakers taxonomy
 */
function jrny_register_taxonomy_speaker() {
    $labels = array(
        'name'              => 'Sermon Speakers',
        'singular_name'     => 'Sermon Speaker',
        'search_items'      => 'Search Sermon Speakers',
        'all_items'         => 'All Sermon Speakers',
        'parent_item'       => 'Parent Sermon Speaker',
        'parent_item_colon' => 'Parent Sermon Speaker:',
        'edit_item'         => 'Edit Sermon Speaker',
        'update_item'       => 'Update Sermon Speaker',
        'add_new_item'      => 'Add New Sermon Speaker',
        'new_item_name'     => 'New Sermon Speaker Name',
    );
    $args   = array(
        'hierarchical'      => false, // make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'meta_box_cb'       => false,
        'public'            => false,
    );
    register_taxonomy( 'jrny_speaker', [ 'jrny_sermon' ], $args );
}
add_action( 'init', 'jrny_register_taxonomy_speaker' );

/**
 * Register the series taxonomy
 */
function jrny_register_taxonomy_series() {
    $labels = array(
        'name'              => 'Sermon Series',
        'singular_name'     => 'Sermon Series',
        'search_items'      => 'Search Sermon Series',
        'all_items'         => 'All Sermon Series',
        'parent_item'       => 'Parent Sermon Series',
        'parent_item_colon' => 'Parent Sermon Series:',
        'edit_item'         => 'Edit Sermon Series',
        'update_item'       => 'Update Sermon Series',
        'add_new_item'      => 'Add New Sermon Series',
        'new_item_name'     => 'New Sermon Series Name',
    );
    $args   = array(
        'hierarchical'      => false, // make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'meta_box_cb'       => false,
        'public'            => false,
    );
    register_taxonomy( 'jrny_series', [ 'jrny_sermon' ], $args );
}
add_action( 'init', 'jrny_register_taxonomy_series' );


/**
 * Register the series taxonomy
 */
function jrny_register_taxonomy_topic() {
    $labels = array(
        'name'              => 'Sermon Topics',
        'singular_name'     => 'Sermon Topics',
        'search_items'      => 'Search Sermon Topics',
        'all_items'         => 'All Sermon Topics',
        'parent_item'       => 'Parent Sermon Topics',
        'parent_item_colon' => 'Parent Sermon Topics:',
        'edit_item'         => 'Edit Sermon Topics',
        'update_item'       => 'Update Sermon Topics',
        'add_new_item'      => 'Add New Sermon Topics',
        'new_item_name'     => 'New Sermon Topics Name',
    );
    $args   = array(
        'hierarchical'      => true, // make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'meta_box_cb'       => false,
        'public'            => false,
    );
    register_taxonomy( 'jrny_topic', [ 'jrny_sermon' ], $args );
}
add_action( 'init', 'jrny_register_taxonomy_topic' );


/**
 * Register sermons custom post type
 */
function jrny_location_custom_post_type() {
    register_post_type('jrny_location',
        [
            'labels'      => [
                'name'                  => 'Locations',
                'singular_name'         => 'Location',
                'menu_name'             => 'Locations',
                'name_admin_bar'        => 'Location',
                'add_new'               => 'Add New',
                'add_new_item'          => 'Add New Location',
                'new_item'              => 'New Location',
                'edit_item'             => 'Edit Location',
                'view_item'             => 'View Location',
                'all_items'             => 'All Locations',
                'search_items'          => 'Search Locations',
                'parent_item_colon'     => 'Parent Locations:',
                'not_found'             => 'No locations found.',
                'not_found_in_trash'    => 'No locations found in Trash.',
                'featured_image'        => 'Location Cover Image',
                'set_featured_image'    => 'Set cover image',
                'remove_featured_image' => 'Remove cover image',
                'use_featured_image'    => 'Use as cover image',
                'archives'              => 'Location archives',
                'insert_into_item'      => 'Insert into location',
                'uploaded_to_this_item' => 'Uploaded to this location',
                'filter_items_list'     => 'Filter locations list',
                'items_list_navigation' => 'Locations list navigation',
                'items_list'            => 'Locations list',
            ],
            'public'      => true,
            'has_archive' => true,
            'rewrite' => [ 'slug' => 'locations' ],
            'supports' => [ 'title', 'thumbnail' ],
        ]
    );
}

add_action('init', 'jrny_location_custom_post_type');