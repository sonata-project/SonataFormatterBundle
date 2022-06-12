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
use Sonata\FormatterBundle\Formatter\RawFormatter;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Sandbox\SecurityError;
use Twig\Template;

class PoolTest extends TestCase
{
    public function testPool(): void
    {
        $formatter = new RawFormatter();
        $env = $this->createMock(Environment::class);
        $template = $this->createMock(Template::class);

        $template->expects(static::once())->method('render')->willReturn('Salut');

        $env->expects(static::once())->method('createTemplate')->willReturn($template);

        $pool = $this->getPool();

        static::assertFalse($pool->has('foo'));

        $pool->add('foo', $formatter, $env);

        static::assertTrue($pool->has('foo'));

        static::assertSame('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testNonExistantFormatter(): void
    {
        $this->expectException(\RuntimeException::class);

        $pool = $this->getPool();
        $pool->get('foo');
    }

    public function testSyntaxError(): void
    {
        $formatter = new RawFormatter();
        $env = $this->createMock(Environment::class);
        $template = $this->createMock(Template::class);

        $template->expects(static::once())
            ->method('render')
            ->will(static::throwException(new SyntaxError('Error')));

        $env->expects(static::once())->method('createTemplate')->willReturn($template);

        $pool = $this->getPool();
        $pool->add('foo', $formatter, $env);

        static::assertSame('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testTwigSandboxSecurityError(): void
    {
        $formatter = new RawFormatter();
        $env = $this->createMock(Environment::class);
        $template = $this->createMock(Template::class);

        $template->expects(static::once())
            ->method('render')
            ->will(static::throwException(new SecurityError('Error')));

        $env->expects(static::once())->method('createTemplate')->willReturn($template);

        $pool = $this->getPool();
        $pool->add('foo', $formatter, $env);

        static::assertSame('Salut', $pool->transform('foo', 'Salut'));
    }

    public function testUnexpectedException(): void
    {
        $this->expectException(\RuntimeException::class);

        $formatter = new RawFormatter();
        $env = $this->createMock(Environment::class);
        $template = $this->createMock(Template::class);

        $template->expects(static::once())
            ->method('render')
            ->will(static::throwException(new \RuntimeException('Error')));

        $env->expects(static::once())->method('createTemplate')->willReturn($template);

        $pool = $this->getPool();
        $pool->add('foo', $formatter, $env);

        $pool->transform('foo', 'Salut');
    }

    public function testDefaultFormatter(): void
    {
        $pool = new Pool('default');
        $pool->setLogger($this->createMock(LoggerInterface::class));

        static::assertSame('default', $pool->getDefaultFormatter());
    }

    private function getPool()
    {
        $pool = new Pool('whatever');
        $pool->setLogger($this->createMock(LoggerInterface::class));

        return $pool;
    }
}
