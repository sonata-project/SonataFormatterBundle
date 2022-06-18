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
use Sonata\FormatterBundle\Twig\SecurityPolicyContainerAware;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Extension\SandboxExtension;
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
            $envId = sprintf('sonata.formatter.twig.env.%s', $code);

            if ($container->hasDefinition($envId)) {
                $this->addExtensions(
                    $container,
                    $code,
                    $container->getDefinition(sprintf('sonata.formatter.twig.env.%s', $code)),
                    $formatterConfig['extensions']
                );
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
        $env->addMethodCall('addExtension', [
            new Definition(SandboxExtension::class, [
                new Definition(SecurityPolicyContainerAware::class, [
                    new Reference('service_container'),
                    $extensions,
                ]),
                true,
            ]),
        ]);

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
            $env->addMethodCall('addRuntimeLoader', [
                new Definition(ContainerRuntimeLoader::class, [
                    ServiceLocatorTagPass::register($container, $runtimes),
                ]),
            ]);
        }
    }
}
