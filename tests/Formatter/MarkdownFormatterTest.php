<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Formatter;

use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Formatter\MarkdownFormatter;

class MarkdownFormatterTest extends TestCase
{
    public function testFormatter(): void
    {
        $parser = $this->createMock('Knp\Bundle\MarkdownBundle\MarkdownParserInterface');
        $parser->expects($this->any())->method('transformMarkdown')->will($this->returnValue('<b>Salut</b>'));
        $formatter = new MarkdownFormatter($parser);

        $this->assertSame('<b>Salut</b>', $formatter->transform('*Salut*'));
    }
}
