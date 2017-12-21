<?php

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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataFormatterExtension extends Extension
{
    /**
     * Loads the url shortener configuration.
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('formatter.xml');
        $loader->load('twig.xml');

        $loader->load('validators.xml');

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['IvoryCKEditorBundle'])) {
            $loader->load('form.xml');
        }

        if (isset($bundles['SonataBlockBundle'])) {
            $loader->load('block.xml');
        }

        if (isset($bundles['SonataMediaBundle'])) {
            $loader->load('ckeditor.xml');
        }

        // NEXT_MAJOR: remove this if block
        if (!isset($config['default_formatter'])) {
            @trigger_error(
                'Not setting the default_formatter configuration node is deprecated since 3.2,'.
                ' and will no longer be supported in 4.0.',
                E_USER_DEPRECATED
            );
            reset($config['formatters']);
            $config['default_formatter'] = key($config['formatters']);
        }

        if (!array_key_exists($config['default_formatter'], $config['formatters'])) {
            throw new \InvalidArgumentException(sprintf(
                'SonataFormatterBundle - Invalid default formatter : %s, available : %s',
                $config['default_formatter'],
                json_encode(array_keys($config['formatters']))
            ));
        }

        $pool = $container->getDefinition('sonata.formatter.pool');
        $pool->addArgument($config['default_formatter']);

        foreach ($config['formatters'] as $code => $configuration) {
            if (0 == count($configuration['extensions'])) {
                $env = null;
            } else {
                $env = new Reference($this->createEnvironment(
                    $container,
                    $code,
                    $container->getDefinition($configuration['service']),
                    $configuration['extensions']
                ));
            }

            $pool->addMethodCall(
                'add',
                [$code, new Reference($configuration['service']), $env]
            );
        }

        $container->setParameter(
            'sonata.formatter.ckeditor.configuration.templates',
            $config['ckeditor']['templates']
        );
    }

    /**
     * @param string $code
     *
     * @return string
     */
    public function createEnvironment(ContainerBuilder $container, $code, Definition $formatter, array $extensions)
    {
        $loader = new Definition('Twig_Loader_Array');

        // NEXT_MAJOR: remove this if block
        if (!class_exists('\Twig_Loader_Array')) {
            $loader = new Definition('Twig_Loader_String');
        }

        $loader->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.loader.%s', $code), $loader);

        $loaderSelector = new Definition('Sonata\FormatterBundle\Twig\Loader\LoaderSelector', [
            new Reference(sprintf('sonata.formatter.twig.loader.%s', $code)),
            new Reference('twig.loader'),
        ]);
        $loaderSelector->setPublic(false);

        $env = new Definition('Twig_Environment', [$loaderSelector, [
            'debug' => false,
            'strict_variables' => false,
            'charset' => 'UTF-8',
        ]]);
        $env->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.env.%s', $code), $env);

        $sandboxPolicy = new Definition('Sonata\FormatterBundle\Twig\SecurityPolicyContainerAware', [new Reference('service_container'), $extensions]);
        $sandboxPolicy->setPublic(false);
        $container->setDefinition(sprintf('sonata.formatter.twig.sandbox.%s.policy', $code), $sandboxPolicy);

        $sandbox = new Definition('Twig_Extension_Sandbox', [$sandboxPolicy, true]);
        $sandbox->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.sandbox.%s', $code), $sandbox);

        $env->addMethodCall('addExtension', [new Reference(sprintf('sonata.formatter.twig.sandbox.%s', $code))]);

        foreach ($extensions as $extension) {
            $env->addMethodCall('addExtension', [new Reference($extension)]);
        }

        $lexer = new Definition('Twig_Lexer', [new Reference(sprintf('sonata.formatter.twig.env.%s', $code)), [
            'tag_comment' => ['<#', '#>'],
            'tag_block' => ['<%', '%>'],
            'tag_variable' => ['<%=', '%>'],
        ]]);
        $lexer->setPublic(false);

        $container->setDefinition(new Reference(sprintf('sonata.formatter.twig.lexer.%s', $code)), $lexer);

        $env->addMethodCall('setLexer', [new Reference(sprintf('sonata.formatter.twig.lexer.%s', $code))]);

        return sprintf('sonata.formatter.twig.env.%s', $code);
    }

    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.sonata-project.org/schema/dic/formatter';
    }

    public function getAlias()
    {
        return 'sonata_formatter';
    }
}
