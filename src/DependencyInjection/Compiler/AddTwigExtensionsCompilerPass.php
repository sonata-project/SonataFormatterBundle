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

use Sonata\FormatterBundle\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig\RuntimeLoader\ContainerRuntimeLoader;

/**
 * @internal
 *
 * @author Jordi Sala <jordism91@gmail.com>
 */
final class AddTwigExtensionsCompilerPass implements CompilerPassInterface
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

                $this->addExtensions($container, $code, $env, $formatterConfig['extensions']);

                $container->setDefinition(sprintf('sonata.formatter.twig.env.%s', $code), $env);
            }
        }
    }

    /**
     * @param string[] $extensions
     */
    private function addExtensions(
        ContainerBuilder $container,
        string $code,
        Definition $env,
        array $extensions
    ): void {
        $runtimes = [];

        foreach ($extensions as $extension) {
            $extensionDefinition = $container->getDefinition($extension);
            $extensionClass = $extensionDefinition->getClass();

            if (null === $extensionClass || !is_a($extensionClass, ExtensionInterface::class, true)) {
                throw new \InvalidArgumentException(sprintf(
                    'Extension "%s" added to formatter "%s" do not implement %s interface.',
                    $extension,
                    $code,
                    ExtensionInterface::class
                ));
            }

            $env->addMethodCall('addExtension', [new Reference($extension)]);

            $extensionRuntimes = $extensionClass::getAllowedRuntimes();

            foreach ($extensionRuntimes as $extensionRuntime) {
                $runtimeDefinition = $container->getDefinition($extensionRuntime);
                $runtimeClass = $runtimeDefinition->getClass();

                if (null !== $runtimeClass) {
                    $runtimes[$runtimeClass] = new Reference($extensionRuntime);
                }
            }
        }

        if ([] !== $runtimes) {
            $runtimeLoader = new Definition(ContainerRuntimeLoader::class, [
                ServiceLocatorTagPass::register($container, $runtimes),
            ]);

            $container->setDefinition(sprintf('sonata.formatter.twig.runtime_loader.%s', $code), $runtimeLoader);

            $env->addMethodCall('addRuntimeLoader', [new Reference(sprintf('sonata.formatter.twig.runtime_loader.%s', $code))]);
        }
    }
}
