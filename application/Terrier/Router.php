<?php

namespace Terrier;

class Router
{
    const MODE_INPUT    = 'input';
    const MODE_CONFIRM  = 'confirm';
    const MODE_REDIRECT = 'redirect';
    const MODE_SEND     = 'send';
    const MODE_ERROR    = 'error';

    protected $action;

    public function __construct()
    {
        // Do we need something?
    }

    public function process()
    {
        Session::init();

        $action = Request::get('action', static::MODE_INPUT);
        switch ( $action )
        {
            case static::MODE_INPUT:
                return static::MODE_INPUT;
                break;

            case static::MODE_CONFIRM:
            case static::MODE_SEND:
                if ( Session::checkToken(Request::post('token')) === FALSE )
                {
                    Session::oneTime('invalid_token', 1);
                    return static::MODE_REDIRECT;
                }
                else if ( Validation::create(Config::load('setting'))->run(Request::postAll()) === FALSE )
                {
                    return static::MODE_INPUT;
                }

                if ( $action === static::MODE_SEND )
                {
                    $mail = new \Terrier\MailSender(Config::load('mail'));
                    if ( ! $mail->send() )
                    {
                        return static::MODE_ERROR;
                    }
                }
                return $action;

            default:
                return static::MODE_INPUT;
        }
    }
}



