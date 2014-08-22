<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application Request manager
 *
 * @namespace Terrier
 * @class Request
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Request
{
    /**
     * Singleton instance
     *
     * @property $instance
     * @private
     * @type \Terrier\Request
     */
    private static $instance;

    /**
     * $_POST stack
     *
     * @property $_post
     * @protected
     * @type array
     */
    protected $_post;

    /**
     * $_GET stack
     *
     * @property $_get
     * @protected
     * @type array
     */
    protected $_get;

    /**
     * $_SERVER stack
     *
     * @property $_server
     * @protected
     * @type array
     */
    protected $_server;

    /**
     * $_COOKIE stack
     *
     * @property $_cookie
     * @protected
     * @type array
     */
    protected $_cookie;

    /**
     * IP stack
     *
     * @property $_ip
     * @protected
     * @type string
     */
    protected $_ip;


    // ----------------------------------------


    /**
     * Get Singeton instance
     *
     * @method getInstance
     * @public static
     * @return \Terrier\Request
     */
    private static function getInstance()
    {
        if ( ! static::$instance )
        {
            static::$instance = new Request();
        }

        return static::$instance;
    }


    // ----------------------------------------


    /**
     * Constructor
     *
     * @constructor
     */
    public function __construct()
    {
        $this->_post   = $this->cleaning($_POST);
        $this->_get    = $this->cleaning($_GET);
        $this->_server = $_SERVER;
        $this->_cookie = $this->cleaning($_COOKIE);
    }


    // ----------------------------------------


    /**
     * Build URL
     *
     * @method buildURL
     * @public
     * @param string $action
     * @return string
     */
    public static function buildURL($action)
    {
        $format = '%s://%s%s';
        $port   = ( static::server('SERVER_PORT')  ) ? (int)static::server('SERVER_PORT') : 80;
        $bind   = array(
            ( static::server('HTTPS') === 'on' || $port === 443 ) ? 'https' : 'http',
            static::server('HTTP_HOST'),
            static::server('PHP_SELF') . '?action=' . $action
        );

        return vsprintf($format, $bind);
    }


    // ----------------------------------------


    /**
     * Get input $_FILE
     *
     * @method file
     * @public static
     * @param string $field
     * @return mixed
     */
    public static function file($field)
    {
        return ( isset($_FILES[$field]) ) ? $_FILES[$field] : null;
    }


    // ----------------------------------------


    /**
     * Get $_GET value
     *
     * @method get
     * @public static
     * @param string $field
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $instance = static::getInstance();

        return ( isset($instance->_get[$key]) ) ? $instance->_get[$key] : $default;
    }



    // ----------------------------------------


    /**
     * Get All $_GET values
     *
     * @method getAll
     * @public static
     * @return array
     */
    public static function getAll()
    {
        $instance = static::getInstance();

        return $instance->_get;
    }


    // ----------------------------------------


    /**
     * Get $_POST value
     *
     * @method post
     * @public static
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function post($key, $default = null)
    {
        $instance = static::getInstance();

        return ( isset($instance->_post[$key]) ) ? $instance->_post[$key] : $default;
    }


    // ----------------------------------------


    /**
     * Get All $_POST values
     *
     * @method postAll
     * @public static
     * @return array
     */
    public static function postAll()
    {
        $instance = static::getInstance();

        return $instance->_post;
    }


    // ----------------------------------------


    /**
     * Get $_SERVER value
     *
     * @method server
     * @public static
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function server($key, $default = null)
    {
        $instance = static::getInstance();
        $key      = strtoupper($key);

        return ( isset($instance->_server[$key]) ) ? $instance->_server[$key] : $default;
    }


    // ----------------------------------------


    /**
     * Get All $_SERVER values
     *
     * @method serverAll
     * @public static
     * @return array
     */
    public static function serverAll()
    {
        $instance = static::getInstance();

        return $instance->_server;
    }


    // ----------------------------------------


    /**
     * Get $_COOKIE value
     *
     * @method cookie
     * @public static
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function cookie($key, $default = null)
    {
        $instance = static::getInstance();

        return ( isset($instance->_cookie[$key]) ) ? $instance->_cookie[$key] : $default;
    }


    // ----------------------------------------


    /**
     * Get All $_COOKIE values
     *
     * @method cookieAll
     * @public static
     * @return array
     */
    public static function cookieAll()
    {
        $instance = static::getInstance();

        return $instance->_cookie;
    }


    // ----------------------------------------


    /**
     * Get access IP address
     *
     * @method ip
     * @public static
     * @return string
     */
    public static function ip()
    {
        $instance = static::getInstance();

        if ( ! $instance->_ip )
        {

            $remote  = static::server('REMOTE_ADDR');
            $trusted = Config::get('trusted_proxys', array());
            $ip = $default = '0.0.0.0';

            if ( ($XFF = static::server('X_FORWARDED_FOR')) && $remote && in_array($remote, $trusted) )
            {
                $exp = explode(',', $XFF);
                $ip  = reset($exp);
            }
            else if ( ($HCI = static::server('HTTP_CLIENT_IP')) && $remote && in_array($remote, $trusted) )
            {
                $exp = explode(',', $HCI);
                $ip  = reset($exp);
            }
            else if ( $remote )
            {
                $ip = $remote;
            }

            if ( ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) )
            {
                $ip = $default;
            }
            $instance->_ip = $ip;
        }

        return $instance->_ip;
    }


    // ----------------------------------------


    /**
     * Clean up variables
     *
     * @method cleaning
     * @private
     * @param array $_global
     * @param string $encoding
     * @return array
     */
    private function cleaning($_global, $encoding = 'UTF-8')
    {
        $filtered = array();
        foreach ( $_global as $key => $value )
        {
            $key = $this->_filterString($key, $encoding);
            if ( is_array($value) )
            {
                foreach ( $value as $k => $v )
                {
                    $k = $this->_filterString($k, $encoding);
                    $value[$k] = $this->_filterString($v, $encoding);
                }
                $filtered[$key] = $value;
            }
            else
            {
                $filtered[$key] = $this->_filterString($value, $encoding);
            }
        }

        return $filtered;
    }


    // ----------------------------------------


    /**
     * Clean up string
     *
     * @method _filterString
     * @private
     * @param string $str
     * @param string $encoding
     * @return string
     */
    private function _filterString($str, $encoding)
    {
        if ( get_magic_quotes_gpc() )
        {
            $str = stripslashes($str);
        }

        if ( $encoding !== 'UTF-8' )
        {
            $str = $this->_convertUTF8($str, $encoding);
        }

        // kill invisible character
        do
        {
            $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str, -1, $count);
        }
        while( $count );

        // to strict linefeed
        if ( strpos($str, "\r") !== FALSE )
        {
            $str = str_replace(array("\r\n", "\r"), "\n", $str);
        }

        // trim nullbyte
        return $this->_killNullByte($str);
    }


    // ----------------------------------------


    /**
     * Kill nullbyte character
     *
     * @method _killNullByte
     * @private
     * @param mixed $str
     * @return mixed
     */
    private function _killNullByte($str)
    {
        return ( is_array($str) ) ? array_map(array($this, '_killNullByte'), $str) : str_replace('\0', '', $str);
    }


    // ----------------------------------------


    /**
     * Convert to UTF-8 string
     *
     * @method _convertUTF8
     * @private
     * @param string $str
     * @param string $encoding
     * @return string
     */
    private function _convertUTF8($str, $encoding = 'UTF-8')
    {
        if ( function_exists('iconv') && ! preg_match('/[^\x00-\x7F]/S', $str) )
        {
            return @iconv($encoding, 'UTF-8//IGNORE', $str);
        }
        else if ( mb_check_encoding($str, $encoding) )
        {
            return mb_convert_encoding($str, 'UTF-8', $encoding);
        }

        return $str;
    }
}
