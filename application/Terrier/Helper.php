<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Template helper
 *
 * @namespace Terrier
 * @class Helper
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Helper
{
    /**
     * Escape string
     *
     * @method esacpe
     * @pubic static
     * @param mixed $str
     * @return mixed
     */
    public static function escape($str)
    {
        return ( is_array($str) )
                 ? array_map(array('static', 'escape'), $str)
                 : htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
