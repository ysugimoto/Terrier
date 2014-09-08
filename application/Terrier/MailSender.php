<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Mail sender
 *
 * @namespace Terrier
 * @class MailSender
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class MailSender
{
    /**
     * Mail send driver
     *
     * @property $driver
     * @protected
     * @type \Terrier\Driver/Mail
     */
    protected $driver;

    /**
     * Mail setting
     *
     * @property $setting
     * @protected
     * @type Variable
     */
    protected $setting;

    /**
     * Constructor
     *
     * @constructor
     * @param array $setting
     */
    public function __construct($setting)
    {
        $this->setting = new Variable($setting);

        // Detect driver
        switch ( $setting['sender_method'] )
        {
            case 'smtp':
                $this->driver = new \Terrier\Driver\Mail\SmtpMail();
                $this->driver->setup($this->setting);
                break;

            case 'php':
                $this->driver = new \Terrier\Driver\Mail\PhpMail();
                $this->driver->setup($this->setting);
                break;
        }

    }



    // ----------------------------------------


    /**
     * Send mail
     *
     * @method send
     * @public
     * @param string $to
     * @param string $mailBody
     */
    public function send($to, $mailBody = 'mailbody.txt')
    {
        if ( ! $this->driver )
        {
            throw new Exception('Mail driver is not selected.');
        }

        // Pre-reset to
        $this->driver->resetTo();
        $this->driver->to($to);
        $this->driver->subject($this->setting->subject);

        if ( ! file_exists(TEMPLATE_PATH . $mailBody) )
        {
            throw new Exception('Mailbody template is not exists.');
        }

        $body = file_get_contents(TEMPLATE_PATH . $mailBody);
        $tmpl = new Template($body);
        $tmpl->compile();
        $values = new Variable(Validation::getValues());
        $this->driver->body($tmpl->parse($values));

        $this->driver->send();
    }
}
