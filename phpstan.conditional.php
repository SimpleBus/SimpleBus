<?php
declare(strict_types = 1);

$config = [];

if (PHP_VERSION_ID < 8_00_00) {
    // Change of signature in PHP 8.0
    $config['parameters']['excludes_analyse'][] = 'packages/symfony-bridge/tests/Functional/Php8SmokeTest.php';
    $config['parameters']['excludes_analyse'][] = 'packages/symfony-bridge/tests/Functional/SmokeTest/Auto/AutoEventSubscriberUsingPublicMethodAndUnion.php';
}

return $config;
