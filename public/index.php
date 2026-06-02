<?php

declare(strict_types=1);

use BailiffPanel\App;

require __DIR__ . '/../vendor/autoload.php';

$app = App::create(__DIR__ . '/..');
$app->run();
