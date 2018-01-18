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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sonata_formatter');

        $rootNode
            ->children()
                ->scalarNode('default_formatter')->end() // NEXT_MAJOR: make this required
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
                                ->scalarNode('browser')->defaultValue('@SonataFormatter/Ckeditor/browser.html.twig')->cannotBeEmpty()->end()
                                ->scalarNode('upload')->defaultValue('@SonataFormatter/Ckeditor/upload.html.twig')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
