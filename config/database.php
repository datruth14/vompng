<?php
/*
 * Application database configuration.
 * Defines the SQLite database path and connection settings.
 */

// Database configuration
return [
    'driver' => 'sqlite',
    'database' => __DIR__ . '/../database/vomp.db',
    'prefix' => '',
];
