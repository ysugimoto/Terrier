<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application Config manager
 *
 * @namespace Terrier
 * @class Config
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Config
{
    /**
     * stack config
     *
     * @property $_config
     * @protected static
     * @type array
     * @default []
     */
    protected static $_config = array();

    /**
     * Flag of initialized
     *
     * @property $initialized
     * @protected sttatic
     * @type boolean
     * @default false
     */
    protected static $initialized = false;

    /**
     * Initialize config
     *
     * @method init
     * @public static
     * @param array $config
     * @throws \Terier\Exception
     */
    public static function init($config = array())
    {
        // Once time only
        if ( static::$initialized !== false )
        {
            throw new Exception('Config has already initialized.');
        }

        static::$_config     = $config;
        static::$initialized = true;
    }


    // ----------------------------------------


    /**
     * Set config
     *
     * @method set
     * @public static
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        static::$_config[$key] = $value;
    }


    // ----------------------------------------


    /**
     * Get config
     *
     * @method get
     * @public static
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return ( isset(static::$_config[$key]) )
                 ? static::$_config[$key]
                 : $default;
    }


    // ----------------------------------------


    /**
     * Load configuration
     *
     * @method load
     * @public static
     * @param string $name
     * @return array
     * @throws \Terrier\Exception
     */
    public static function load($name)
    {
        if ( ! file_exists(CONFIG_PATH . $name . '.php') )
        {
            throw new Exception('Config load error: ' . $name . ' is not exists.');
        }

        return include(CONFIG_PATH . $name . '.php');
    }
}
