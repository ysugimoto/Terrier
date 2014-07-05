<?php

namespace Terrier;

class Variable implements \Iterator
{
    protected $_getterVariable;
    protected $_variable;
    protected $_pointer = 0;

    public function __construct($var)
    {
        $this->_variable       = $var;
        $this->_getterVariable = ( is_object($var) ) ? get_object_vars($var) : $var;
    }

    public function __get($key)
    {
        return ( array_key_exists($key, $this->_getterVariable) )
                 ? $this->_getterVariable[$key]
                 : null;
    }

    public function get()
    {
        return $this->_getterVariable;
    }

    public function __toString()
    {
        return $this->_variable;
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
