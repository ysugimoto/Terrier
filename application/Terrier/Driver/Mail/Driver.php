<?php

namespace Terrier\Driver\Mail;

abstract class Driver
{
    /**
     * parameter stacks
     * @var array / string
     */
    protected $_to  = array();
    protected $_cc  = array();
    protected $_bcc = array();
    protected $_subject;
    protected $_body;


    /**
     * Attachment files
     * @var array
     */
    protected $_attachFiles = array();


    /**
     * Boundary position string
     * @var string
     */
    protected $_boundary;


    /**
     * From parameters
     * @var string
     */
    protected $_from     = '';
    protected $_fromName = '';


    /**
     * Message-ID
     * @var string
     */
    protected $_messageID = '';


    /**
     * Connection keep-alive flag
     * ( use only SMTP )
     * @var bool
     */
    protected $_keepAlive = FALSE;


    /**
     * new lines
     * @var string
     */
    protected $LF   = "\n";
    protected $CRLF = "\r\n";

    protected $setting;

    /**
     * do send mail implements
     * @abstract
     */
    abstract protected function _sendmail();

    // ---------------------------------------------------------------


    /**
     * setup
     * 
     * @access public
     * @return string
     */
    public function setup($settings)
    {
        $this->setting   = $settings;
        $this->_from     = $this->_removeLine((string)$this->setting->from);
        $this->_fromName = $this->_removeLine((string)$this->setting->from_name);

    }


    // ---------------------------------------------------------------

    protected function _setTarget($sign, $email, $toName)
    {
        if ( is_array($email) )
        {
            foreach ( $email as $email )
            {
                call_user_func_array(array($this, $sign), $email);
            }
            return;
        }
        if ( strpos($email, ',') !== FALSE )
        {
            $exp = explode(',', $email);
            array_map(array($this, $sign), $exp);
        }
        else if ( strpos($email, ';') !== FALSE )
        {
            $exp = explode(';', $email);
            array_map(array($this, $sign), $exp);
        }
        else
        {
            $this->{'_' . $sign}[] = array($this->_removeLine($email), $this->_removeLine($toName));
        }
    }


    /**
     * set To
     * 
     * @access public
     * @param  mixed  $email
     * @param  string $toName
     */
    public function to($email= '', $toName = '')
    {
        $this->_setTarget('to', $email, $toName);
    }


    // ---------------------------------------------------------------


    /**
     * Set Cc
     * 
     * @access public
     * @param  mixed  $email
     * @param  string $toName
     */
    public function cc($email= '', $toName = '')
    {
        $this->_setTarget('cc', $email, $toName);
    }


    // ---------------------------------------------------------------


    /**
     * Set Bcc
     * 
     * @access public
     * @param  mixed  $email
     * @param  string $toName
     */
    public function bcc($email= '', $toName = '')
    {
        $this->_setTarget('bcc', $email, $toName);
    }


    // ---------------------------------------------------------------


    /**
     * Set Subject
     * 
     * @access public
     * @param  string $subject
     */
    public function subject($subject)
    {
        // pre remove new-line chars
        $this->_subject = $this->_removeLine($subject);
    }


    // ---------------------------------------------------------------


    /**
     * Set from
     * 
     * @access public
     * @param  string $from
     */
    public function from($from)
    {
        $this->_from = $this->_removeLine($from);
    }


    // ---------------------------------------------------------------


    /**
     * Set from name
     * 
     * @access public
     * @param  string $fromName
     */
    public function fromName($fromName)
    {
        $this->_fromName = $this->_removeLine($fromName);
    }


    // ---------------------------------------------------------------


    /**
     * Reset To
     * 
     * @access public
     */
    public function resetTo()
    {
        $this->_to = array();
    }


    // ---------------------------------------------------------------


    /**
     * Set Message-ID
     * 
     * @access public
     * @param  string $msgID
     */
    public function messageID($msgID)
    {
        $this->_messageID = $this->_removeLine($msgID);
    }


    // ---------------------------------------------------------------


    /**
     * set mailbody
     * 
     * @access public
     * @param  string $body
     */
    public function body($body)
    {
        // pre covert new-line to "\n"
        $this->_body = str_replace(array("\r\n", "\r"), "\n", $body);
    }


    // ---------------------------------------------------------------


    /**
     * Attach file
     * 
     * @access public
     * @param  string $file
     * @param  string $attachName
     * @param  string $encoding ( default base64 )
     * @param  string $mimetype ( default application/octec-stream )
     */
    public function attach($file, $attachName = '', $encoding = 'base64', $mimetype = 'application/octet-stream')
    {
        if ( is_array($file) )
        {
            foreach ( $file as $f )
            {
                $this->attach($f);
            }
            return;
        }
        else
        {
            // Does file really exists?
            if ( ! file_exists($file) )
            {
                throw new \Terrier\Exception('Attachment file not exists!. file=' . $file);
                return;
            }

            // create stack data
            $data = new \stdClass;

            $data->filePath   = $file;
            $data->encoding   = $encoding;
            $data->mimeType   = $mimetype;

            // If attachName is empty, use basename
            $data->attachName = ( empty($attachName) )
                                 ? $this->_removeLine(basename($file))
                                 : $this->_removeLine($attachName);

            $this->_attachFiles[] = $data;
        }
    }


    // ---------------------------------------------------------------


    /**
     * Do send mail
     * 
     * @access public
     * @return bool
     */
    public function send()
    {
        // encode transaction start
        $defLang = mb_language();
        $defEnc  = mb_internal_encoding();

        mb_language('ja');
        mb_internal_encoding('UTF-8');

        $ret = $this->_sendmail();

        mb_language($defLang);
        mb_internal_encoding($defEnc);

        return $ret;
    }


    // ---------------------------------------------------------------
    // Utility methods
    // ---------------------------------------------------------------


    /**
     * Encode Header string
     * 
     * @access protected
     * @param string $str
     * @return string
     */
    protected function _encodeHeader($str)
    {
        // We don't trust "mb_encode_mimeheader" function...
        // So, manualy encode ISO-2022-JP on base64_encode
        return '=?iso-2022-jp?B?' . base64_encode(mb_convert_encoding($str, 'JIS', 'UTF-8')) . '?=';
    }


    // ---------------------------------------------------------------


    /**
     * remove new line character
     * 
     * @access protected
     * @param  $str
     * @return string
     */
    protected function _removeLine($str)
    {
        return str_replace(array("\r\n", "\r", "\n"), '', trim($str));
    }


    // ---------------------------------------------------------------


    /**
     * Format To, Cc, Bcc
     * 
     * @access protected
     * @param array $addr
     * 
     * returns "Name <e-mail>" formatted string
     */
    protected function _addressFormat($addr)
    {
        return ( ! isset($addr[1]) || empty($addr[1]) )
                 ? $addr[0]
                 : sprintf('%s <%s>', $this->_encodeHeader($addr[1]), $addr[0]);
    }
}
