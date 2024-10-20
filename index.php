<?php

use Http\Router;

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/helpers.php';
require __DIR__ . '/config.php';


$router = new Router();

require __DIR__ . '/routes/api.php';
require __DIR__ . '/routes/web.php';

$router->dispatch();