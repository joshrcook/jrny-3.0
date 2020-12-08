<?php

$context = Timber::get_context();

if (function_exists('get_field')) {
    $context['title'] = get_field('sermons_archive_title', 'option');
    $context['description'] = get_field('sermons_archive_description', 'option');
}

$context['latest_sermons'] = Timber::get_posts([
    'post_type' => 'jrny_sermon',
    'numberposts' => 6,
    'orderby' => 'meta_value_num',
    'meta_key' => 'date',
]);


Timber::render('archive-jrny_sermon.twig', $context);