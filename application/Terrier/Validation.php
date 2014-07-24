<?php

namespace Terrier;

class Validation
{
    protected static $errors = array();
    protected static $values = array();

    public static function create($setting)
    {
        return new static($setting);
    }

    public static function getErrors()
    {
        return static::$errors;
    }

    public static function flushError()
    {
        static::$errors = array();
    }

    public static function getValues()
    {
        return static::$values;
    }

    public function __construct($setting)
    {
        $this->setting  = $setting;
        $this->messages = Config::load('messages');
    }

    protected $currentValues = array();

    public function run($values)
    {
        // stack
        $this->currentValues = $values;
        $isValid  = true;

        foreach ( $this->setting as $field => $setting )
        {
            $value = ( isset($values[$field]) ) ? $values[$field] : '';

            foreach ( $setting['rules'] as $key => $rule )
            {
                if ( isset($rule['upload']) && $rule['upload'] === true )
                {
                    $upload = new Upload(Config::load('upload'));
                    $result = $upload->process($field);
                    if ( $result === false )
                    {
                        $sValid = false;
                        static::$errors[$field] = true;
                    }
                    continue;
                }

                list($validation, $arguments) = $this->parseRules($rule);

                if ( ! method_exists($this, $validation) )
                {
                    continue;
                }

                //@FIXME refactoring
                if ( is_array($value) )
                {
                    foreach ( $value as $index => $val )
                    {
                        array_unshift($arguments, $val);
                        $this->_check($validation, $arugments);
                        $result = call_user_func_array(array($this, $validation), $arguments);
                        if ( is_bool($result) && $result === false )
                        {
                            $arguments[0] = $setting['label'];
                            static::$errors[$field] = vsprintf($this->messages[$validation], $arguments);
                            $isValid = FALSE;
                            break;
                        }
                        else if ( is_string($result) )
                        {
                            $val = $value[$index] = $result;
                        }
                        array_shift($arguments);
                    }

                    if ( $isValid === false )
                    {
                        break;
                    }
                }
                else
                {
                    array_unshift($arguments, $value);
                    $result = call_user_func_array(array($this, $validation), $arguments);
                    if ( is_bool($result) && $result === false )
                    {
                        $arguments[0] = $setting['label'];
                        static::$errors[$field] = vsprintf($this->messages[$validation], $arguments);
                        $isValid = FALSE;
                        break;
                    }
                    else if ( is_string($result) )
                    {
                        $value = $result;
                    }
                }
            } // rules foreach

            static::$values[$field] = $value;

        } // setting foreach

        // purge
        $this->currentValue = array();

        return $isValid;
    }

    protected function parseRules($rule)
    {
        if ( FALSE !== ($point = strpos($rule, '@')) )
        {
            $validation = substr($rule, 0, $point);
            $arguments  = explode(',', substr($rule, ++$point));
            $arguments  = array_map('trim', $arguments);
        }
        else
        {
            $validation = $rule;
            $arguments  = array();
        }

        return array($validation, $arguments);
    }

    // --------------------------------------------------

