<?php

define('PROCESS_INIT_TIME', time());

define('BASE_PATH',   __DIR__   . '/');
define('APP_PATH',    BASE_PATH . 'Terrier/');
define('CONFIG_PATH', BASE_PATH . 'config/');

spl_autoload_register(function($className) {
    $className = str_replace('\\', '/', $className);

    if ( file_exists(BASE_PATH . $className . '.php') ) {
        require_once(BASE_PATH . $className . '.php');
    }
});

if ( ! file_exists(CONFIG_PATH . 'config.php') )
{
    exit('Configuration file not found.');
}

$config = require(CONFIG_PATH . 'config.php');
\Terrier\Config::init($config);

\Terrier\Env::set('default_charset', 'UTF-8');

define('TEMPLATE_PATH', BASE_PATH . trim(\Terrier\Config::get('template_path', 'templates'), '/') . '/');
define('TMP_PATH',      BASE_PATH . trim(\Terrier\Config::get('tmp_path', 'tmp'), '/') . '/');

// include user helper
if ( file_exists(TEMPLATE_PATH . 'functions.php') )
{
    require_once(TEMPLATE_PATH . 'functions.php');
}
