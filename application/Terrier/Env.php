<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * php-ini setter/getter
 *
 * @namespace Terrier
 * @class Env
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Env
{
    /**
     * Get ini value
     *
     * @method get
     * @public static
     * @param {string} $setting
     * @return {mixed}
     */
    public static function get($setting)
    {
        return ini_get($setting);
    }

    // ----------------------------------------


    /**
     * Set ini value
     *
     * @method set
     * @public static
     * @param {string} $setting
     * @param {string} $value
     * @return {void}
     */
    public static function set($setting, $value)
    {
        return ini_set($setting, $value);
    }
}
