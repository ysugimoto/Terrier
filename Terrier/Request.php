<?php

namespace Terrier;

class Request
{
    protected $_post;
    protected $_get;
    protected $_server;

    public function __construct()
    {
        $this->_post   = $this->cleaning($_POST);
        $this->_get    = $this->cleaning($_GET);
        $this->_server = $this->cleaning($_SERVER);
    }

    public function get($key, $default = null)
    {
        return ( isset($this->_get[$key]) )
                 ? $this->_get[$key]
                 : $default;
    }

    public function post($key, $default = null)
    {
        return ( isset($this->_post[$key]) )
                 ? $this->_post[$key]
                 : $default;
    }
    public function server($key, $default = null)
    {
        return ( isset($this->_server[$key]) )
                 ? $this->_server[$key]
                 : $default;
    }

    private function cleaning($_global)
    {
        // TODO: sanytize
        return $_global;
    }
}

