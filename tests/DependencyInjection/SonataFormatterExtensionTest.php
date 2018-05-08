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

namespace Sonata\FormatterBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\FormatterBundle\DependencyInjection\SonataFormatterExtension;

class SonataFormatterExtensionTest extends AbstractExtensionTestCase
{
    public function testLoadWithMinimalDocumentedConfig(): void
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

    public function testWithOptionalBundles(): void
    {
        $this->setParameter('kernel.bundles', array_flip([
            'FOSCKEditorBundle',
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

    public function testGetLoader(): void
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
    public function testLoadWithoutDefaultFormatter(): void
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
