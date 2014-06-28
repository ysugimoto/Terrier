<?php

namespace Terrier;

class Variable
{
    protected $variable;

    public function __construct($var)
    {
        $this->variable = $var;
    }

    public function __get($key)
    {
        if ( is_object($this->variable) )
        {
            return ( isset($this->variable->{$key}) )
                     ? $this->variable->{$key}
                     : null;
        }
        else if ( is_array($this->variable) )
        {
            return ( array_key_exists($key, $this->variable) )
                     ? $this->variable[$key]
                     : null;
        }
    }
}
