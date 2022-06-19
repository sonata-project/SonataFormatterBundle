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
use Sonata\FormatterBundle\DependencyInjection\Compiler\AddTwigExtensionsCompilerPass;
use Sonata\FormatterBundle\Extension\GistExtension;
use Sonata\FormatterBundle\Extension\MediaExtension;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Twig\SecurityPolicyContainerAware;
use Sonata\MediaBundle\Twig\MediaRuntime;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;
use Twig\Extension\SandboxExtension;

/**
 * @author Jordi Sala <jordism91@gmail.com>
 */
final class AddTwigExtensionsCompilerPassTest extends AbstractCompilerPassTestCase
{
    public function testAddNonExtensionsToFormatter(): void
    {
        $this->container->setParameter('sonata.formatter.configuration.formatters', [
            'text' => [
                'extensions' => [
                    'random_service',
                ],
            ],
        ]);
        $this->container->register('sonata.formatter.twig.env.text', Environment::class);
        $this->container->register('random_service', Pool::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Extension "random_service" added to formatter "text" do not implement Sonata\FormatterBundle\Extension\ExtensionInterface interface.'
        );

        $this->compile();
    }

    public function testAddExtensionsToEnvironment(): void
    {
        $this->container->setParameter('sonata.formatter.configuration.formatters', [
            'text' => [
                'extensions' => [
                    'sonata.formatter.twig.gist',
                    'sonata.formatter.twig.media',
                ],
            ],
        ]);
        $this->container->register('sonata.formatter.twig.env.text', Environment::class);
        $this->container->register('sonata.formatter.twig.gist', GistExtension::class);
        $this->container->register('sonata.formatter.twig.media', MediaExtension::class);
        $this->container->register('sonata.media.twig.runtime', MediaRuntime::class);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sonata.formatter.twig.env.text',
            'addExtension',
            [
                new Definition(SandboxExtension::class, [
                    new Definition(SecurityPolicyContainerAware::class, [
                        new Reference('service_container'),
                        [
                            'sonata.formatter.twig.gist',
                            'sonata.formatter.twig.media',
                        ],
                    ]),
                    true,
                ]),
            ],
            0
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sonata.formatter.twig.env.text',
            'addExtension',
            [
                new Reference('sonata.formatter.twig.gist'),
            ],
            1
        );

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sonata.formatter.twig.env.text',
            'addExtension',
            [
                new Reference('sonata.formatter.twig.media'),
            ],
            2
        );

        static::assertTrue(
            $this->container->getDefinition('sonata.formatter.twig.env.text')
                ->hasMethodCall('addRuntimeLoader')
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddTwigExtensionsCompilerPass());
    }
}
