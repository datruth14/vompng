<?php

require_once __DIR__ . '/../backend/Database.php';
require_once __DIR__ . '/../backend/Auth.php';

use Backend\Auth;

$auth = new Auth();
$auth->logout();

header('Location: /');
