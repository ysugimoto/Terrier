<?php

namespace Terrier;

class EncryptTest extends \PHPUnit_Framework_TestCase
{
    public function testEncodeDecodeMatch()
    {
        $str = 'foobar';
        $encoded = Encrypt::encode($str);

        $this->assertEquals($str, Encrypt::decode($encoded));
    }

    public function testEncodeDecodeNotMatch()
    {
        $str = 'foobar';
        $encoded = Encrypt::encode($str);

        Config::set('encrypt_cipher', 'LoremIpsum');

        $this->assertNotEquals($str, Encrypt::decode($encoded));
    }
}
