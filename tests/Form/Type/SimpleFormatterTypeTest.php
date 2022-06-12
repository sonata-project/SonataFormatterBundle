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

namespace Sonata\FormatterBundle\Tests\Form\Type;

use FOS\CKEditorBundle\Config\CKEditorConfigurationInterface;
use PHPUnit\Framework\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class SimpleFormatterTypeTest extends TestCase
{
    /**
     * @var CKEditorConfigurationInterface|MockObject
     */
    private $ckEditorConfiguration;

    /**
     * @var SimpleFormatterType
     */
    private $formType;

    protected function setUp(): void
    {
        if (!class_exists(CKEditorConfigurationInterface::class)) {
            static::markTestSkipped('Test only available using friendsofsymfony/ckeditor-bundle 2.x');
        }

        parent::setUp();

        $this->ckEditorConfiguration = $this->createMock(CKEditorConfigurationInterface::class);

        $this->formType = new SimpleFormatterType(
            $this->ckEditorConfiguration
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testBuildForm(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);

        $options = ['format' => 'format'];

        $this->formType->buildForm($formBuilder, $options);
    }

    public function testBuildViewWithDefaultConfig(): void
    {
        $defaultConfig = 'context';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $this->ckEditorConfiguration->expects(static::once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->ckEditorConfiguration->expects(static::once())
            ->method('getConfig')
            ->with($defaultConfig)
            ->willReturn($defaultConfigValues);

        /** @var FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock(FormInterface::class);
        $this->formType->buildView($view, $form, [
            'format' => 'format',
            'ckeditor_context' => null,
            'ckeditor_image_format' => 'format',
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => [],
            'ckeditor_templates' => [],
            'ckeditor_toolbar_icons' => [],
        ]);

        static::assertSame(
            $view->vars['ckeditor_configuration'],
            ['toolbar' => ['Button1'], 'filebrowserImageUploadRouteParameters' => ['format' => 'format']]
        );
    }

    public function testBuildViewWithStylesSet(): void
    {
        $defaultConfig = 'context';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $this->ckEditorConfiguration->expects(static::once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->ckEditorConfiguration->expects(static::once())
            ->method('getConfig')
            ->with($defaultConfig)
            ->willReturn($defaultConfigValues);

        $styleSets = [
            'my_styleset' => [
                ['name' => 'Blue Title', 'element' => 'h2', 'styles' => ['color' => 'Blue']],
                ['name' => 'CSS Style', 'element' => 'span', 'attributes' => ['class' => 'my_style']],
                [
                    'name' => 'Multiple Element Style',
                    'element' => ['h2', 'span'],
                    'attributes' => ['class' => 'my_class'],
                ],
                [
                    'name' => 'Widget Style',
                    'type' => 'widget',
                    'widget' => 'my_widget',
                    'attributes' => ['class' => 'my_widget_style'],
                ],
            ],
        ];

        $this->ckEditorConfiguration->expects(static::once())
            ->method('getStyles')
            ->willReturn($styleSets);

        /** @var FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock(FormInterface::class);
        $this->formType->buildView($view, $form, [
            'format' => 'format',
            'ckeditor_context' => null,
            'ckeditor_image_format' => 'format',
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => [],
            'ckeditor_templates' => [],
            'ckeditor_style_sets' => [],
            'ckeditor_toolbar_icons' => [],
        ]);

        static::assertSame($view->vars['ckeditor_style_sets'], $styleSets);
    }

    public function testBuildViewWithToolbarOptionsSetAsPredefinedString(): void
    {
        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => 'basic'];
        $basicToolbarSets = [
            0 => [
                0 => 'Bold',
                1 => 'Italic',
            ],
            1 => [
                0 => 'NumberedList',
                1 => 'BulletedList',
            ],
        ];

        $this->ckEditorConfiguration->expects(static::once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->ckEditorConfiguration->expects(static::once())
            ->method('getConfig')
            ->with($defaultConfig)
            ->willReturn($defaultConfigValues);
        $this->ckEditorConfiguration->expects(static::once())
            ->method('getToolbar')
            ->with('basic')
            ->willReturn($basicToolbarSets);

        /** @var FormView $view */
        $view = $this->createMock(FormView::class);
        $form = $this->createMock(FormInterface::class);
        $this->formType->buildView($view, $form, [
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_image_format' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => [],
            'ckeditor_templates' => [],
            'ckeditor_toolbar_icons' => [],
            'format' => [],
        ]);

        $defaultConfigValues['toolbar'] = $basicToolbarSets;
        static::assertSame($view->vars['ckeditor_configuration'], $defaultConfigValues);
    }
}
