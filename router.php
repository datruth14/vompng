<?php
/*
 * Built-in PHP server router.
 * Allows static assets to be served directly while routing PHP requests to index.php.
 */

// PHP Built-in server router
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|svg|woff|woff2|ttf|eot|json|ico|webp)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is
}

/* Block access to database directory */
if (strpos($_SERVER['REQUEST_URI'], '/database/') === 0) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

/* Block access to .db files */
if (preg_match('/\.db$/i', $_SERVER["REQUEST_URI"])) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

/* Route uploaded files */
if (strpos($_SERVER['REQUEST_URI'], '/assets/uploads/') === 0 || strpos($_SERVER['REQUEST_URI'], '/assets/media/') === 0) {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require_once 'index.php';
