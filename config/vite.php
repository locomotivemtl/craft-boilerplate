<?php

use craft\helpers\App;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

if (Craft::$app->getRequest()->getIsConsoleRequest()) {
    return [];
}

// Use the current host for dev server requests. Otherwise, fall back to the primary site.
$host = App::env('VITE_SERVER_URL') ?? '';
$port = App::env('VITE_SERVER_PORT') ?: '5173';

if (!$host) {
    $devServerPublic = null;
    $devServerIsRunning = false;
} else {
    $devServerPublic = "$host:$port";

    // Check if the Vite server is running
    $devServerIsRunning = false;

    try {
        $client = new Client(['verify' => false]);
        $response = $client->get($devServerPublic . '/@vite/client');
        $statusCode = $response->getStatusCode();
        $devServerIsRunning = ($statusCode >= 200 && $statusCode < 300);
    } catch (GuzzleException $e) {
        $devServerIsRunning = false;
    }
}

return [
    'devServerPublic' => $devServerPublic,
    'serverPublic' => '/dist/',
    'checkDevServer' => false,
    'useDevServer' => $devServerIsRunning,
    'manifestPath' => '@webroot/dist/manifest.json',
    'criticalSuffix' => '_critical.min.css',
    'criticalPath' => '@webroot/dist/criticalcss',
];
