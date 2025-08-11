<?php

/**
 * Vite plugin for Craft CMS
 *
 * Allows the use of the Vite.js next generation frontend tooling with Craft CMS
 *
 * @link      https://nystudio107.com
 * @copyright Copyright (c) 2021 nystudio107
 */

use craft\helpers\App;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$devServerPort = App::env('VITE_SERVER_PORT') ?: '5173';
$devServerPublic = preg_replace('/:\d+$/', '', App::env('VITE_SERVER_URL')) . ':' . $devServerPort;
$devServerInternal = App::env('VITE_SERVER_URL') . ':' . $devServerPort;

if (App::env('ENVIRONMENT') === 'dev' || App::env('CRAFT_ENVIRONMENT') === 'dev') {
    try {
        $client = new Client(['verify' => false]);
        $response = $client->get($devServerPublic . '/@vite/client');
        $statusCode = $response->getStatusCode();
        $useDevServer = ($statusCode >= 200 && $statusCode < 300);
    } catch (GuzzleException $e) {
        $useDevServer = false;
    }
} else {
    $useDevServer = false;
}

return [

    /**
     * @var bool Should the dev server be used?
     */
    'useDevServer' => $useDevServer,

    /**
     * @var string File system path (or URL) to the Vite-built manifest.json
     */
    'manifestPath' => '@webroot/dist/manifest.json',

    /**
     * @var string The public URL to the dev server (what appears in `<script src="">` tags
     */
    'devServerPublic' => $devServerPublic,

    /**
     * @var string The public URL to use when not using the dev server
     */
    'serverPublic' => App::env('PRIMARY_SITE_URL') . '/dist/',

    /**
     * @var string|array The JavaScript entry from the manifest.json to inject on Twig error pages
     *              This can be a string or an array of strings
     */
    'errorEntry' => '',

    /**
     * @var string String to be appended to the cache key
     */
    'cacheKeySuffix' => '',

    /**
     * @var string The internal URL to the dev server, when accessed from the environment in which PHP is executing
     *              This can be the same as `$devServerPublic`, but may be different in containerized or VM setups.
     *              ONLY used if $checkDevServer = true
     */
    'devServerInternal' => $devServerInternal,

    /**
     * @var bool Should we check for the presence of the dev server by pinging $devServerInternal to make sure it's running?
     */
    'checkDevServer' => true,

    /**
     * @var bool Whether the react-refresh-shim should be included
     */
    'includeReactRefreshShim' => false,

    /**
     * @var bool Whether the modulepreload-polyfill shim should be included
     */
    'includeModulePreloadShim' => true,

    /**
     * @var string File system path (or URL) to where the Critical CSS files are stored
     */
    'criticalPath' => '@webroot/dist/criticalcss',

    /**
     * @var string the suffix added to the name of the currently rendering template for the critical css file name
     */
    'criticalSuffix' => '_critical.min.css',

    /**
     * @var bool Whether an onload handler should be added to <script> tags to fire a custom event when the script has loaded
     */
    'includeScriptOnloadHandler' => true,
];
