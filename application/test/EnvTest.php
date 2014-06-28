<?php

namespace Terrier;

class EnvTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvGet()
    {
        $this->assertEquals('Asia/Tokyo', Env::get('date.timezone'));
    }

    public function testEnvSet()
    {
        $def = Env::get('default_charset');
        if ( $def === 'UTF-8' )
        {
            Env::set('default_charset', 'EUC-JP');
            $this->assertNotEquals($def, Env::get('default_charset'));
        }
        else
        {
            Env::set('default_charset', 'UTF-8');
            $this->assertNotEquals($def, Env::get('default_charset'));
        }

        Env::set('default_charset', $def);
    }
}
