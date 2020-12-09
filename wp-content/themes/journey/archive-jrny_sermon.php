<?php

$context = Timber::get_context();

// Set default values
$context['title'] = 'Sermons';
$context['description'] = '';
$context['sermon_groups'] = [];

if (function_exists('get_field')) {
    $context['title'] = get_field('sermons_archive_title', 'option');
    $context['description'] = get_field('sermons_archive_description', 'option');
    $context['topics_section'] = get_field('sermon_archive_topics_section', 'option');
    $sermon_groups = get_field('sermon_archive_groups', 'option');
    foreach($sermon_groups as $group) {
        $sermon_group = [
            'title' => $group['title'],
            'sermons' => [],
        ];
        if($group['type'] == 'category') {
            $sermon_group['sermons'] = Timber::get_posts([
                'post_type' => 'jrny_sermon',
                'meta_key' => 'date',
                'orderby' => 'meta_value_num',
                'tax_query' => [
                    [
                        'taxonomy' => 'jrny_sermon_category',
                        'field' => 'term_id',
                        'terms' => $group['category'],
                    ]
                ],
                'numberposts' => $group['show_at_most'] ?: 6,
            ]);
        }
        if($group['type'] == 'latest') {
            $sermon_group['sermons'] = Timber::get_posts([
                'post_type' => 'jrny_sermon',
                'meta_key' => 'date',
                'orderby' => 'meta_value_num',
                'numberposts' => $group['show_at_most'] ?: 6,
            ]);
        }
        $context['sermon_groups'][] = $sermon_group;
    }
}


Timber::render('archive-jrny_sermon.twig', $context);