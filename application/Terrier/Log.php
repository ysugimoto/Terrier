<?php

namespace Terrier;

class Log
{
    private static $fp;

    const LEVEL_INFO = 0x01;
    const LEVEL_WARN = 0x10;

    public static function write($message, $level)
    {
        $date  = new \DateTime();
        $write = '';

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
