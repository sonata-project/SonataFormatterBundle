<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Form\Type;

use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Tests\TestCase;
use Symfony\Component\Form\FormView;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class FormatterTypeTest extends TestCase
{
    public function testBuildFormOneChoice()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();

        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $type = new FormatterType($pool, $translator, $configManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue(array('foo' => 'bar')));

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->exactly(3))->method('add');
        $formBuilder->expects($this->once())->method('get')->will($this->returnValue($choiceFormBuilder));
        $formBuilder->expects($this->once())->method('remove');

        $options = array(
            'format_field' => 'format',
            'source_field' => 'source',
            'format_field_options' => array(
                'property_path' => '',
            ),
            'source_field_options' => array(
                'property_path' => '',
            ),
            'listener' => false,
        );

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildFormSeveralChoices()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $type = new FormatterType($pool, $translator, $configManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue(array('foo' => 'bar', 'foo2' => 'bar2')));

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->exactly(2))->method('add');
        $formBuilder->expects($this->once())->method('get')->will($this->returnValue($choiceFormBuilder));

        $options = array(
            'format_field' => 'format',
            'source_field' => 'source',
            'format_field_options' => array(
                'property_path' => '',
            ),
            'source_field_options' => array(
                'property_path' => '',
            ),
            'listener' => false,
        );

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithCustomFormatter()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')
            ->disableOriginalConstructor()
            ->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $formatters = array('text' => 'Text', 'html' => 'HTML', 'markdown' => 'Markdown');

        $selectedFormat = 'html';

        $pool->method('getFormatters')->will($this->returnValue($formatters));
        $type = new FormatterType($pool, $translator, $configManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue($formatters));

        $options = array(
            'format_field' => 'SomeFormatField',
            'source_field' => 'SomeSourceField',
            'format_field_options' => array(
                'property_path' => '',
                'data' => $selectedFormat,
                'choices' => $formatters,
            ),
            'source_field_options' => array(
                'property_path' => '',
            ),
            'listener' => false,
        );

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', 'choice', array(
            'property_path' => 'SomeFormatField',
            'data' => $selectedFormat,
            'choices' => $formatters,
        ));
        $formBuilder->expects($this->at(1))->method('get')->will($this->returnValue($choiceFormBuilder));
        $formBuilder->expects($this->at(2))->method('add')->with('SomeSourceField', 'textarea', array(
            'property_path' => 'SomeSourceField',
        ));

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithDefaultFormatter()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')
            ->disableOriginalConstructor()
            ->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $formatters = array('text' => 'Text', 'html' => 'HTML', 'markdown' => 'Markdown');
        $defaultFormatter = 'text';

        $pool->method('getFormatters')->will($this->returnValue($formatters));
        $pool->method('getDefaultFormatter')->will($this->returnValue('text'));
        $type = new FormatterType($pool, $translator, $configManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue($formatters));

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', 'choice', array(
            'property_path' => 'SomeFormatField',
            'data' => $defaultFormatter,
            'choices' => $formatters,
        ));
        $formBuilder->expects($this->at(1))->method('get')->will($this->returnValue($choiceFormBuilder));
        $formBuilder->expects($this->at(2))->method('add')->with('SomeSourceField', 'textarea', array(
            'property_path' => 'SomeSourceField',
        ));

        $options = array(
            'format_field' => 'SomeFormatField',
            'source_field' => 'SomeSourceField',
            'format_field_options' => array(
                'property_path' => '',
                'choices' => $formatters,
            ),
            'source_field_options' => array(
                'property_path' => '',
            ),
            'listener' => false,
        );

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildFormWithDefaultFormatterAndPluginManager()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')
            ->disableOriginalConstructor()
            ->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $pluginManager = $this->createMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');

        $formatters = array('text' => 'Text', 'html' => 'HTML', 'markdown' => 'Markdown');
        $defaultFormatter = 'text';

        $pool->method('getFormatters')->will($this->returnValue($formatters));
        $pool->method('getDefaultFormatter')->will($this->returnValue('text'));
        $type = new FormatterType($pool, $translator, $configManager, $pluginManager);

        $choiceFormBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue($formatters));

        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');
        $formBuilder->expects($this->at(0))->method('add')->with('SomeFormatField', 'choice', array(
            'property_path' => 'SomeFormatField',
            'data' => $defaultFormatter,
            'choices' => $formatters,
        ));
        $formBuilder->expects($this->at(1))->method('get')->will($this->returnValue($choiceFormBuilder));
        $formBuilder->expects($this->at(2))->method('add')->with('SomeSourceField', 'textarea', array(
            'property_path' => 'SomeSourceField',
        ));

        $options = array(
            'format_field' => 'SomeFormatField',
            'source_field' => 'SomeSourceField',
            'format_field_options' => array(
                'property_path' => '',
                'choices' => $formatters,
            ),
            'source_field_options' => array(
                'property_path' => '',
            ),
            'listener' => false,
        );

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildViewWithDefaultConfig()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = array('toolbar' => array('Button1'));
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($pool, $translator, $configManager);

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => array(),
            'ckeditor_templates' => array(),
            'ckeditor_toolbar_icons' => array(),
        ));

        $this->assertSame($view->vars['ckeditor_configuration'], $defaultConfigValues);
    }

    public function testBuildViewWithoutDefaultConfig()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(false));

        $type = new FormatterType($pool, $translator, $configManager);

        $ckEditorToolBarIcons = array('Icon 1');

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => array(),
            'ckeditor_templates' => array(),
            'ckeditor_toolbar_icons' => $ckEditorToolBarIcons,
        ));

        $ckeditorConfiguration = array('toolbar' => $ckEditorToolBarIcons);
        $this->assertSame($view->vars['ckeditor_configuration'], $ckeditorConfiguration);
    }

    public function testBuildViewWithDefaultConfigAndWithToolbarIcons()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = array('toolbar' => array('Button 1'));
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($pool, $translator, $configManager);

        $ckEditorToolBarIcons = array('Icon 1');

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => array(),
            'ckeditor_templates' => array(),
            'ckeditor_toolbar_icons' => $ckEditorToolBarIcons,
        ));

        $this->assertSame($view->vars['ckeditor_configuration'], array('toolbar' => $defaultConfigValues['toolbar']));
    }

    public function testBuildViewWithFormatter()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $type = new FormatterType($pool, $translator, $configManager);

        $ckEditorToolBarIcons = array('Icon 1');

        $formatters = array();
        $formatters['text'] = 'Text';
        $formatters['html'] = 'HTML';

        $format = 'html';

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => array(
                'choices' => $formatters,
                'data' => $format,
            ),
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => array(),
            'ckeditor_templates' => array(),
            'ckeditor_toolbar_icons' => $ckEditorToolBarIcons,
        ));

        $this->assertSame($view->vars['format_field_options']['data'], $format);
    }

    public function testBuildViewWithDefaultConfigAndPluginManager()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $pluginManager = $this->createMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = array('toolbar' => array('Button1'));
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($pool, $translator, $configManager, $pluginManager);

        /** @var FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => array(),
            'ckeditor_templates' => array(),
            'ckeditor_toolbar_icons' => array(),
        ));

        $this->assertSame($view->vars['ckeditor_configuration'], $defaultConfigValues);
    }

    public function testBuildViewWithDefaultConfigAndPluginManagerAndTemplateManager()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $pluginManager = $this->createMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');
        $templateManager = $this->createMock('Ivory\CKEditorBundle\Model\TemplateManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = array('toolbar' => array('Button1'));
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($pool, $translator, $configManager, $pluginManager, $templateManager);

        /** @var FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => array(),
            'ckeditor_templates' => array(),
            'ckeditor_toolbar_icons' => array(),
        ));

        $this->assertSame($view->vars['ckeditor_configuration'], $defaultConfigValues);
    }

    public function testBuildViewWithDefaultConfigAndPluginManagerAndTemplateManagerAndWithTemplates()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $pluginManager = $this->createMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');
        $templateManager = $this->createMock('Ivory\CKEditorBundle\Model\TemplateManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = array('toolbar' => array('Button1'));
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $templates = array(
            'imagesPath' => '/bundles/mybundle/templates/images',
            'templates' => array(
                array(
                    'title' => 'My Template',
                    'image' => 'images.jpg',
                    'description' => 'My awesome template',
                    'html' => '<p>Crazy template :)</p>',
                ),
            ),
        );

        $templateManager->expects($this->once())->method('hasTemplates')->will($this->returnValue(true));
        $templateManager->expects($this->once())->method('getTemplates')->will($this->returnValue($templates));

        $type = new FormatterType($pool, $translator, $configManager, $pluginManager, $templateManager);

        /** @var FormView $view */
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->createMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => array(),
            'ckeditor_templates' => array(),
            'ckeditor_toolbar_icons' => array(),
        ));

        $this->assertSame($view->vars['ckeditor_templates'], $templates);
    }
}
