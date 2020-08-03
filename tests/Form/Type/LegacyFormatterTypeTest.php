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

use FOS\CKEditorBundle\Model\ConfigManagerInterface;
use FOS\CKEditorBundle\Model\PluginManagerInterface;
use FOS\CKEditorBundle\Model\TemplateManagerInterface;
use FOS\CKEditorBundle\Model\ToolbarManagerInterface;
use PHPUnit\Framework\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Formatter\RawFormatter;
use Sonata\FormatterBundle\Formatter\TextFormatter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class LegacyFormatterTypeTest extends TestCase
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var TranslatorInterface|MockObject
     */
    private $translator;

    /**
     * @var ConfigManagerInterface|MockObject
     */
    private $configManager;

    /**
     * @var PluginManagerInterface|MockObject
     */
    private $pluginManager;

    /**
     * @var TemplateManagerInterface|MockObject
     */
    private $templateManager;

    /**
     * @var ToolbarManagerInterface|MockObject
     */
    private $toolbarManager;

    /**
     * @var FormatterType
     */
    private $formType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pool = new Pool('');

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->configManager = $this->createMock(ConfigManagerInterface::class);
        $this->pluginManager = $this->createMock(PluginManagerInterface::class);
        $this->templateManager = $this->createMock(TemplateManagerInterface::class);
        $this->toolbarManager = $this->createMock(ToolbarManagerInterface::class);

        $this->formType = new FormatterType(
            $this->pool,
            $this->translator,
            $this->configManager,
            $this->pluginManager,
            $this->templateManager,
            $this->toolbarManager
        );
    }

    public function testBuildFormOneChoice(): void
    {
        $choiceFormBuilder = $this->createMock(FormBuilderInterface::class);
        $choiceFormBuilder->expects($this->once())
            ->method('getOption')
            ->with('choices')
            ->willReturn(['foo' => 'bar']);

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects($this->exactly(3))->method('add');
        $formBuilder->expects($this->once())->method('get')->willReturn($choiceFormBuilder);
        $formBuilder->expects($this->once())->method('remove');

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
        $choiceFormBuilder->expects($this->once())
            ->method('getOption')
            ->with('choices')
            ->willReturn(['foo' => 'bar', 'foo2' => 'bar2']);

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects($this->exactly(2))->method('add');
        $formBuilder->expects($this->once())->method('get')->willReturn($choiceFormBuilder);

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
        $this->pool->add('raw', new RawFormatter());

        $formatters = ['text' => 'Text', 'raw' => 'Raw'];

        $choiceFormBuilder = $this->createMock(FormBuilderInterface::class);

        $choiceFormBuilder->expects($this->once())
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
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', ChoiceType::class, [
            'property_path' => 'SomeFormatField',
            'data' => $selectedFormat,
            'choices' => $formatters,
        ]);
        $formBuilder->expects($this->at(1))->method('get')->willReturn($choiceFormBuilder);
        $formBuilder->expects($this->at(2))->method('add')->with('SomeSourceField', TextareaType::class, [
            'property_path' => 'SomeSourceField',
        ]);

        $this->formType->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithDefaultFormatter(): void
    {
        $this->pool->add('text', new TextFormatter());
        $this->pool->add('raw', new RawFormatter());

        $formatters = ['text' => 'Text', 'raw' => 'Raw'];

        $choiceFormBuilder = $this->createMock(FormBuilderInterface::class);

        $choiceFormBuilder->expects($this->once())
            ->method('getOption')
            ->with('choices')
            ->willReturn($formatters);

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', ChoiceType::class, [
            'property_path' => 'SomeFormatField',
            'data' => $defaultFormatter = 'text',
            'choices' => $formatters,
        ]);
        $formBuilder->expects($this->at(1))->method('get')->willReturn($choiceFormBuilder);
        $formBuilder->expects($this->at(2))->method('add')->with('SomeSourceField', TextareaType::class, [
            'property_path' => 'SomeSourceField',
        ]);

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

    public function testBuildFormWithDefaultFormatterAndPluginManager(): void
    {
        $this->pool->add('text', new TextFormatter());
        $this->pool->add('raw', new RawFormatter());

        $formatters = ['text' => 'Text', 'raw' => 'Raw'];

        $choiceFormBuilder = $this->createMock(FormBuilderInterface::class);
        $choiceFormBuilder->expects($this->once())
            ->method('getOption')
            ->with('choices')
            ->willReturn($formatters);

        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', ChoiceType::class, [
            'property_path' => 'SomeFormatField',
            'data' => $defaultFormatter = 'text',
            'choices' => $formatters,
        ]);
        $formBuilder->expects($this->at(1))->method('get')->willReturn($choiceFormBuilder);
        $formBuilder->expects($this->at(2))->method('add')->with('SomeSourceField', TextareaType::class, [
            'property_path' => 'SomeSourceField',
        ]);

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

    public function testBuildViewWithDefaultConfig(): void
    {
        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $this->configManager->expects($this->once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->configManager->expects($this->once())
            ->method('hasConfig')
            ->with($defaultConfig)
            ->willReturn(true);
        $this->configManager->expects($this->once())
            ->method('getConfig')
            ->with($defaultConfig)
            ->willReturn($defaultConfigValues);

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
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
        ]);

        $this->assertSame($view->vars['ckeditor_configuration'], $defaultConfigValues);
    }

    public function testBuildViewWithoutDefaultConfig(): void
    {
        $defaultConfig = 'default';
        $this->configManager->expects($this->once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->configManager->expects($this->once())
            ->method('hasConfig')
            ->with($defaultConfig)
            ->willReturn(false);

        $ckEditorToolBarIcons = ['Icon 1'];

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
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
            'ckeditor_toolbar_icons' => $ckEditorToolBarIcons,
        ]);

        $ckeditorConfiguration = ['toolbar' => $ckEditorToolBarIcons];
        $this->assertSame($view->vars['ckeditor_configuration'], $ckeditorConfiguration);
    }

    public function testBuildViewWithDefaultConfigAndWithToolbarIcons(): void
    {
        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button 1']];
        $this->configManager->expects($this->once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->configManager->expects($this->once())
            ->method('hasConfig')
            ->with($defaultConfig)
            ->willReturn(true);
        $this->configManager->expects($this->once())
            ->method('getConfig')
            ->with($defaultConfig)
            ->willReturn($defaultConfigValues);

        $ckEditorToolBarIcons = ['Icon 1'];

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
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
            'ckeditor_toolbar_icons' => $ckEditorToolBarIcons,
        ]);

        $this->assertSame($view->vars['ckeditor_configuration'], ['toolbar' => $defaultConfigValues['toolbar']]);
    }

    public function testBuildViewWithFormatter(): void
    {
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

        $this->assertSame($view->vars['format_field_options']['data'], $format);
    }

    public function testBuildViewWithDefaultConfigAndPluginManager(): void
    {
        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $this->configManager->expects($this->once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->configManager->expects($this->once())
            ->method('hasConfig')
            ->with($defaultConfig)
            ->willReturn(true);
        $this->configManager->expects($this->once())
            ->method('getConfig')
            ->with($defaultConfig)
            ->willReturn($defaultConfigValues);

        /** @var FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
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
        ]);

        $this->assertSame($view->vars['ckeditor_configuration'], $defaultConfigValues);
    }

    public function testBuildViewWithDefaultConfigAndPluginManagerAndTemplateManager(): void
    {
        $defaultConfig = 'default';
        $toolbar_config = 'custom_toolbar';
        $defaultConfigValues = ['toolbar' => $toolbar_config];
        $custom_toolbar = ['Button 1'];
        $this->configManager->expects($this->once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->configManager->expects($this->once())
            ->method('hasConfig')
            ->with($defaultConfig)
            ->willReturn(true);
        $this->configManager->expects($this->once())
            ->method('getConfig')
            ->with($defaultConfig)
            ->willReturn($defaultConfigValues);
        $this->toolbarManager->expects($this->once())
            ->method('resolveToolbar')
            ->with($toolbar_config)
            ->willReturn($custom_toolbar);

        /** @var FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
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
        ]);

        $this->assertSame($view->vars['ckeditor_configuration'], ['toolbar' => $custom_toolbar]);
    }

    public function testBuildViewWithDefaultConfigAndPluginManagerAndTemplateManagerAndWithTemplates(): void
    {
        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $this->configManager->expects($this->once())->method('getDefaultConfig')->willReturn($defaultConfig);
        $this->configManager->expects($this->once())
            ->method('hasConfig')
            ->with($defaultConfig)
            ->willReturn(true);
        $this->configManager->expects($this->once())
            ->method('getConfig')
            ->with($defaultConfig)
            ->willReturn($defaultConfigValues);

        $templates = [
            'imagesPath' => '/bundles/mybundle/templates/images',
            'templates' => [
                [
                    'title' => 'My Template',
                    'image' => 'images.jpg',
                    'description' => 'My awesome template',
                    'html' => '<p>Crazy template :)</p>',
                ],
            ],
        ];

        $this->templateManager->expects($this->once())->method('hasTemplates')->willReturn(true);
        $this->templateManager->expects($this->once())->method('getTemplates')->willReturn($templates);

        /** @var FormView $view */
        $view = $this->createMock(FormView::class);
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
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
        ]);

        $this->assertSame($view->vars['ckeditor_templates'], $templates);
    }

    public function testOptions(): void
    {
        $this->pool->add('text', new TextFormatter());
        $this->pool->add('raw', new RawFormatter());

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $expectedOptions = [
            'choices' => [
                'text' => 'text',
                'raw' => 'raw',
            ],
            'choice_translation_domain' => 'SonataFormatterBundle',
        ];

        $this->assertSame($expectedOptions, $options['format_field_options']);
    }
}
