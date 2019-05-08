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
use Psr\Log\LoggerInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Formatter\TextFormatter;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Sandbox\SecurityError;
use Twig\Template;

class PoolTest extends TestCase
{
    public function testPool(): void
    {
        $formatter = new TextFormatter();
        $env = $this->createMock(Environment::class);
        $template = $this->createMock(Template::class);

        $template->expects($this->once())->method('render')->willReturn('Salut');

        $env->expects($this->once())->method('createTemplate')->willReturn($template);

        $pool = $this->getPool();

        $this->assertFalse($pool->has('foo'));

        $pool->add('foo', $formatter, $env);

        $this->assertTrue($pool->has('foo'));

        $this->assertSame('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testNonExistantFormatter(): void
    {
        $this->expectException('RuntimeException');

        $pool = $this->getPool();
        $pool->get('foo');
    }

    public function testSyntaxError(): void
    {
        $formatter = new TextFormatter();
        $env = $this->createMock(Environment::class);
        $template = $this->createMock(Template::class);

        $template->expects($this->once())
            ->method('render')
            ->will($this->throwException(new SyntaxError('Error')));

        $env->expects($this->once())->method('createTemplate')->willReturn($template);

        $pool = $this->getPool();
        $pool->add('foo', $formatter, $env);

        $this->assertSame('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testTwigSandboxSecurityError(): void
    {
        $formatter = new TextFormatter();
        $env = $this->createMock(Environment::class);
        $template = $this->createMock(Template::class);

        $template->expects($this->once())
            ->method('render')
            ->will($this->throwException(new SecurityError('Error')));

        $env->expects($this->once())->method('createTemplate')->willReturn($template);

        $pool = $this->getPool();
        $pool->add('foo', $formatter, $env);

        $this->assertSame('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testUnexpectedException(): void
    {
        $this->expectException('RuntimeException');

        $formatter = new TextFormatter();
        $env = $this->createMock(Environment::class);
        $template = $this->createMock(Template::class);

        $template->expects($this->once())
            ->method('render')
            ->will($this->throwException(new \RuntimeException('Error')));

        $env->expects($this->once())->method('createTemplate')->willReturn($template);

        $pool = $this->getPool();
        $pool->add('foo', $formatter, $env);

        $pool->transform('foo', 'Salut');
    }

    public function testDefaultFormatter(): void
    {
        $pool = new Pool('default');
        $pool->setLogger($this->createMock(LoggerInterface::class));

        $this->assertSame('default', $pool->getDefaultFormatter());
    }

    private function getPool()
    {
        $pool = new Pool('whatever');
        $pool->setLogger($this->createMock(LoggerInterface::class));

        return $pool;
    }
}
