<?php

namespace Terrier;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $config = new \ReflectionProperty('\Terrier\Config', '_config');
        $config->setAccessible(true);
        $config->setValue(array());

        $initialized = new \ReflectionProperty('\Terrier\Config', 'initialized');
        $initialized->setAccessible(true);
        $initialized->setValue(false);
    }

    public function testInit()
    {
        Config::init(array('foo' => 'bar'));
        $this->assertEquals('bar', Config::get('foo'));
    }

    /**
     * @expectedException \Terrier\Exception
     */
    public function testInitThrowsExceptionWhenSecondTimes()
    {
        Config::init(array('foo' => 'bar'));
        Config::init(array('foo' => 'baz'));
    }

    public function testSet()
    {
        Config::set('Lorem', 'ipsum');
        $this->assertEquals('ipsum', Config::get('Lorem'));
    }

    public function testGetExsitingKey()
    {
        Config::init(array('foo' => 'bar'));
        $this->assertEquals('bar', Config::get('foo'));
    }

    public function testGetReturnsDefaultValue()
    {
        Config::init(array('foo' => 'bar'));
        $this->assertEquals('baz', Config::get('notexists', 'baz'));
    }

    public function testLoad()
    {
        $config = Config::load('config');
        $this->assertInternalType('array', $config);
    }

    /**
     * @expectedException \Terrier\Exception
     */
    public function testLoadThrowsExceptionWhenNotExistsConfig()
    {
        Config::load('foo');
    }
}
