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

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Formatter\MarkdownFormatter;

class MarkdownFormatterTest extends TestCase
{
    /**
     * NEXT_MAJOR: Remove the group when deleting FormatterInterface.
     *
     * @group legacy
     */
    public function testFormatter(): void
    {
        $parser = $this->createMock(MarkdownParserInterface::class);
        $parser->expects($this->any())->method('transformMarkdown')->willReturn('<b>Salut</b>');
        $formatter = new MarkdownFormatter($parser);

        $this->assertSame('<b>Salut</b>', $formatter->transform('*Salut*'));
    }
}
