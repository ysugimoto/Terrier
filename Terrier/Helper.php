<?php

namespace Terrier;

class Helper
{
    public static function escape($str)
    {
        return ( is_array($str) )
                 ? array_map(array('static', 'escape'), $str)
                 : htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
