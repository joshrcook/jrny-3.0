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

    if (function_exists('get_fields')) {
        $context['options'] = get_fields('option');
    }

    $context['widgets'] = [
        'footer_menus' =>  Timber::get_widgets('footer_menus'),
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
    add_theme_support( 'editor-styles' );
    add_image_size( 'featured-card', 1110, 500);
    add_image_size( 'featured-card-2x', 2220, 1000);
    add_image_size( 'card', 500, 500);
    add_image_size( 'card-2x', 1000, 1000);
}

add_action( 'after_setup_theme', 'jrny_site_setup' );


/**
 * Add editor styles
 */
function jrny_add_editor_styles() {
    add_editor_style('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    add_editor_style( get_template_directory_uri() . '/assets/css/bootstrap-4.5.3-custom.css');
    add_editor_style( get_template_directory_uri() . '/assets/css/tailwind.css');
    add_editor_style( get_template_directory_uri() . '/assets/css/styles.css'); 
    add_editor_style(get_template_directory_uri() . '/assets/css/editor-styles.css');
}

add_action('admin_init', 'jrny_add_editor_styles');



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
    wp_enqueue_style('slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap-4.5.3-custom.css');
    wp_enqueue_style('tailwind', get_template_directory_uri() . '/assets/css/tailwind.css');
    wp_enqueue_style('styles', get_template_directory_uri() . '/assets/css/styles.css'); 

    wp_enqueue_script('slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], null, true);
    wp_enqueue_script('index', get_template_directory_uri() . '/assets/js/index.js', ['jquery', 'slick'], null, true);
}

add_action('wp_enqueue_scripts', 'add_theme_scripts_styles');


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

    if( function_exists('acf_add_options_page') ) {
    
        acf_add_options_sub_page(array(
            'page_title'     => 'Sermons Archive Options',
            'menu_title'    => 'Sermons Archive Options',
            'parent_slug'    => 'edit.php?post_type=jrny_sermon',
        ));
    
    }
}

add_action( 'init', 'acf_register_options_page' );


function my_acf_init_block_types() {
    
    // Check function exists.
    if( function_exists('acf_register_block_type') ) {
        
            // Register the featured card block
            acf_register_block_type(array(
                'name'              => 'featured-card-group',
                'title'             => 'Featured Card Group',
                'description'       => 'A custom editor block that displays featured cards',
                'render_callback'   => 'timber_render_custom_acf_blocks',
                'category'          => 'formatting',
                'icon'              => 'admin-comments',
                'supports'          => [
                    'align' => false,
                    'jsx' => true,
                ],
                'allowedBlocks' => esc_attr(wp_json_encode(['acf/featured-card'])),
            ));

            acf_register_block_type([
                'name' => 'featured-card',
                'title' => 'Featured Card',
                'description' => '',
                'render_callback' => 'timber_render_custom_acf_blocks',
                'category' => 'formatting',
                'icon' => 'admin-comments',
                'supports' => [
                    'align' => false,
                ],
            ]);

            acf_register_block_type([
                'name' => 'standard-card-group',
                'title' => 'Standard Card Group',
                'description' => 'A group of cards',
                'render_callback' => 'timber_render_custom_acf_blocks',
                'category' => 'formatting',
                'icon' => 'admin-comments',
                'supports' => [
                    'align' => false,
                    'jsx' => true,
                ],
                'allowedBlocks' => esc_attr(wp_json_encode(['acf/standard-card'])),
            ]);

            acf_register_block_type([
                'name' => 'section',
                'title' => 'Section',
                'description' => '',
                'render_callback' => 'timber_render_custom_acf_blocks',
                'category' => 'formatting',
                'icon' => 'admin-comments',
                'supports' => [
                    'align' => false,
                    'jsx' => true,
                ],
            ]);

            acf_register_block_type(array(
                'name'              => 'menu-group',
                'title'             => 'Menu Group',
                'description'       => 'A custom menu list block.',
                'render_callback'   => 'timber_render_custom_acf_blocks',
                'category'          => 'formatting',
                'icon'              => 'admin-comments',
                'supports'          => [
                    'align' => false,
                ],
            ));

            // Register the columns block
            // acf_register_block_type(array(
            //     'name'              => 'featured-card',
            //     'title'             => 'Featured Card',
            //     'description'       => 'A custom featured card block.',
            //     'render_callback'   => 'timber_render_custom_acf_blocks',
            //     'category'          => 'formatting',
            //     'icon'              => 'admin-comments',
            //     'supports'          => [
            //         'align' => false,
            //     ],
            // ));

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
 * Register our sidebars and widgetized areas.
 *
 */
function arphabet_widgets_init() {

	register_sidebar( array(
		'name'          => 'Footer Menus',
		'id'            => 'footer_menus',
	) );

}
add_action( 'widgets_init', 'arphabet_widgets_init' );


function wporg_block_wrapper($block_content, $block) {
    if ($block['blockName'] == 'core/columns') {
        $block_content = '<div>' . $block_content . '</div>';
    }
    return $block_content;
}

add_filter( 'render_block', 'wporg_block_wrapper', 10, 2 );

function jrny_allowed_block_types( $allowed_block_types, $post ) {
    if ( $post->post_type !== 'post' ) {
        return $allowed_block_types;
    }
    return array( 'core/paragraph' );
}
 
add_filter( 'allowed_block_types', 'jrny_allowed_block_types', 10, 2 );


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
        'name'              => 'Sermon Categories',
        'singular_name'     => 'Sermon Category',
        'search_items'      => 'Search Sermon Categories',
        'all_items'         => 'All Sermon Categories',
        'parent_item'       => 'Parent Sermon Category',
        'parent_item_colon' => 'Parent Sermon Category:',
        'edit_item'         => 'Edit Sermon Category',
        'update_item'       => 'Update Sermon Category',
        'add_new_item'      => 'Add New Sermon Category',
        'new_item_name'     => 'New Sermon Category Name',
    );
    $args   = array(
        'hierarchical'      => true, // make it hierarchical (like categories)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'meta_box_cb'       => false,
        'public'            => false,
    );
    register_taxonomy( 'jrny_sermon_category', [ 'jrny_sermon' ], $args );
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

/**
 * Filter Youtube embeds to remove extra info
 */
function jrny_filter_youtube_embed() {

}

add_filter('oembed_result', 'jrny_filter_youtube_embed');