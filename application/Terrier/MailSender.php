<?php

namespace Terrier;

class MailSender
{
    protected $driver;
    protected $setting;

    public function __construct($setting)
    {
        $this->setting = new Variable($setting);

        switch ( $setting['sender_method'] )
        {
            case 'smtp':
                $this->driver = new \Terrier\Driver\Mail\SmtpMail();
                $this->driver->setup($setting);
                break;

            case 'php':
                $this->driver = new \Terrier\Driver\Mail\PhpMail();
                $this->driver->setup($setting);
                break;
        }

    }

    public function send($to)
    {
        if ( ! $this->driver )
        {
            throw new Exception('Mail driver is not selected.');
        }

        $this->driver->to($to);
        $this->driver->subject($this->setting->subject);

        if ( ! file_exists(TEMPLATE_PATH . 'mailbody.txt') )
        {
            throw new Exception('Mailbody template is not exists.');
        }

        $body = file_get_contents(TEMPLATE_PATH . 'mailbody.txt');
        $tmpl = new Template($body);
        $tmpl->compile();
        $values = new Variable(Validation::getValues());
        $this->driver->body($tmpl->parse($values));

        $this->driver->send();
    }
}



