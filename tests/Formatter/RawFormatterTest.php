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
use Sonata\FormatterBundle\Formatter\RawFormatter;

class RawFormatterTest extends TestCase
{
    /**
     * NEXT_MAJOR: Remove the group when deleting FormatterInterface.
     *
     * @group legacy
     */
    public function testFormatter(): void
    {
        $formatter = new RawFormatter();

        $this->assertSame('Salut', $formatter->transform('Salut'));
        $this->assertSame('<p>Salut<br />Ca va ?</p>', $formatter->transform('<p>Salut<br />Ca va ?</p>'));
    }
}
