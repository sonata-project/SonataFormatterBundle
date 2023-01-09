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

namespace Sonata\FormatterBundle\Tests\Form\Widget;

use FOS\CKEditorBundle\Config\CKEditorConfigurationInterface;
use FOS\CKEditorBundle\Renderer\CKEditorRendererInterface;
use FOS\CKEditorBundle\Twig\CKEditorExtension;
use PHPUnit\Framework\MockObject\MockObject;
use Sonata\Form\Test\AbstractWidgetTestCase;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Formatter\Pool;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\PreloadedExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class SonataFormatterTypeWidgetTest extends AbstractWidgetTestCase
{
    private Pool $pool;

    /**
     * @var CKEditorConfigurationInterface&MockObject
     */
    private CKEditorConfigurationInterface $ckEditorConfiguration;

    protected function setUp(): void
    {
        $this->pool = new Pool('text');
        $this->ckEditorConfiguration = $this->createMock(CKEditorConfigurationInterface::class);

        parent::setUp();
    }

    public function testSingleFormatterOptionRender(): void
    {
        $this->ckEditorConfiguration->expects(static::once())->method('getDefaultConfig')->willReturn('default');

        $form = $this->factory->createBuilder()
            ->add('text', FormatterType::class, [
                'format_field' => 'formatText',
                'format_field_options' => [
                    'property_path' => 'textFormat',
                    'choices' => [
                        'text' => 'text',
                    ],
                ],
                'source_field' => 'textRaw',
                'target_field' => 'text',
            ])
            ->getForm();

        $html = $this->cleanHtmlWhitespace($this->renderWidget($form->createView()));
        static::assertStringNotContainsString(
            '<select id="form_text_formatText" name="form[text][formatText]">',
            $html
        );
        static::assertStringNotContainsString(
            '[trans]please_select_format_method[/trans]',
            $html
        );
    }

    public function testMultipleFormatterOptionRender(): void
    {
        $this->ckEditorConfiguration->expects(static::once())->method('getDefaultConfig')->willReturn('default');

        $form = $this->factory->createBuilder()
            ->add('text', FormatterType::class, [
                'format_field' => 'formatText',
                'format_field_options' => [
                    'property_path' => 'textFormat',
                    'choices' => [
                        'text' => 'text',
                        'rawhtml' => 'rawhtml',
                    ],
                ],
                'source_field' => 'textRaw',
                'target_field' => 'text',
            ])
            ->getForm();

        $html = $this->cleanHtmlWhitespace($this->renderWidget($form->createView()));
        static::assertStringContainsString(
            '<select id="form_text_formatText" name="form[text][formatText]">',
            $html
        );
        static::assertStringContainsString(
            '[trans]please_select_format_method[/trans]',
            $html
        );
    }

    /**
     * @return FormExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([
                new FormatterType($this->pool, $this->ckEditorConfiguration),
            ], []),
        ];
    }

    protected function getEnvironment(): Environment
    {
        $loader = new FilesystemLoader($this->getTemplatePaths());
        $loader->addPath($this->getSonataFormatterViewsPath(), 'SonataFormatter');

        $environment = parent::getEnvironment();
        $environment->setLoader($loader);

        $ckRenderer = $this->createMock(CKEditorRendererInterface::class);
        $environment->addExtension(new CKEditorExtension($ckRenderer));

        return $environment;
    }

    protected function getRenderingEngine(Environment $environment): TwigRendererEngine
    {
        return new TwigRendererEngine(
            ['form_div_layout.html.twig', 'formatter.html.twig'],
            $environment
        );
    }

    protected function getTemplatePaths(): array
    {
        return array_merge(parent::getTemplatePaths(), [$this->getSonataFormatterViewsPath().'/Form']);
    }

    private function getSonataFormatterViewsPath(): string
    {
        return sprintf('%s/../../../src/Resources/views', __DIR__);
    }
}
