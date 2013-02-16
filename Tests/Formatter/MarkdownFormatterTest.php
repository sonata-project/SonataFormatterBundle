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

use Sonata\FormatterBundle\Formatter\MarkdownFormatter;

class MarkdownFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatter()
    {
        $parser = $this->getMock('Knp\Bundle\MarkdownBundle\MarkdownParserInterface');
        $parser->expects($this->any())->method('transformMarkdown')->will($this->returnValue('<b>Salut</b>'));
        $formatter = new MarkdownFormatter($parser);

        $this->assertEquals("<b>Salut</b>", $formatter->transform("*Salut*"));
    }
}