    /**
     * Value is required
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function required($str)
    {
        return ( $str !== '' ) ? TRUE : FALSE;
    }


    // --------------------------------------------------

    /**
     * Value is expected string
     * 
     * @access public
     * @param  string $str
     * poaram  [string, ...]
     * @return bool
     */
    public function expected($str)
    {
        $expects = array_slice(func_get_args(), 1);

        return ( in_array($str, $expects) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is blank only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function blank($str)
    {
        return ( $str === '' ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is valid email format and dns exists
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function valid_email($str)
    {
        if ( function_exists('filter_var') )
        {
            if ( ! filter_var($str, FILTER_VALIDATE_EMAIL) )
            {
                return FALSE;
            }
        }
        else if ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/iD", $str) )
        {
            return FALSE;
        }

        if ( function_exists('checkdnsrr') )
        {
            list(, $host) = explode('@', $str);
            if ( ! checkdnsrr($host, 'MX') && ! checkdnsrr($host, 'A') )
            {
                return FALSE;
            }
        }
        return TRUE;
    }


    // --------------------------------------------------


    /**
     * Value is valid URI format
     * 
     * @access public
     * @param  string $str
     * @return bool
     */

    public function valid_url($str)
    {
        if ( function_exists('filter_var') )
        {
            return (bool)filter_var($str, FILTER_VALIDATE_URL);
        }
        return ( preg_match('/\A(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)\Z/u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is strcit integer format ( 0-9 digits )
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function ctype($str)
    {
        return ctype_digit($str);
    }


    // --------------------------------------------------


    /**
     * return trimmed value
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function trim($str)
    {
        return trim($str);
    }


    // --------------------------------------------------


    /**
     * Value length less than condition length
     * 
     * @access public
     * @param  string $str
     * @param  string $length
     * @return bool
     */
    public function max_length($str, $length)
    {
        if ( function_exists('mb_strlen') )
        {
            $len = mb_strlen($str, 'UTF-8');
        }
        else
        {
            $len = strlen($str);
        }

        return ( $len <= (int)$length ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value length greater than condition length
     * 
     * @access public
     * @param  string $str
     * @param  string $length
     * @return bool
     */
    public function min_length($str, $length)
    {
        if ( function_exists('mb_strlen') )
        {
            $len = mb_strlen($str, 'UTF-8');
        }
        else
        {
            $len = strlen($str);
        }

        return ( $len >= (int)$length ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is exact length
     * 
     * @access public
     * @param  string $str
     * @param  string $length
     * @return bool
     */
    public function exact_length($str, $length)
    {
        if ( function_exists('mb_strlen') )
        {
            $len = mb_strlen($str, 'UTF-8');
        }
        else
        {
            $len = strlen($str);
        }

        return ( $len === (int)$length ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is match in regex
     * 
     * @access public
     * @param  string $str
     * @param  string $regex
     * @return bool
     */
    public function regex($str, $regex)
    {
        return ( preg_match('#' . str_replace('#', '\#', $regex) . '#u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value in range digit
     * 
     * @access public
     * @param  string $str
     * @param  string $range
     * @return bool
     */
    public function range($str, $range)
    {
        list($min, $max) = explode(':', $range);
        return ( (int)$min <= (int)$str && (int)$max >= (int)$str ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is Alpha chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function alpha($str)
    {
        return ( preg_match('/\A[a-zA-Z]+\Z/u', $str) ) ? TRUE : FALSE; 
    }


    // --------------------------------------------------


    /**
     * Value is Alpha-numeric chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function alnum($str)
    {
        return ( preg_match('/\A[a-zA-Z0-9]+\Z/u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is Alpha-numeric and dash chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function alnum_dash($str)
    {
        return ( preg_match('/\A[a-zA-Z0-9\-_]+\Z/u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is Alpha and dash chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function alpha_dash($str)
    {
        return ( preg_match('/\A[a-zA-Z\-_]+\Z/u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is Lowercase-Alpha chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function alpha_lower($str)
    {
        return ( preg_match('/\A[a-z]+\Z/u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is Upercase-Alpha chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function alpha_upper($str)
    {
        return ( preg_match('/\A[A-Z]+\Z/u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is numeric chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function numeric($str)
    {
        return is_numeric($str);
    }


    // --------------------------------------------------


    /**
     * Value is expected
     * 
     * @access public
     * @param  string $str
     * @pram   string cond
     * @return bool
     */
    public function expects($str, $cond)
    {
        $expected = array_filter(explode(':', $cond));

        return in_array($str, $expected);
    }


    // --------------------------------------------------


    /**
     * Value is unsigned numeric chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function unsigned($str)
    {
        return ( is_numeric($str) && (int)$str > 0 ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is telnumber format only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function telnumber($str)
    {
        return ( preg_match('/\A[0-9]{2,4}\-[0-9]{3,4}\-[0-9]{4}\Z/u', $str) )? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is "kana" chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function kana($str)
    {
        $str = str_replace('　', ' ', $str);
        return ( preg_match("/\A[ァ-ヴー\s]+$/u", $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is "hiragana" chars only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function hiragana($str)
    {
        $str = str_replace('　', ' ', $str);
        return ( preg_match("/^[ぁ-ゞ]+$/u", $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * convert kana
     * 
     * @access public
     * @param  string $str
     * @return string
     */
    public function conv_kana($str, $cond)
    {
        return mb_convert_kana($str, $cond);
    }


    // --------------------------------------------------


    /**
     * convert number
     * 
     * @access public
     * @param  string $str
     * @return string
     */
    public function conv_num($str)
    {
        return str_repalce(
            array('０', '１', '２', '３', '４', '５', '６', '７', '８', '９'),
            array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'),
            $str
        );
    }


    // --------------------------------------------------


    /**
     * Value is Zipcode-format only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function zipcode($str)
    {
        return ( preg_match('/\A[0-9]{3}\-[0-9]{4}\Z/u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is Date-format only
     * 
     * @access public
     * @param  string $str
     * @param  string $cond
     * @return bool
     */
    public function dateformat($str, $cond)
    {
        if ( ! $cond )
        {
            $cond = '-';
        }
        $sep = preg_quote($cond, '/');
        return ( preg_match('/\A[0-9]{4}' . $sep . '[0-9]{2}' . $sep . '[0-9]{2}\Z/u', $str) ) ? TRUE : FALSE;
    }


    // --------------------------------------------------


    /**
     * Value is past-date only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function past_date($str)
    {
        $validated = false;
        try
        {
            $date = new DateTime($str);
            $validated = ( $date->getTimestamp() < time() ) ? TRUE : FALSE;
        }
        catch (Exception $e)
        {
            $validated = false;
        }

        return $validated;
    }


    // --------------------------------------------------


    /**
     * Value is future date only
     * 
     * @access public
     * @param  string $str
     * @return bool
     */
    public function future_date($str)
    {
        $validated = false;
        try
        {
            $date = new DateTime($str);
            $validated = ( $date->getTimestamp() >= time() ) ? TRUE : FALSE;
        }
        catch (Exception $e)
        {
            $validated = false;
        }

        return $validated;
    }


    // --------------------------------------------------


    /**
     * Value is exact date only
     * 
     * @access public
     * @param  string $str
     * @param  string $cond
     * @return bool
     */
    public function exact_date($str, $cond)
    {
        if ( ! $cond )
        {
            $cond = '-';
        }
        $exp = explode($cond, $str);
        return checkdate($exp[1], $exp[2], $exp[0]);
    }


    // --------------------------------------------------


    /**
     * Value is matched other field value
     * 
     * @access public
     * @param  string $str
     * @param  string $cond
     * @return bool
     */
    public function matches($str, $cond)
    {
        $matchValue = ( isset($this->currentValues[$cond]) )
                        ? $this->currentValues[$cond]
                        : null;

        return ( $str === $matchValue ) ? TRUE : FALSE;
    }
}
