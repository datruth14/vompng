<?php
/*
 * API endpoint for user logout.
 * Deletes the active session and redirects back to the homepage.
 */


require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';

auth_logout();

header('Location: /');
