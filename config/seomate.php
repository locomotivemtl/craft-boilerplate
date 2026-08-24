<?php

use craft\helpers\App;

return [
    // General Configuration
    'cacheEnabled' => App::env('SEOMATE_ENABLE_CACHING') ?? true,
    'includeSitenameInTitle' => true,
    'sitenamePosition' => 'after',
    'sitenameSeparator' => '|',
    'previewEnabled' => false,

    // SEO Profiles
    'defaultProfile' => 'standard',
    'profileMap' => [],
    'fieldProfiles' => [
        'standard' => [
            'title' => ['seo.seoTitle', 'title'],
            'description' => ['seo.seoDescription', 'description'],
            'image' => ['seo.seoImage', 'image'],
        ],
    ],

    // Default metadata
    'defaultMeta' => [
        'title' => ['seo.seoTitle', 'title'],
        'description' => ['seo.seoDescription'],
        'image' => ['seo.seoImage'],
    ],

    // Additional Metas
    'additionalMeta' => [
        'og:type' => 'website',
        'twitter:card' => 'summary_large_image',
    ],
    // Sitemap
    'sitemapEnabled' => true,
    'sitemapConfig' => [
        'elements' => [
            'home' => [
                'elementType' => \craft\elements\Entry::class,
                'criteria' => ['section' => ['home']],
                'params' => ['changefreq' => 'monthly', 'priority' => 1],
            ],
        ],
        'custom' => [],
    ],
];
