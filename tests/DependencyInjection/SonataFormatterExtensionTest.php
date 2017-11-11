<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\FormatterBundle\DependencyInjection\SonataFormatterExtension;

class SonataFormatterExtensionTest extends AbstractExtensionTestCase
{
    /**
     * NEXT_MAJOR: remove this method when dropping support for sf < 2.8.
     */
    protected function setUp()
    {
        if (!method_exists($this, 'setParameter')) {
            $this->markTestSkipped('Skipping this test for sf 2.3, too cumbersome to write');
        }

        parent::setUp();
    }

    public function testLoadWithMinimalDocumentedConfig()
    {
        $this->setParameter('kernel.bundles', []);
        $this->load([
            'default_formatter' => 'text',
            'formatters' => ['text' => [
                'service' => 'sonata.formatter.text.text',
                'extensions' => [
                    'sonata.formatter.twig.control_flow',
                    'sonata.formatter.twig.gist',
                ],
            ]],
        ]);
        $this->assertContainerBuilderHasService('sonata.formatter.pool');
    }

    public function testWithOptionalBundles()
    {
        $this->setParameter('kernel.bundles', array_flip([
            'IvoryCKEditorBundle',
            'SonataBlockBundle',
            'SonataMediaBundle',
        ]));

        $this->load([
            'default_formatter' => 'text',
            'formatters' => ['text' => [
                'service' => 'sonata.formatter.text.text',
                'extensions' => [
                    'sonata.formatter.twig.control_flow',
                    'sonata.formatter.twig.gist',
                ],
            ]],
        ]);

        $this->assertContainerBuilderHasService('sonata.formatter.form.type.selector');
        $this->assertContainerBuilderHasService('sonata.formatter.block.formatter');
        $this->assertContainerBuilderHasService('sonata.formatter.ckeditor.extension');
    }

    public function testGetLoader()
    {
        $this->setParameter('kernel.bundles', []);
        $this->load();
        $this->assertInstanceOf(
            '\Twig_LoaderInterface',
            $this->container->get('sonata.formatter.twig.loader.text')
        );
    }

    /**
     * @group legacy
     */
    public function testLoadWithoutDefaultFormatter()
    {
        $this->setParameter('kernel.bundles', []);
        $this->load([
            'formatters' => ['text' => [
                'service' => 'sonata.formatter.text.text',
                'extensions' => [
                    'sonata.formatter.twig.control_flow',
                    'sonata.formatter.twig.gist',
                ],
            ]],
        ]);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sonata.formatter.pool',
            0,
            'text'
        );
    }

    protected function getContainerExtensions()
    {
        return [
            new SonataFormatterExtension(),
        ];
    }

    protected function getMinimalConfiguration()
    {
        return [
            'default_formatter' => 'text',
            'formatters' => ['text' => [
                'service' => 'sonata.formatter.text.text',
                'extensions' => [
                    'sonata.formatter.twig.control_flow',
                    'sonata.formatter.twig.gist',
                ],
            ]],
        ];
    }
}
