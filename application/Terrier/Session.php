<?php

namespace Terrier;

class Session
{

    protected static $request;

    public static function init()
    {
        session_name(Config::get('session_name'));
        session_start();
        session_regenerate_id(true);

        if ( ! static::readSession() ) {
            static::createSession();
        }
    }

    public static function close()
    {
        session_write_close();
    }

    public static function oneTime($key, $value)
    {
        $_SESSION['userData']['Onetime:sweep:' . $key] = $value;
    }

    public static function oneTimeToken()
    {
        $token = sha1(bin2hex(openssl_random_pseudo_bytes(32)));
        $_SESSION['userData']['Onetime:sweep:token'] = $token;

        return $token;
    }

    public static function set($key, $value)
    {
        $_SESSION['userData'][$key] = $value;
    }

    public static function get($key, $default = null)
    {
        if ( isset($_SESSION['userData'][$key]) )
        {
            return $_SESSION['userData'][$key];
        }
        else if ( isset($_SESSION['userData']['Onetime:sweep:' . $key]) )
        {
            return $_SESSION['userData']['Onetime:sweep:' . $key];
        }

        return $default;
    }

    protected static function createSession()
    {
        $auth = array(
            'ipAddress'    => Request::ip(),
            'userAgent'    => Request::server('HTTP_USER_AGENT'),
            'lastActivity' => PROCESS_INIT_TIME,
            'sessionId'    => bin2hex(openssl_random_pseudo_bytes(16))
        );
        $auth = serialize($auth);
        $auth = Encrypt::encode($auth);

        $_SESSION[Config::get('session_auth_name')] = $auth;
        $_SESSION['userData']                       = array();
    }

    protected static function readSession()
    {
        if ( false === ($auth = static::getAuthSession()) )
        {
            return;
        }

        // check session is expired
        if ( $auth['lastActivity'] + Config::get('session_lifetime') < PROCESS_INIT_TIME )
        {
            return static::destroySession();
        }

        // check useragent maching
        if ( Config::get('session_match_useragent') === true
             && strpos(Request::server('HTTP_USER_AGENT'), $auth['userAgent']) !== 0 )
        {
            return static::destroySession();
        }

        // mark and sweep onetime sessions
        $newSession = array();
        foreach ( $_SESSION['userData'] as $key => $value )
        {
            if ( preg_match('#\AOnetime:(keep|sweep):([0-9a-zA-Z\-\._]+)#u', $key, $match) )
            {
                if ( $match[1] === 'keep' )
                {
                    $newSession['Onetime:sweep:' . $match[2]] = $value;
                }
            }
            else
            {
                $newSession[$key] = $value;
            }
        }

        $_SESSION['userData'] = $newSession;

        $auth['lastActivity'] = PROCESS_INIT_TIME;
        $_SESSION[Config::get('auth_auth_name')] = static::encode(serialize($auth));

        return true;
    }

    protected static function getAuthSession()
    {
        $authName = Config::get('session_auth_name');

        return ( isset($_SESSION[$authName]) )
                 ? @unserialize(Encrypt::decode($_SESSION[$authName]))
                 : false;
    }
}
