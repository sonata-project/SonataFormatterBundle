<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Formatter;

use Sonata\FormatterBundle\Formatter\TextFormatter;

class TextFormatterTest extends \PHPUnit_Framework_TestCase
{

    public function testFormatter()
    {
        $formatter = new TextFormatter();

        $this->assertEquals("Salut", $formatter->transform("Salut"));
        $this->assertEquals("Salut<br />\nCa va ?", $formatter->transform("Salut\nCa va ?"));
    }
}
