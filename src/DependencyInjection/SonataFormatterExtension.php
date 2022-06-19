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

namespace Sonata\FormatterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Twig\Environment;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class SonataFormatterExtension extends Extension
{
    /**
     * Loads the url shortener configuration.
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('formatter.php');
        $loader->load('twig.php');

        $loader->load('validators.php');

        $bundles = $container->getParameter('kernel.bundles');
        \assert(\is_array($bundles));

        if (isset($bundles['FOSCKEditorBundle'])) {
            $loader->load('form.php');
        }

        if (isset($bundles['SonataBlockBundle'])) {
            $loader->load('block.php');
        }

        if (isset($bundles['SonataMediaBundle'])) {
            $loader->load('media.php');
        }

        if (!\array_key_exists($config['default_formatter'], $config['formatters'])) {
            throw new \InvalidArgumentException(sprintf(
                'SonataFormatterBundle - Invalid default formatter: %s, available: %s',
                $config['default_formatter'],
                sprintf('["%s"]', implode('", "', array_keys($config['formatters'])))
            ));
        }

        $pool = $container->getDefinition('sonata.formatter.pool');
        $pool->addArgument($config['default_formatter']);

        foreach ($config['formatters'] as $code => $formatterConfig) {
            $env = null;

            if (0 !== \count($formatterConfig['extensions'])) {
                $envId = sprintf('sonata.formatter.twig.env.%s', $code);

                $container->register($envId, Environment::class)
                    ->setArguments([
                        new Reference('twig.loader'),
                        [
                            'debug' => false,
                            'strict_variables' => false,
                            'charset' => 'UTF-8',
                        ],
                    ]);

                $env = new Reference($envId);
            }

            $pool->addMethodCall(
                'add',
                [$code, new Reference($formatterConfig['service']), $env]
            );
        }

        $container->setParameter(
            'sonata.formatter.configuration.formatters',
            $config['formatters']
        );

        $container->setParameter(
            'sonata.formatter.ckeditor.configuration.templates',
            $config['ckeditor']['templates']
        );
    }

    public function getNamespace(): string
    {
        return 'http://www.sonata-project.org/schema/dic/formatter';
    }
}
