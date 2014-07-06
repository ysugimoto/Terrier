<?php

namespace Terrier;

class Router
{
    const MODE_INPUT    = 'input';
    const MODE_CONFIRM  = 'confirm';
    const MODE_REDIRECT = 'redirect';
    const MODE_SEND     = 'send';
    const MODE_ERROR    = 'error';
    const MODE_COMPLETE = 'complete';

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
            case static::MODE_CONFIRM:
            case static::MODE_SEND:
                if ( Session::checkToken(Request::post('token')) === FALSE )
                {
                    Session::oneTime('invalid_token', 1);
                    $action = static::MODE_REDIRECT;
                }
                else if ( Validation::create(Config::load('setting'))->run(Request::postAll()) === FALSE )
                {
                    $action = static::MODE_INPUT;
                }

                if ( $action === static::MODE_SEND )
                {
                    $mail = new \Terrier\MailSender(Config::load('mail'));
                    if ( $mail->send(Config::get('admin_email')) )
                    {
                        Session::oneTime('send_success', 1);
                        $action = static::MODE_COMPLETE;
                    }
                    else
                    {
                        Session::oneTime('send_error', 1);
                        $action = static::MODE_ERROR;
                    }
                }
                break;

            case static::MODE_COMPLETE:
                if ( ! Session::get('send_success') )
                {
                    $action = static::MODE_REDIRECT;
                }
                break;

            case static::MODE_ERROR:
                if ( ! Session::get('send_error') )
                {
                    $action = static::MODE_REDIRECT;
                }
                break;


            default:
                $action = static::MODE_INPUT;
                break;
        }

        return $action;
    }
}



