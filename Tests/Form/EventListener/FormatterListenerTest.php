<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Form\EventListener;

use Sonata\FormatterBundle\Form\EventListener\FormatterListener;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Formatter\FormatterInterface;
use Symfony\Component\Form\Test\FormInterface;

use Symfony\Component\Form\FormEvent;

class FormatterListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testWithInvalidFormatter()
    {
        $this->setExpectedException('RuntimeException');

        $pool = new Pool();

        $listener = new FormatterListener($pool, '[format]', '[source]', '[target]');

        $event = new FormEvent($this->getMock('Symfony\Component\Form\Test\FormInterface'), array(
            'format' => 'error',
            'source' => 'data',
            'target' => null,
        ));

        $listener->postSubmit($event);
    }

    public function testWithValidFormatter()
    {
        $formatter = $this->getMock('Sonata\FormatterBundle\Formatter\FormatterInterface');
        $formatter->expects($this->once())->method('transform')->will($this->returnCallback(function($text) {
            return strtoupper($text);
        }));

        $pool = new Pool();
        $pool->add('myformat', $formatter);

        $listener = new FormatterListener($pool, '[format]', '[source]', '[target]');

        $event = new FormEvent($this->getMock('Symfony\Component\Form\Test\FormInterface'), array(
            'format' => 'myformat',
            'source' => 'data',
            'target' => null,
        ));

        $listener->postSubmit($event);

        $expected =  array(
            'format' => 'myformat',
            'source' => 'data',
            'target' => 'DATA'
        );

        $this->assertEquals($expected, $event->getData());
    }

}
