<?php

namespace Terrier;

class Env
{
    public static function get($setting)
    {
        return ini_get($setting);
    }

    public static function set($setting, $value)
    {
        return ini_set($setting, $value);
    }
}
