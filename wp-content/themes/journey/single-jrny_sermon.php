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


Timber::render('single-jrny_sermon.twig', $context);