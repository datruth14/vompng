<?php
// PHP Built-in server router
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|svg|woff|woff2|ttf|eot|json|ico)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is
}
require_once 'index.php';
