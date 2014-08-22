<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * String Encryption class
 *
 * @namespace Terrier
 * @class Encrypt
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Encrypt
{
    /**
     * Encode xor
     *
     * @method encode
     * @public static
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
     * @decode
     * @public static
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
     * @method _merge
     * @protected static
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
