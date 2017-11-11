<?php

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
use Sonata\FormatterBundle\Form\EventListener\FormatterListener;
use Sonata\FormatterBundle\Formatter\Pool;
use Symfony\Component\Form\FormEvent;

class FormatterListenerTest extends TestCase
{
    public function testWithInvalidFormatter()
    {
        $this->setExpectedException('RuntimeException');

        $pool = $this->getPool();

        $listener = new FormatterListener($pool, '[format]', '[source]', '[target]');

        $event = new FormEvent($this->createMock('Symfony\Component\Form\Test\FormInterface'), [
            'format' => 'error',
            'source' => 'data',
            'target' => null,
        ]);

        $listener->postSubmit($event);
    }

    public function testWithValidFormatter()
    {
        $formatter = $this->createMock('Sonata\FormatterBundle\Formatter\FormatterInterface');
        $formatter->expects($this->once())->method('transform')->will($this->returnCallback(function ($text) {
            return strtoupper($text);
        }));

        $pool = $this->getPool();
        $pool->add('myformat', $formatter);

        $listener = new FormatterListener($pool, '[format]', '[source]', '[target]');

        $event = new FormEvent($this->createMock('Symfony\Component\Form\Test\FormInterface'), [
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
        $pool->setLogger($this->createMock('Psr\Log\LoggerInterface'));

        return $pool;
    }
}
