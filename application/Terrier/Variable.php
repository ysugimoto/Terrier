<?php

namespace Terrier;

class Variable implements \Iterator
{
    protected $_getterVariable;
    protected $_variable;
    protected $_isPrimitive = false;
    protected $_pointer = 0;

    public function __construct($var)
    {
        $this->_variable = $var;

        if ( is_object($var) )
        {
            $this->_getterVariable = get_object_vars($var);
        }
        else if ( is_array($var) )
        {
            $this->_getterVariable = $var;
        }
        else
        {
            $this->_isPrimitive = true;
            $this->_getterVariable = array(
                "$$var" => $var
            );
        }
    }

    public function __get($key)
    {
        return ( array_key_exists($key, $this->_getterVariable) )
                 ? $this->_getterVariable[$key]
                 : null;
    }

    public function __isset($key)
    {
        if ( array_key_exists($key, $this->_getterVariable)
            && count($this->_getterVariable[$key]->get()) > 0 )
        {
            return true;
        }
        return false;
    }


    public function get()
    {
        return $this->_getterVariable;
    }

    public function __toString()
    {
        return ( $this->_isPrimitive )
                 ? $this->_variable
                 : gettype($this->_variable);
    }

    public function rewind()
    {
        $this->_pointer = 0;
    }

    public function current()
    {
        return $this->_getterVariable[$this->_pointer];
    }

    public function key()
    {
        return $this->_pointer;
    }

    public function next()
    {
        ++$this->_pointer;
    }

    public function valid()
    {
        return array_key_exists($this->_pointer, $this->_getterVariable);
    }
}
