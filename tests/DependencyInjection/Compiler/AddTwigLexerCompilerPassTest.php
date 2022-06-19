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

namespace Sonata\FormatterBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sonata\FormatterBundle\DependencyInjection\Compiler\AddTwigLexerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;
use Twig\Lexer;

/**
 * @author Jordi Sala <jordism91@gmail.com>
 */
final class AddTwigLexerCompilerPassTest extends AbstractCompilerPassTestCase
{
    public function testAddsLexerToTwigEnvironments(): void
    {
        $this->container->setParameter('sonata.formatter.configuration.formatters', [
            'text' => []
        ]);

        $this->container->register('sonata.formatter.twig.env.text')
            ->setClass(Environment::class);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sonata.formatter.twig.env.text',
            'setLexer',
            [
                new Definition(Lexer::class, [
                    new Reference('sonata.formatter.twig.env.text'),
                    [
                        'tag_comment' => ['<#', '#>'],
                        'tag_block' => ['<%', '%>'],
                        'tag_variable' => ['<%=', '%>'],
                    ],
                ]),
            ]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddTwigLexerCompilerPass());
    }
}
