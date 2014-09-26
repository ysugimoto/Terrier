<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application Router
 *
 * @namespace Terrier
 * @class Router
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Router
{
    /**
     * INPUT signature
     *
     * @const MODE_INPUT
     * @type string
     * @default input
     */
    const MODE_INPUT = 'input';

    /**
     * CONFIRM signature
     *
     * @const MODE_CONFIRM
     * @type string
     * @default confirm
     */
    const MODE_CONFIRM = 'confirm';

    /**
     * REDIRECT signature
     *
     * @const MODE_REDIRECT
     * @type string
     * @default redirect
     */
    const MODE_REDIRECT = 'redirect';

    /**
     * SEND signature
     *
     * @const MODE_SEND
     * @type string
     * @default send
     */
    const MODE_SEND = 'send';

    /**
     * ERROR signature
     *
     * @const MODE_ERROR
     * @type string
     * @default error
     */
    const MODE_ERROR = 'error';

    /**
     * COMPLETE signature
     *
     * @const MODE_COMPLETE
     * @type string
     * @default complete
     */
    const MODE_COMPLETE = 'complete';

    /**
     * Action stack
     *
     * @property $action
     * @protected static
     * @type string
     */
    protected static $action;

    /**
     * Need to redirect flag
     *
     * @property $isNeedRedirect
     * @protected static
     * @type bool
     */
    protected static $isNeedRedirect = false;

    /**
     * Constructor
     *
     * @constructor
     */
    public function __construct()
    {
        // Do we need something?
    }


    // ----------------------------------------


    /**
     * Get Action
     *
     * @method action
     * @public static
     * @return string
     */
    public static function action()
    {
        return ( static::$action ) ? static::$action : static::MODE_INPUT;
    }


    // ----------------------------------------


    /**
     * Check is redirector
     *
     * @method isRedirect
     * @public static
     * @return bool
     */
    public static function isRedirect()
    {
        return static::$isNeedRedirect;
    }


    // ----------------------------------------


    /**
     * Process action
     *
     * @method process
     * @public
     * @return string
     */
    public function process()
    {
        // session start
        Session::init();

        $action = Request::get('action', static::MODE_INPUT);
        switch ( $action )
        {
            case static::MODE_INPUT:
                $this->handleInput();
                break;

            case static::MODE_CONFIRM:
                $this->handleConfirm($action);
                break;

            case static::MODE_SEND:
                $this->handleConfirm($action);
                if ( $action === static::MODE_SEND )
                {
                    $this->handleSend($action);
                }
                break;

            case static::MODE_COMPLETE:
                $this->handleComplete($action);
                break;

            case static::MODE_ERROR:
                $this->handleError($action);
                break;

            default:
                $action = static::MODE_INPUT;
                break;
        }

        static::$action = $action;

        return $action;
    }


    // ----------------------------------------


    /**
     * Input action handler
     *
     * @method handleInput
     * @protected
     * @return void
     */
    protected function handleInput()
    {
        if ( Request::server('REQUEST_METHOD') === 'POST' )
        {
            Validation::create(Config::load('setting'))->run(Request::postAll());
            Validation::flushError();
        }
    }


    // ----------------------------------------


    /**
     * Confirm action handler
     *
     * @method handleConfirm
     * @protected
     * @param string $action (ref)
     * @return void
     */
    protected function handleConfirm(&$action)
    {
        if ( Session::checkToken(Request::post('token')) === FALSE )
        {
            Session::oneTime('invalid_token', 1);
            static::$isNeedRedirect = true;
            $action = static::MODE_REDIRECT;
        }
        else if ( Validation::create(Config::load('setting'))->run(Request::postAll()) === FALSE )
        {
            $action = static::MODE_INPUT;
        }
    }


    // ----------------------------------------


    /**
     * Send action handler
     *
     * @method handleSend
     * @protected
     * @param string $action (ref)
     * @return void
     */
    protected function handleSend(&$action)
    {
        if ( $this->_sendMail() === true )
        {
            Session::oneTime('send_success', 1);
            static::$isNeedRedirect = true;
            $action = static::MODE_COMPLETE;
        }
        else
        {
            Session::oneTime('send_error', 1);
            static::$isNeedRedirect = true;
            $action = static::MODE_ERROR;
        }
    }


    // ----------------------------------------


    /**
     * Complete action handler
     *
     * @method handleComplete
     * @protected
     * @param string $action (ref)
     * @return void
     */
    protected function handleComplete(&$action)
    {
        if ( ! Session::get('send_success') )
        {
            static::$isNeedRedirect = true;
            $action = static::MODE_REDIRECT;
        }
        Session::purge();
    }


    // ----------------------------------------


    /**
     * Error action handler
     *
     * @method handleError
     * @protected
     * @param string $action (ref)
     * @return void
     */
    protected function handleError(&$action)
    {
        if ( ! Session::get('send_error') )
        {
            static::$isNeedRedirect = true;
            $action = static::MODE_REDIRECT;
        }
        Session::purge();
    }


    // ----------------------------------------


    /**
     * Send mail
     *
     * @method _sendMail
     * @protected
     * @return bool
     */
    protected function _sendMail()
    {
        $setting = Config::load('mail');
        $mailer  = new \Terrier\MailSender($setting);

        // send to admin
        if ( ! empty($setting['admin_email']) )
        {
            try
            {
                $subject = ( isset($setting['subject_for_admin'] ) ? $setting['subject_for_admin'] : $setting['subject'];
                $mailer->send($setting['admin_email'], $subject, Config::get('admin_mailbody'));
            }
            catch ( \Terrier\Exception $e )
            {
                Log::write($e->getMessage(), Log::LEVEL_INFO);
                return false;
            }
        }

        // loop and send mail for key fields
        foreach ( Validation::getReplyFields() as $field )
        {
            $to = Validation::getValue($field);
            try
            {
                $mailer->send($to, $setting['subject'], Config::get('reply_mailbody'));
            }
            catch ( \Terrier\Exception $e )
            {
                Log::write($e->getMessage(), Log::LEVEL_INFO);
                return false;
            }
        }

        return true;
    }
}
