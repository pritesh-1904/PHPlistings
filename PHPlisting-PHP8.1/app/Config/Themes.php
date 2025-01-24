<?php

    return [
        'pages' => [
            'custom' => [
                'title' => 'Custom',
                'widgets' => [
                    'header',
                    'custom',
                    'footer',
                ],
            ],

            'type' => [
                'index' => [
                    'title' => '{type_plural}',
                    'widgets' => [
                        'header',
                        'searchbox',
                        'threecolumnteaser',
                        'listings',
                        'categories',
                        'locations',
                        'addaccountteaser',
                        'pricing',
                        'newsletter',
                        'footer',
                    ],
                ],
                'search' => [
                    'title' => 'Search {type_plural}',
                    'widgets' => [
                        'header',
                        'listingsearchresultsheader',
                        'listingsearchresults',
                        'footer',
                    ],
                ],
                'category' => [
                    'title' => '{category}',
                    'widgets' => [
                        'header',
                        'listingsearchresultsheader',
                        'listingsearchresults',
                        'footer',
                    ],
                ],
                'location' => [
                    'title' => '{location}',
                    'widgets' => [
                        'header',
                        'listingsearchresultsheader',
                        'listingsearchresults',
                        'footer',
                    ],
                ],
                'category/location' => [
                    'title' => '{category} in {location}',
                    'widgets' => [
                        'header',
                        'listingsearchresultsheader',
                        'listingsearchresults',
                        'footer',
                    ],
                ],
                'listing' => [
                    'title' => '{listing}',
                    'widgets' => [
                        'header',
                        'listinggalleryslider',
                        'listing',
                        'listingreviews',
                        'footer',
                    ],
                ],
                'listing/send-message' => [
                    'title' => 'Send Message',
                    'widgets' => [
                        'header',
                        'listingheader',
                        'listingsendmessageform',
                        'footer',
                    ],
                ],
                'listing/reviews' => [
                    'title' => 'Listing Reviews',
                    'widgets' => [
                        'header',
                        'listingheader',
                        'listingreviews',
                        'footer',
                    ],
                ],
                'listing/add-review' => [
                    'title' => 'Add Listing Review',
                    'widgets' => [
                        'header',
                        'listingheader',
                        'listingaddreviewform',
                        'footer',
                    ],
                ],
                'listing/claim' => [
                    'title' => 'Claim Your Listing',
                    'widgets' => [
                        'header',
                        'listingheader',
                        'listingclaimform',
                        'footer',
                    ],
                ],
                'pricing' => [
                    'title' => 'Plans & Pricing',
                    'widgets' => [
                        'header',
                        'pricing',
                        'footer',
                    ],
                ],
            ],
        ],
    ];
