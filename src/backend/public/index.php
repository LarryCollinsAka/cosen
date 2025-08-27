<?php

// 1. This is the single most important line of code.
//    It loads Composer's autoloader, giving us access to all our dependencies.
require_once __DIR__ . '/../../../vendor/autoload.php';

// 2. We'll set up error reporting for development.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 3. We'll add our first database connection and routing logic here later.
//    For now, let's just make sure it works.
echo "Backend is running!";