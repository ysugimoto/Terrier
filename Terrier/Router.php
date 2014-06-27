<?php

namespace Terrier;

class Router
{
    protected $action;

    public function __construct()
    {
        // Do we need something?
    }

    public function process()
    {
        Session::init();
        Session::oneTimeToken();

        $this->action = Request::get('action', 'input');
        switch ( $this->action )
        {
            case 'input':
                break;

            case 'confirm':
            case 'send':
                if ( Session::checkToken(Request::post('token')) === FALSE )
                {
                    Session::oneTime('invalid_token', 1);
                    $this->action = 'redirect';
                }
                if ( Validation::create(Config::load('validation'))->run() === FALSE )
                {
                    $this->action = 'input';
                }

                if ( $this->action === 'send' )
                {
                    $mail = new \Terrier\MailSender(Config::load('mail'));
                    $mail->send();
                }
                break;

            default:
                $this->action = 'input';
                break;
        }

        return $this->action;
    }

    public function getMode()
    {
        return $this->action;
    }
}



