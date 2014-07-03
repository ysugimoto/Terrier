<?php

namespace Terrier;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function testEscapeString()
    {
        $str = '<i>foo</i>';

        $this->assertEquals('&lt;i&gt;foo&lt;/i&gt;', Helper::escape($str));
    }

    public function testEscapeArrayStrings()
    {
        $ary = array(
            '<s>foo</s>',
            '<i>bar</i>'
        );

        $escaped = Helper::escape($ary);
        $this->assertInternalType('array', $escaped);
        $this->assertEquals('&lt;s&gt;foo&lt;/s&gt;', $escaped[0]);
        $this->assertEquals('&lt;i&gt;bar&lt;/i&gt;', $escaped[1]);
    }
}
