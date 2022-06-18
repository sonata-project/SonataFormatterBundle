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

namespace Sonata\FormatterBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Lexer;

/**
 * @internal
 *
 * @author Jordi Sala <jordism91@gmail.com>
 */
final class AddTwigLexerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sonata.formatter.configuration.formatters')) {
            return;
        }

        $formatters = $container->getParameter('sonata.formatter.configuration.formatters');
        \assert(\is_array($formatters));

        foreach ($formatters as $code => $formatterConfig) {
            if (0 !== \count($formatterConfig['extensions'])) {
                $env = $container->getDefinition(sprintf('sonata.formatter.twig.env.%s', $code));

                $lexer = new Definition(Lexer::class, [new Reference(sprintf('sonata.formatter.twig.env.%s', $code)), [
                    'tag_comment' => ['<#', '#>'],
                    'tag_block' => ['<%', '%>'],
                    'tag_variable' => ['<%=', '%>'],
                ]]);
                $lexer->setPublic(false);

                $container->setDefinition(sprintf('sonata.formatter.twig.lexer.%s', $code), $lexer);

                $env->addMethodCall('setLexer', [new Reference(sprintf('sonata.formatter.twig.lexer.%s', $code))]);

                $container->setDefinition(sprintf('sonata.formatter.twig.env.%s', $code), $env);
            }
        }
    }
}
