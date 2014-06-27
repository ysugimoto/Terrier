<?php

namespace Terrier;

class Request
{
    protected $_post;
    protected $_get;
    protected $_server;
    protected $_cookie;
    protected $_ip;
    private   $instance;

    private static function getInstance()
    {
        if ( ! static::$instance )
        {
            static::$instance = new Request();
        }

        return static::$instance;
    }

    public function __construct()
    {
        $this->_post   = $this->cleaning($_POST);
        $this->_get    = $this->cleaning($_GET);
        $this->_server = $this->cleaning($_SERVER);
        $this->_cookie = $this->cleaning($_COOKIE);
    }

    public static function get($key, $default = null)
    {
        $instance = static::getInstance();

        return ( isset($instance->_get[$key]) ) ? $instance->_get[$key] : $default;
    }

    public static function post($key, $default = null)
    {
        $instance = static::getInstance();

        return ( isset($instance->_post[$key]) ) ? $instance->_post[$key] : $default;
    }

    public static function server($key, $default = null)
    {
        $instance = static::getInstance();
        $key      = strtoupper($key);

        return ( isset($instance->_server[$key]) ) ? $instance->_server[$key] : $default;
    }

    public static function cookie($key, $default = null)
    {
        $instance = static::getInstance();

        return ( isset($instance->_cookie[$key]) ) ? $instance->_cookie[$key] : $default;
    }

    public static function ip()
    {
        $instance = static::getInstance();

        if ( ! $instance->_ip )
        {

            $remote  = static::server('REMOTE_ADDR');
            $trusted = Config::get('trusted_proxys', array());
            $ip = $default = '0.0.0.0';

            if ( FALSE !== ( $XFF = static::server('X_FORWARDED_FOR')) && $remote && in_array($remote, $trusted) )
            {
                $exp = explode(',', $XFF);
                $ip  = reset($exp);
            }
            else if ( FALSE !== ( $HCI = $this->server('HTTP_CLIENT_IP')) && $remote && in_array($remote, $trusted) )
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
            $this->_ip = $ip;
        }

        return $this->_ip;
    }

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

    private function _filterString($key, $encoding)
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

    private function _killNullByte($str)
    {
        return ( is_array($str) ) ? array_map(array($this, '_killNullByte'), $str) : str_replace('\0', '', $str);
    }

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

