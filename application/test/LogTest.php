<?php

namespace Terrier;

class LogTest extends \PHPUnit_Framework_TestCase
{
    public $filePath;

    public function setUp()
    {
        $date = new \DateTime();
        $this->filePath = TMP_PATH . 'log/' . $date->format('Y-m-d') . '.log';

        if ( file_exists($this->filePath) )
        {
            unlink($this->filePath);
        }
    }

    public function tearDown()
    {
        Log::close();
    }

    public function testLogWriteInfo()
    {
        Config::set('logging_level', 1);
        $message = 'info';
        Log::write($message, Log::LEVEL_INFO);

        $this->assertFileExists($this->filePath);
    }

    public function testLogNoWriteInfo()
    {
        Config::set('logging_level', 0);
        $message = 'info';
        Log::write($message, Log::LEVEL_INFO);

        $this->assertFileNotExists($this->filePath);
    }

    public function testLogWriteWarn()
    {
        Config::set('logging_level', 2);
        $message = 'warn';
        Log::write($message, Log::LEVEL_WARN);

        $this->assertFileExists($this->filePath);
    }

    public function testLogNoWriteWarn()
    {
        Config::set('logging_level', 1);
        $message = 'warn';
        Log::write($message, Log::LEVEL_WARN);

        $this->assertFileNotExists($this->filePath);
    }
}
