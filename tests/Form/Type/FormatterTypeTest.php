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

use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Formatter\RawFormatter;
use Sonata\FormatterBundle\Formatter\TextFormatter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class FormatterTypeTest extends TestCase
{
    /**
     * @var Pool
     */
    private $pool;

    public function setUp(): void
    {
        parent::setUp();

        $this->pool = new Pool('');
    }

    public function testBuildFormOneChoice(): void
    {
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $type = new FormatterType($this->pool, $translator, $configManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue(['foo' => 'bar']));

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->exactly(3))->method('add');
        $formBuilder->expects($this->once())->method('get')->will($this->returnValue($choiceFormBuilder));
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

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildFormSeveralChoices(): void
    {
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $type = new FormatterType($this->pool, $translator, $configManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue(['foo' => 'bar', 'foo2' => 'bar2']));

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->exactly(2))->method('add');
        $formBuilder->expects($this->once())->method('get')->will($this->returnValue($choiceFormBuilder));

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

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithCustomFormatter(): void
    {
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $this->pool->add('text', new TextFormatter());
        $this->pool->add('raw', new RawFormatter());

        $formatters = ['text' => 'Text', 'raw' => 'Raw'];

        $type = new FormatterType($this->pool, $translator, $configManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())
            ->method('getOption')
            ->with('choices')
            ->will($this->returnValue($formatters));

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

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', ChoiceType::class, [
            'property_path' => 'SomeFormatField',
            'data' => $selectedFormat,
            'choices' => $formatters,
        ]);
        $formBuilder->expects($this->at(1))->method('get')->will($this->returnValue($choiceFormBuilder));
        $formBuilder->expects($this->at(2))->method('add')->with('SomeSourceField', TextareaType::class, [
            'property_path' => 'SomeSourceField',
        ]);

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithDefaultFormatter(): void
    {
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $this->pool->add('text', new TextFormatter());
        $this->pool->add('raw', new RawFormatter());

        $formatters = ['text' => 'Text', 'raw' => 'Raw'];

        $type = new FormatterType($this->pool, $translator, $configManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())
            ->method('getOption')
            ->with('choices')
            ->will($this->returnValue($formatters));

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', ChoiceType::class, [
            'property_path' => 'SomeFormatField',
            'data' => $defaultFormatter = 'text',
            'choices' => $formatters,
        ]);
        $formBuilder->expects($this->at(1))->method('get')->will($this->returnValue($choiceFormBuilder));
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

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithDefaultFormatterAndPluginManager(): void
    {
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $pluginManager = $this->createMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');

        $this->pool->add('text', new TextFormatter());
        $this->pool->add('raw', new RawFormatter());

        $formatters = ['text' => 'Text', 'raw' => 'Raw'];

        $type = new FormatterType($this->pool, $translator, $configManager, $pluginManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue($formatters));

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', ChoiceType::class, [
            'property_path' => 'SomeFormatField',
            'data' => $defaultFormatter = 'text',
            'choices' => $formatters,
        ]);
        $formBuilder->expects($this->at(1))->method('get')->will($this->returnValue($choiceFormBuilder));
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

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildViewWithDefaultConfig(): void
    {
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($this->pool, $translator, $configManager);

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, [
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
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(false));

        $type = new FormatterType($this->pool, $translator, $configManager);

        $ckEditorToolBarIcons = ['Icon 1'];

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, [
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
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button 1']];
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($this->pool, $translator, $configManager);

        $ckEditorToolBarIcons = ['Icon 1'];

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, [
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
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $type = new FormatterType($this->pool, $translator, $configManager);

        $ckEditorToolBarIcons = ['Icon 1'];

        $formatters = [];
        $formatters['text'] = 'Text';
        $formatters['html'] = 'HTML';

        $format = 'html';

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, [
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
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $pluginManager = $this->createMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($this->pool, $translator, $configManager, $pluginManager);

        /** @var FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, [
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
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $pluginManager = $this->createMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');
        $templateManager = $this->createMock('Ivory\CKEditorBundle\Model\TemplateManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($this->pool, $translator, $configManager, $pluginManager, $templateManager);

        /** @var FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, [
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

    public function testBuildViewWithDefaultConfigAndPluginManagerAndTemplateManagerAndWithTemplates(): void
    {
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $pluginManager = $this->createMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');
        $templateManager = $this->createMock('Ivory\CKEditorBundle\Model\TemplateManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => ['Button1']];
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

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

        $templateManager->expects($this->once())->method('hasTemplates')->will($this->returnValue(true));
        $templateManager->expects($this->once())->method('getTemplates')->will($this->returnValue($templates));

        $type = new FormatterType($this->pool, $translator, $configManager, $pluginManager, $templateManager);

        /** @var FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, [
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

        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');

        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $optionsResolver = new OptionsResolver();

        $type = new FormatterType($this->pool, $translator, $configManager);
        $type->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve();

        $expectedOptions = [
            'choices' => [
                'text' => 'text',
                'raw' => 'raw',
            ],
            'choice_translation_domain' => 'SonataFormatterBundle',
        ];

        // choices_as_values options is not needed in SF 3.0+
        if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
            $expectedOptions['choices_as_values'] = true;
        }

        $this->assertEquals($expectedOptions, $options['format_field_options']);
    }
}
