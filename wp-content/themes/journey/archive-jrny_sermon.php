<?php

$context = Timber::get_context();

if (function_exists('get_field')) {
    $context['title'] = get_field('sermons_archive_title', 'option');
    $context['description'] = get_field('sermons_archive_description', 'option');
}


Timber::render('archive-jrny_sermon.twig', $context);