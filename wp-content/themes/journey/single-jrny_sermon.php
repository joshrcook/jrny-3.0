<?php

$context = Timber::get_context();

// Get the other series sermons
if (function_exists('get_field')) {
    $series_id = get_field('series', $context['posts'][0]->id);

    $context['series_sermons'] = Timber::get_posts([
        'post_type' => 'jrny_sermon',
        'numberposts' => -1,
        'post__not_in' => [ $context['posts'][0]->id ],
        'tax_query' => [
            [
                'taxonomy' => 'jrny_series',
                'field' => 'term_id',
                'terms' => $series_id,
            ]
        ],
        'meta_key' => 'date',
        'orderby' => 'meta_value_num',
    ]);
}

$post_id = get_the_id();

$post    = Brizy_Editor_Post::get( $post_id );
var_dump($post);
$html = new Brizy_Editor_CompiledHtml( $post->get_compiled_html() );

// the <head> content
// the $headHtml contains all the assets the page needs
$headHtml = apply_filters( 'brizy_content', $html->get_head(), Brizy_Editor_Project::get(), $post->getWpPost() );

// the <body> content
$bodyHtml = apply_filters( 'brizy_content', $html->get_body(), Brizy_Editor_Project::get(), $post->getWpPost() );

var_dump($headHtml);
die();

?>

<?= get_header(); ?>

<?php do_action( 'wp_body_open' ); ?>
<?php do_action( 'brizy_template_content' ); ?>

<?php Timber::render('single-jrny_sermon.twig', $context); ?>

<?= get_footer(); ?>

