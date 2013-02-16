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

use Sonata\FormatterBundle\Formatter\RawFormatter;
use Sonata\FormatterBundle\Formatter\Pool;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    public function testPool()
    {
        $formatter = new RawFormatter();
        $env = $this->getMock('\Twig_Environment');
        $env->expects($this->once())->method('render')->will($this->returnValue('Salut'));

        $pool = new Pool;

        $this->assertFalse($pool->has('foo'));

        $pool->add('foo', $formatter, $env);

        $this->assertTrue($pool->has('foo'));

        $this->assertEquals('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testNonExistantFormatter()
    {
        $this->setExpectedException('RuntimeException');

        $pool = new Pool;
        $pool->get('foo');
    }

    public function testSyntaxError()
    {

        $formatter = new RawFormatter();
        $env = $this->getMock('\Twig_Environment');
        $env->expects($this->once())->method('render')->will($this->throwException(new \Twig_Error_Syntax('Error')));

        $pool = new Pool;
        $pool->add('foo', $formatter, $env);

        $this->assertEquals('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testTwig_Sandbox_SecurityError()
    {

        $formatter = new RawFormatter();
        $env = $this->getMock('\Twig_Environment');
        $env->expects($this->once())->method('render')->will($this->throwException(new \Twig_Sandbox_SecurityError('Error')));

        $pool = new Pool;
        $pool->add('foo', $formatter, $env);

        $this->assertEquals('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testUnexpectedException()
    {
        $this->setExpectedException('RuntimeException');

        $formatter = new RawFormatter();
        $env = $this->getMock('\Twig_Environment');
        $env->expects($this->once())->method('render')->will($this->throwException(new \RuntimeException('Error')));

        $pool = new Pool;
        $pool->add('foo', $formatter, $env);

        $pool->transform('foo', 'Salut');
    }
}
