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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Formatter\TextFormatter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class FormatterTypeTest extends TestCase
{
    private Pool $pool;

    private FormatterType $formType;

    /**
     * @var CKEditorConfigurationInterface&MockObject
     */
    private CKEditorConfigurationInterface $ckEditorConfiguration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pool = new Pool('');
        $this->ckEditorConfiguration = $this->createMock(CKEditorConfigurationInterface::class);
        $this->formType = new FormatterType($this->pool, $this->ckEditorConfiguration);
    }

    public function testBuildFormOneChoice(): void
    {
        $choiceFormBuilder = $this->createMock(FormBuilderInterface::class);
        $choiceFormBuilder->expects(static::once())
            ->method('getOption')
            ->with('choices')
            ->willReturn(['foo' => 'bar']);

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects(static::exactly(3))->method('add');
        $formBuilder->expects(static::once())->method('get')->willReturn($choiceFormBuilder);
        $formBuilder->expects(static::once())->method('remove');

        $options = [
            'format_field' => 'format',
            'source_field' => 'source',
            'format_field_options' => [
                'property_path' => '',
            ],
            'source_field_options' => [
                'property_path' => '',
            ],
            'listener' => false,
        ];

        $this->formType->buildForm($formBuilder, $options);
    }

    public function testBuildFormSeveralChoices(): void
    {
        $choiceFormBuilder = $this->createMock(FormBuilderInterface::class);
        $choiceFormBuilder->expects(static::once())
            ->method('getOption')
            ->with('choices')
            ->willReturn(['foo' => 'bar', 'foo2' => 'bar2']);

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects(static::exactly(2))->method('add');
        $formBuilder->expects(static::once())->method('get')->willReturn($choiceFormBuilder);

        $options = [
            'format_field' => 'format',
            'source_field' => 'source',
            'format_field_options' => [
                'property_path' => '',
            ],
            'source_field_options' => [
                'property_path' => '',
            ],
            'listener' => false,
        ];

        $this->formType->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithCustomFormatter(): void
    {
        $this->pool->add('text', new TextFormatter());

        $formatters = ['text' => 'Text'];

        $choiceFormBuilder = $this->createMock(FormBuilderInterface::class);

        $choiceFormBuilder->expects(static::once())
            ->method('getOption')
            ->with('choices')
            ->willReturn($formatters);

        $options = [
            'format_field' => 'SomeFormatField',
            'source_field' => 'SomeSourceField',
            'format_field_options' => [
                'property_path' => '',
                'data' => $selectedFormat = 'text',
                'choices' => $formatters,
            ],
            'source_field_options' => [
                'property_path' => '',
            ],
            'listener' => false,
        ];

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->method('add')
            ->withConsecutive(
                ['SomeFormatField', ChoiceType::class, [
                    'property_path' => 'SomeFormatField',
                    'data' => $selectedFormat,
                    'choices' => $formatters,
                ]],
                ['SomeFormatField', HiddenType::class, [
                    'property_path' => 'SomeFormatField',
                    'data' => $selectedFormat,
                ]]
            );
        $formBuilder->expects(static::once())->method('get')->willReturn($choiceFormBuilder);

        $this->formType->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithDefaultFormatter(): void
    {
        $this->pool->add('text', new TextFormatter());

        $formatters = ['text' => 'Text'];

        $choiceFormBuilder = $this->createMock(FormBuilderInterface::class);
        $choiceFormBuilder->expects(static::once())
            ->method('getOption')
            ->with('choices')
            ->willReturn($formatters);

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->method('add')
            ->withConsecutive(
                ['SomeFormatField', ChoiceType::class, [
                    'property_path' => 'SomeFormatField',
                    'data' => $defaultFormatter = 'text',
                    'choices' => $formatters,
                ]],
                ['SomeFormatField', HiddenType::class, [
                    'property_path' => 'SomeFormatField',
                    'data' => $defaultFormatter,
                ]]
            );
        $formBuilder->expects(static::once())->method('get')->willReturn($choiceFormBuilder);

        $options = [
            'format_field' => 'SomeFormatField',
            'source_field' => 'SomeSourceField',
            'format_field_options' => [
                'property_path' => '',
                'data' => 'text',
                'choices' => $formatters,
            ],
            'source_field_options' => [
                'property_path' => '',
            ],
            'listener' => false,
        ];

        $this->formType->buildForm($formBuilder, $options);
    }

    public function testBuildViewWithFormatter(): void
    {
        $defaultConfig = 'default';
        $this->ckEditorConfiguration->expects(static::once())->method('getDefaultConfig')->willReturn($defaultConfig);

        $ckEditorToolBarIcons = ['Icon 1'];

        $formatters = [];
        $formatters['text'] = 'Text';
        $formatters['html'] = 'HTML';

        $format = 'html';

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock(FormInterface::class);
        $this->formType->buildView($view, $form, [
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => [
                'choices' => $formatters,
                'data' => $format,
            ],
            'ckeditor_context' => null,
            'ckeditor_image_format' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => [],
            'ckeditor_templates' => [],
            'ckeditor_toolbar_icons' => $ckEditorToolBarIcons,
        ]);

        static::assertSame($view->vars['format_field_options']['data'], $format);
    }

    public function testOptions(): void
    {
        $this->pool->add('text', new TextFormatter());

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $expectedOptions = [
            'choices' => [
                'text' => 'text',
            ],
        ];

        static::assertSame($expectedOptions, $options['format_field_options']);
    }
}
