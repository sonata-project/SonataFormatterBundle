<?php

namespace Sonata\FormatterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sonata_formatter');

        $rootNode
            ->children()
                ->arrayNode('formatters')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service')->isRequired()->end()
                            ->arrayNode('extensions')
                                ->prototype('scalar')
                                ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addCkeditorSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds CKEditor configuration section to root node configuration
     *
     * @param ArrayNodeDefinition $node
     */
    private function addCkeditorSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('ckeditor')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('templates')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('browser')->defaultValue('SonataFormatterBundle:Ckeditor:browser.html.twig')->cannotBeEmpty()->end()
                                ->scalarNode('upload')->defaultValue('SonataFormatterBundle:Ckeditor:upload.html.twig')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
