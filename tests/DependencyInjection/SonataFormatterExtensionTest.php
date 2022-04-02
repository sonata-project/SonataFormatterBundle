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
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Twig\Loader\LoaderInterface;

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

    public function testItThrowsOnInvalidDefaultFormatter(): void
    {
        $this->setParameter('kernel.bundles', []);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'SonataFormatterBundle - Invalid default formatter: tixt, available: ["text", "tuxt"]'
        );

        $this->load([
            'default_formatter' => 'tixt',
            'formatters' => ['tuxt' => [
                'service' => 'sonata.formatter.text.text',
                'extensions' => [
                    'sonata.formatter.twig.control_flow',
                    'sonata.formatter.twig.gist',
                ],
            ]],
        ]);
    }

    public function testGetLoader(): void
    {
        $this->setParameter('kernel.bundles', []);
        $this->load();
        static::assertInstanceOf(
            LoaderInterface::class,
            $this->container->get('sonata.formatter.twig.loader.text')
        );
    }

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [
            new SonataFormatterExtension(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getMinimalConfiguration(): array
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
