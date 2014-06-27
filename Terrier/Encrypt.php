<?php

namespace Terrier;

class Encrypt
{
    /**
     * Encode xor
     * 
     * @access public
     * @param  string $string
     * @return string
     */
    public static function encode($string)
    {
        $enc        = '';
        $rand       = sha1(bin2hex(openssl_random_pseudo_bytes(16)));
        $randLength = strlen($rand);
        $length     = strlen($string);

        for ( $i = 0; $i < $length; $i++ )
        {
            $tip  = substr($rand, ($i % $randLength), 1);
            $enc .= $tip;
            $enc .= ($tip ^ substr($string, $i, 1));
        }

        return static::_merge($enc);
    }


    // ----------------------------------------


    /**
     * Decode xor
     * 
     * @access public
     * @param  string $string
     * @return string
     */
    public static function decode($string)
    {
        $dat    = static::_merge($string);
        $dec    = '';
        $length = strlen($string);

        for ( $i = 0; $i < $length; $i += 2 )
        {
            $dec .= (substr($dat, $i, 1) ^ substr($dat, $i + 1, 1));
        }

        return $dec;
    }


    // ----------------------------------------


    /**
     * XOR merge
     * 
     * @access protected
     * @param  string $string
     * @return string
     */
    private static function _merge($string)
    {
        $ret        = '';
        $hash       = sha1(Config::get('encrypt_cipher'));
        $hashLength = strlen($hash);
        $length     = strlen($string);

        for ( $i = 0; $i < $length; $i++ )
        {
            $tip = substr($string, $i, 1);
            $ret .= ($tip ^ substr($hash, ($i % $hashLength), 1));
        }

        return $ret;
    }
}
