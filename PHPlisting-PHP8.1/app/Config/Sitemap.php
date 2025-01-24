<?php

return [
    'priority' => [
        'index' => '1.0',
        'pages' => '0.9',
        'types' => [
            'index' => '0.9',
            'search' => '0.9',
            'pricing' => '0.9',
        ],
        'listings' => [
            'index' => '0.9',
            'send_message' => '0.5',
            'reviews' => '0.9',
            'add_review' => '0.5',
            'claim' => '0.5',
        ],
        'categories' => '0.8',
        'locations' => '0.7',
    ],
    'changefreq' => [
        'index' => 'weekly',
        'pages' => 'weekly',
        'types' => [
            'index' => 'weekly',
            'search' => 'weekly',
            'pricing' => 'weekly',
        ],
        'listings' => [
            'index' => 'weekly',
            'send_message' => 'weekly',
            'reviews' => 'weekly',
            'add_review' => 'weekly',
            'claim' => 'weekly',
        ],
        'categories' => 'weekly',
        'locations' => 'weekly',
    ],
];
