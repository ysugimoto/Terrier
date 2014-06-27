<?php
define('PROCESS_INIT_TIME', time());

define('BASE_PATH',     __DIR__   . '/');
define('APP_PATH',      BASE_PATH . 'Terrier/');
define('TEMPLATE_PATH', BASE_PATH . 'templates/');
define('CONFIG_PATH',   APP_PATH  . 'config/');
define('TMP_PATH',      APP_PATH  . 'tmp/');

spl_autoload_register(function($className) {
    $className = str_replace('\\', '/', $className);

    if ( file_exists(BASE_PATH . $className . '.php') ) {
        \Terrier\Log::write($className . ' class loaded', \Terrier\Log::LEVEL_INFO);
        require_once(BASE_PATH . $className . '.php');
    }
});

$config = require(CONFIG_PATH . 'config.php');
\Terrier\Config::init($config);

\Terrier\Env::set('default_charset', 'UTF-8');

$router   = new \Terrier\Router();
$action   = $router->process();
$response = new \Terrier\Response($action);

$response->setView(new \Terrier\View($action));
$response->displayHeader();
$response->display();
