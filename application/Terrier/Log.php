<?php

namespace Terrier;

/**
 *
 * Terrier Mailform application
 * Application Logger
 *
 * @namespace Terrier
 * @class Log
 * @author Yoshiaki Sugimoto <sugimoto@wnotes.net>
 */
class Log
{
    /**
     * File poiner
     *
     * @property $fp
     * @private static
     * @type Resource
     */
    private static $fp;

    /**
     * Info level
     *
     * @const LEVEL_INFO
     * @type int
     * @default 0x01
     */
    const LEVEL_INFO = 0x01;

    /**
     * Warn level
     *
     * @const LEVEL_WARN
     * @type int
     * @default 0x10
     */
    const LEVEL_WARN = 0x10;

    /**
     * Write log
     *
     * @method write
     * @public static
     * @param string $message
     * @param int $level
     * @return void
     */
    public static function write($message, $level)
    {
        $date  = new \DateTime();
        $write = '';

        // check logging level
        if ( $level <= self::LEVEL_INFO && Config::get('logging_level') > 0 )
        {
            $write = '[' . $date->format('Y-m-d H:i:s') . ' INFO] ';
        }
        else if ( $level <= self::LEVEL_WARN && Config::get('logging_level') > 1 )
        {
            $write = '[' . $date->format('Y-m-d H:i:s') . ' WARN] ';
        }

        if ( ! empty($write) )
        {
            if ( ! static::$fp )
            {
                static::$fp = @fopen(TMP_PATH . 'log/' . $date->format('Y-m-d') . '.log', 'ab');
            }
            flock(static::$fp, LOCK_EX);
            fwrite(static::$fp, $write . $message . "\n");
            flock(static::$fp, LOCK_UN);
        }
    }



    // ----------------------------------------


    /**
     * Close logfile pointer if opened
     *
     * @method close
     * @public static
     * @return void
     */
    public static function close()
    {
        if ( is_resource(static::$fp) )
        {
            fclose(static::$fp);
            // force gc
            static::$fp = null;
        }
    }
}
