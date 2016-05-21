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
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataFormatterExtension extends Extension
{
    /**
     * Loads the url shortener configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
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

        // TODO: To be removed when major version is changed
        if (!isset($config['default_formatter'])) {
            reset($config['formatters']);
            $config['default_formatter'] = key($config['formatters']);
        }

        if (!array_key_exists($config['default_formatter'], $config['formatters'])) {
            throw new \InvalidArgumentException(sprintf('SonataFormatterBundle - Invalid default formatter : %s, available : %s', $config['default_formatter'], json_encode(array_keys($config['formatters']))));
        }

        $pool = $container->getDefinition('sonata.formatter.pool');
        // TODO: This should become the first (zero-indexed) argument when the major version is changed
        $pool->addArgument($config['default_formatter']);

        foreach ($config['formatters'] as $code => $configuration) {
            if (count($configuration['extensions']) == 0) {
                $env = null;
            } else {
                $env = new Reference($this->createEnvironment($container, $code, $container->getDefinition($configuration['service']), $configuration['extensions']));
            }

            $pool->addMethodCall('add', array($code, new Reference($configuration['service']), $env));
        }

        $container->setParameter('sonata.formatter.ckeditor.configuration.templates', $config['ckeditor']['templates']);
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $code
     * @param Definition       $formatter
     * @param array            $extensions
     *
     * @return string
     */
    public function createEnvironment(ContainerBuilder $container, $code, Definition $formatter, array $extensions)
    {
        $loader = new Definition('Twig_Loader_String');
        $loader->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.loader.%s', $code), $loader);

        $loaderSelector = new Definition('Sonata\FormatterBundle\Twig\Loader\LoaderSelector', array(
            new Reference(sprintf('sonata.formatter.twig.loader.%s', $code)),
            new Reference('twig.loader'),
        ));
        $loaderSelector->setPublic(false);

        $env = new Definition('Twig_Environment', array($loaderSelector, array(
            'debug' => false,
            'strict_variables' => false,
            'charset' => 'UTF-8',
        )));
        $env->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.env.%s', $code), $env);

        $sandboxPolicy = new Definition('Sonata\FormatterBundle\Twig\SecurityPolicyContainerAware', array(new Reference('service_container'), $extensions));
        $sandboxPolicy->setPublic(false);
        $container->setDefinition(sprintf('sonata.formatter.twig.sandbox.%s.policy', $code), $sandboxPolicy);

        $sandbox = new Definition('Twig_Extension_Sandbox', array($sandboxPolicy, true));
        $sandbox->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.sandbox.%s', $code), $sandbox);

        $env->addMethodCall('addExtension', array(new Reference(sprintf('sonata.formatter.twig.sandbox.%s', $code))));

        foreach ($extensions as $extension) {
            $env->addMethodCall('addExtension', array(new Reference($extension)));
        }

        $lexer = new Definition('Twig_Lexer', array(new Reference(sprintf('sonata.formatter.twig.env.%s', $code)), array(
            'tag_comment' => array('<#', '#>'),
            'tag_block' => array('<%', '%>'),
            'tag_variable' => array('<%=', '%>'),
        )));
        $lexer->setPublic(false);

        $container->setDefinition(new Reference(sprintf('sonata.formatter.twig.lexer.%s', $code)), $lexer);

        $env->addMethodCall('setLexer', array(new Reference(sprintf('sonata.formatter.twig.lexer.%s', $code))));

        return sprintf('sonata.formatter.twig.env.%s', $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://www.sonata-project.org/schema/dic/formatter';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'sonata_formatter';
    }
}
