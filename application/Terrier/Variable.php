<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Variable mapper
 *
 * @namespace Terrier
 * @class Variable
 * @implements \Iterator
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Variable implements \Iterator
{
    /**
     * Getter mapping values
     *
     * @property $_getterVariable
     * @protected
     * @type array
     */
    protected $_getterVariable;

    /**
     * Passed variable
     *
     * @property $_variable
     * @protected
     * @type array
     */
    protected $_variable;

    /**
     * Primitive flag
     *
     * @property $_isPrimitive
     * @protected
     * @type bool
     */
    protected $_isPrimitive = false;

    /**
     * Mapping pointer
     *
     * @property $_pointer
     * @protected
     * @type int
     */
    protected $_pointer = 0;


    // ----------------------------------------


    /**
     * Constructor
     *
     * @constructor
     * @param mixed $var
     */
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


    // ----------------------------------------


    /**
     * Overload __get method
     *
     * @method __get
     * @public
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return ( array_key_exists($key, $this->_getterVariable) )
                 ? $this->_getterVariable[$key]
                 : null;
    }


    // ----------------------------------------


    /**
     * Overload __isset method
     *
     * @method __isset
     * @public
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        if ( array_key_exists($key, $this->_getterVariable)
            && count($this->_getterVariable[$key]->get()) > 0 )
        {
            return true;
        }
        return false;
    }


    // ----------------------------------------


    /**
     * Get Mapping value
     *
     * @method get
     * @public
     * @return array
     */
    public function get()
    {
        return $this->_getterVariable;
    }


    // ----------------------------------------


    /**
     * Overload __toString method
     *
     * @method __toString
     * @public
     * @return string
     */
    public function __toString()
    {
        return ( $this->_isPrimitive )
                 ? $this->_variable
                 : gettype($this->_variable);
    }


    // ----------------------------------------


    /**
     * Iterator interface inplements
     *
     * @method rewind
     * @public
     * @return void
     */
    public function rewind()
    {
        $this->_pointer = 0;
    }


    // ----------------------------------------


    /**
     * Iterator interface inplements
     *
     * @method current
     * @public
     * @return mixed
     */
    public function current()
    {
        return $this->_getterVariable[$this->_pointer];
    }


    // ----------------------------------------


    /**
     * Iterator interface inplements
     *
     * @method key
     * @public
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }


    // ----------------------------------------


    /**
     * Iterator interface inplements
     *
     * @method next
     * @public
     * @return void
     */
    public function next()
    {
        ++$this->_pointer;
    }


    // ----------------------------------------


    /**
     * Iterator interface inplements
     *
     * @method valid
     * @public
     * @return bool
     */
    public function valid()
    {
        return array_key_exists($this->_pointer, $this->_getterVariable);
    }
}
