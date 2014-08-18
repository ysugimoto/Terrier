<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application Session manager
 *
 * @namespace Terrier
 * @class Session
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Session
{
    /**
     * OneTime keep sesion signature
     *
     * @const ONETIME_KEEP_SIGNATURE
     * @type string
     */
    const ONETIME_KEEP_SIGNATURE  = 'Onetime:keep';

    /**
     * OneTime sweep sesion signature
     *
     * @const ONETIME_SWEEP_SIGNATURE
     * @type string
     */
    const ONETIME_SWEEP_SIGNATURE = 'Onetime:sweep';


    // ----------------------------------------


    /**
     * Session initialize
     *
     * @method init
     * @public static
     * @return void
     */
    public static function init()
    {
        session_name(Config::get('session_name'));
        session_start();
        session_regenerate_id(true);

        if ( ! static::readSession() ) {
            static::createSession();
        }
    }


    // ----------------------------------------


    /**
     * Session close
     *
     * @method close
     * @public static
     * @return void
     */
    public static function close()
    {
        session_write_close();
    }


    // ----------------------------------------


    /**
     * Onetime session set
     *
     * @method oneTime
     * @public static
     * @return void
     */
    public static function oneTime($key, $value)
    {
        $_SESSION['userData'][static::ONETIME_KEEP_SIGNATURE. $key] = $value;
    }


    // ----------------------------------------


    /**
     * Set onetime token
     *
     * @method oneTimeToken
     * @public static
     * @return string
     */
    public static function oneTimeToken()
    {
        $token = sha1(bin2hex(openssl_random_pseudo_bytes(32)));
        static::oneTime(Config::get('session_onetime_token_name', 'token'), $token);

        return $token;
    }


    // ----------------------------------------


    /**
     * Check token
     *
     * @method checkToken
     * @public static
     * @param string $token
     * @return bool
     */
    public static function checkToken($token = null)
    {
        $tokenName = Config::get('session_onetime_token_name', 'token');

        return ( $token && $token === static::get($tokenName) ) ? true : false;
    }


    // ----------------------------------------


    /**
     * Set session
     *
     * @method set
     * @public static
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set($key, $value)
    {
        $_SESSION['userData'][$key] = $value;
    }


    // ----------------------------------------


    /**
     * Get session value
     *
     * @method get
     * @public static
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if ( isset($_SESSION['userData'][$key]) )
        {
            return $_SESSION['userData'][$key];
        }
        else if ( isset($_SESSION['userData'][static::ONETIME_KEEP_SIGNATURE . $key]) )
        {
            return $_SESSION['userData'][static::ONETIME_KEEP_SIGNATURE . $key];
        }
        else if ( isset($_SESSION['userData'][static::ONETIME_SWEEP_SIGNATURE . $key]) )
        {
            return $_SESSION['userData'][static::ONETIME_SWEEP_SIGNATURE . $key];
        }

        return $default;
    }


    // ----------------------------------------


    /**
     * Create new session
     *
     * @method createSession
     * @protected static
     */
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


    // ----------------------------------------


    /**
     * Read saved session and authenticate
     *
     * @method readSession
     * @protected static
     * @return bool
     */
    protected static function readSession()
    {
        if ( false === ($auth = static::getAuthSession()) )
        {
            return;
        }

        // check session is expired
        if ( $auth['lastActivity'] + Config::get('session_lifetime', 300000) < PROCESS_INIT_TIME )
        {
            Log::write('Session destroyed: expired', Log::LEVEL_INFO);
            return static::purge();
        }

        // check useragent maching
        if ( Config::get('session_match_useragent') === true
             && strpos(Request::server('HTTP_USER_AGENT'), $auth['userAgent']) !== 0 )
        {
            Log::write('Session destroyed: userAgent changed', Log::LEVEL_INFO);
            return static::purge();
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
        $_SESSION[Config::get('session_auth_name')] = Encrypt::encode(serialize($auth));

        return true;
    }


    // ----------------------------------------


    /**
     * Get auth session
     *
     * @method getAuthSession
     * @protected static
     * @return mixed
     */
    protected static function getAuthSession()
    {
        $authName = Config::get('session_auth_name');

        return ( isset($_SESSION[$authName]) )
                 ? @unserialize(Encrypt::decode($_SESSION[$authName]))
                 : false;
    }


    // ----------------------------------------


    /**
     * Purge session data
     *
     * @method purge
     * @public static
     */
    public static function purge()
    {
        $_SESSION = array();
    }
}
