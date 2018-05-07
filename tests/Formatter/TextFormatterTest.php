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
use Sonata\FormatterBundle\Formatter\TextFormatter;

class TextFormatterTest extends TestCase
{
    public function testFormatter(): void
    {
        $formatter = new TextFormatter();

        $this->assertSame('Salut', $formatter->transform('Salut'));
        $this->assertSame("Salut<br />\nCa va ?", $formatter->transform("Salut\nCa va ?"));
    }
}
