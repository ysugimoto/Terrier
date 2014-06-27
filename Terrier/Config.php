<?php

namespace Terrier;

class Config
{
    protected static $_config;

    public static function init($config = array())
    {
        // Once time only
        if ( static::$_config !== null )
        {
            throw new \Terrier\Exeption('Config has already initialized.');
        }

        static::$_config = $config;
    }

    public static function set($key, $value)
    {
        static::$_config[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return ( isset(static::$_config[$key]) )
                 ? static::$_config[$key]
                 : $default;
    }

    public static function load($name)
    {
        if ( ! file_exists(CONFIG_PATH . $name . '.php') )
        {
            throw new Exception('Config load error: ' . $name . ' is not exists.');
        }
        return include(CONFIG_PATH . $name . '.php');
    }
}
