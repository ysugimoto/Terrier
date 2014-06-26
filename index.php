<?php

define('APP_PATH',     __DIR__ . '/');
define('CONFIG_PATH', APP_PATH . 'config/');
define('TMP_PATH',    APP_PATH . 'tmp/');

spl_autoload_register(function($className) {
    $className = str_replace('\\', '/', $className);

    if ( file_exists(APP_PATH . $className . '.php') ) {
        require_once(APP_PATH . $className . '.php');
    }
});

$config = require(CONFIG_PATH . 'config.php');
\Terrier\Config::init($config);

$request = new \Terrier\Request();
$router  = new \Terrier\Router($request);

$router->process();

$view = View::create($router->getMode());
$view->attachExtraHeader();
$view->attachExtraFooter();

echo $view->getOutput();
