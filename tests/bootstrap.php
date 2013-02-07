<?php

require __DIR__.'/../SplClassLoader.php';
$classLoader = new SplClassLoader('HAProxy\\Test', __DIR__);
$classLoader->register();

// Register the main classes
require __DIR__.'/../autoload.php';
