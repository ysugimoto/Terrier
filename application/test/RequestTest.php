<?php

namespace Terrier;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $instance = new \ReflectionProperty('\Terrier\Request', 'instance');
        $instance->setAccessible(true);
        $instance->setValue(null);
    }

    public function testGet()
    {
        $_GET['foo'] = 'bar';
        $this->assertEquals('bar', Request::get('foo'));
    }

    public function testGetDefaultValue()
    {
        $_GET['foo'] = 'bar';
        $this->assertEquals('baz', Request::get('bar', 'baz'));
    }

    public function testGetAll()
    {
        $_GET['foo'] = 'bar';
        $get = Request::getAll();

        $this->assertInternalType('array', $get);
        $this->assertArrayHasKey('foo', $get);
    }

    public function testPost()
    {
        $_POST['foo'] = 'bar';
        $this->assertEquals('bar', Request::post('foo'));
    }

    public function testPostDefaultValue()
    {
        $_POST['foo'] = 'bar';
        $this->assertEquals('baz', Request::post('bar', 'baz'));
    }

    public function testPostAll()
    {
        $_POST['foo'] = 'bar';
        $post = Request::postAll();

        $this->assertInternalType('array', $post);
        $this->assertArrayHasKey('foo', $post);
    }

    public function testServer()
    {
        $this->assertStringEndsWith('phpunit', Request::server('PHP_SELF'));
    }

    public function testServerAtLowerCase()
    {
        $this->assertStringEndsWith('phpunit', Request::server('php_self'));
    }

    public function testServerDefaultValue()
    {
        $_SERVER['foo'] = 'bar';
        $this->assertEquals('baz', Request::server('bar', 'baz'));
    }

    public function testServerAll()
    {
        $_SERVER['foo'] = 'bar';
        $server = Request::serverAll();

        $this->assertInternalType('array', $server);
        $this->assertArrayHasKey('PHP_SELF', $server);
    }

    public function testCookie()
    {
        $_COOKIE['foo'] = 'bar';
        $this->assertEquals('bar', Request::cookie('foo'));
    }

    public function testCookieDefaultValue()
    {
        $_COOKIE['foo'] = 'bar';
        $this->assertEquals('baz', Request::cookie('bar', 'baz'));
    }

    public function testCookieAll()
    {
        $_COOKIE['foo'] = 'bar';
        $cookie = Request::cookieAll();

        $this->assertInternalType('array', $cookie);
        $this->assertArrayHasKey('foo', $cookie);
    }

    public function testIpFromRemoteAddr()
    {
        $vip = '49.212.152.36';
        $_SERVER['REMOTE_ADDR'] = $vip;

        $this->assertEquals($vip, Request::ip());
    }

    public function testIpFromXForwardedForAndTrusted()
    {
        $vip = '49.212.152.36';
        $proxy = '50.212.152.36';

        $_SERVER['REMOTE_ADDR']     = $vip;
        $_SERVER['X_FORWARDED_FOR'] = $proxy;

        // trust
        Config::set('trusted_proxys', array($vip));

        $this->assertEquals($proxy, Request::ip());
    }

    public function testIpFromXForwardedForAndNotTrusted()
    {
        $vip = '49.212.152.36';
        $proxy = '50.212.152.36';

        $_SERVER['REMOTE_ADDR']     = $vip;
        $_SERVER['X_FORWARDED_FOR'] = $proxy;

        Config::set('trusted_proxys', array());

        $this->assertNotEquals($proxy, Request::ip());
        $this->assertEquals($vip, Request::ip());
    }

    public function testIpFromHttpClientIpAndTrusted()
    {
        $vip = '49.212.152.36';
        $proxy = '50.212.152.36';

        $_SERVER['REMOTE_ADDR']     = $vip;
        $_SERVER['HTTP_CLIENT_IP'] = $proxy;

        // trust
        Config::set('trusted_proxys', array($vip));

        $this->assertEquals($proxy, Request::ip());
    }

    public function testIpFromHttpClientIpAndNotTrusted()
    {
        $vip = '49.212.152.36';
        $proxy = '50.212.152.36';

        $_SERVER['REMOTE_ADDR']     = $vip;
        $_SERVER['HTTP_CLIENT_IP'] = $proxy;

        Config::set('trusted_proxys', array());

        $this->assertNotEquals($proxy, Request::ip());
        $this->assertEquals($vip, Request::ip());
    }
}
