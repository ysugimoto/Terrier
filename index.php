<?php

define('APP_PATH',          __DIR__  . '/');
define('CONFIG_PATH',       APP_PATH . 'Terrier/config/');
define('COMPONENT_PATH',    APP_PATH . 'Components/');
define('TMP_PATH',          APP_PATH . 'Twrrier/tmp/');
define('PROCESS_INIT_TIME', time());

spl_autoload_register(function($className) {
    $className = str_replace('\\', '/', $className);

    if ( file_exists(APP_PATH . $className . '.php') ) {
        require_once(APP_PATH . $className . '.php');
    }
});

Env::set('default_charset', 'UTF-8');

$config = require(CONFIG_PATH . 'config.php');
\Terrier\Config::init($config);

$router   = new \Terrier\Router();
$action   = $router->process();
$response = new \Terrier\Response($action);

$response->setView(new \Terrier\View($action));
$response->displayHeader();
$response->display();
