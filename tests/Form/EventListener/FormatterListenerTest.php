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

namespace Sonata\FormatterBundle\Tests\Form\EventListener;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sonata\FormatterBundle\Form\EventListener\FormatterListener;
use Sonata\FormatterBundle\Formatter\FormatterInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

class FormatterListenerTest extends TestCase
{
    public function testWithInvalidFormatter(): void
    {
        $this->expectException('RuntimeException');

        $pool = $this->getPool();

        $listener = new FormatterListener($pool, '[format]', '[source]', '[target]');

        $event = new FormEvent($this->createMock(FormInterface::class), [
            'format' => 'error',
            'source' => 'data',
            'target' => null,
        ]);

        $listener->postSubmit($event);
    }

    public function testWithValidFormatter(): void
    {
        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects($this->once())->method('transform')->willReturnCallback(static function ($text) {
            return strtoupper($text);
        });

        $pool = $this->getPool();
        $pool->add('myformat', $formatter);

        $listener = new FormatterListener($pool, '[format]', '[source]', '[target]');

        $event = new FormEvent($this->createMock(FormInterface::class), [
            'format' => 'myformat',
            'source' => 'data',
            'target' => null,
        ]);

        $listener->postSubmit($event);

        $expected = [
            'format' => 'myformat',
            'source' => 'data',
            'target' => 'DATA',
        ];

        $this->assertSame($expected, $event->getData());
    }

    private function getPool()
    {
        $pool = new Pool('whatever');
        $pool->setLogger($this->createMock(LoggerInterface::class));

        return $pool;
    }
}
