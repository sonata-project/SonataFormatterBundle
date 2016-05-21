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

/**
 * Class FormatterTypeTest.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class FormatterTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildFormOneChoice()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();

        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->getMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $type = new FormatterType($pool, $translator, $configManager);

        $choiceFormBuilder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue(array('foo' => 'bar')));

        $formBuilder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
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
        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->getMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $type = new FormatterType($pool, $translator, $configManager);

        $choiceFormBuilder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $choiceFormBuilder->expects($this->once())->method('getOption')->with('choices')->will($this->returnValue(array('foo' => 'bar', 'foo2' => 'bar2')));

        $formBuilder = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
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

    public function testBuildViewWithDefaultConfig()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->getMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = array('toolbar' => array('Button1'));
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($pool, $translator, $configManager);

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->getMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_toolbar_icons' => array(),
        ));

        $this->assertSame($view->vars['ckeditor_configuration'], $defaultConfigValues);
    }

    public function testBuildViewWithoutDefaultConfig()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->getMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(false));

        $type = new FormatterType($pool, $translator, $configManager);

        $ckEditorToolBarIcons = array('Icon 1');

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->getMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_toolbar_icons' => $ckEditorToolBarIcons,
        ));

        $ckeditorConfiguration = array('toolbar' => $ckEditorToolBarIcons);
        $this->assertSame($view->vars['ckeditor_configuration'], $ckeditorConfiguration);
    }

    public function testBuildViewWithDefaultConfigAndWithToolbarIcons()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')->disableOriginalConstructor()->getMock();
        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $configManager = $this->getMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $defaultConfig = 'default';
        $defaultConfigValues = array('toolbar' => array('Button 1'));
        $configManager->expects($this->once())->method('getDefaultConfig')->will($this->returnValue($defaultConfig));
        $configManager->expects($this->once())->method('hasConfig')->with($defaultConfig)->will($this->returnValue(true));
        $configManager->expects($this->once())->method('getConfig')->with($defaultConfig)->will($this->returnValue($defaultConfigValues));

        $type = new FormatterType($pool, $translator, $configManager);

        $ckEditorToolBarIcons = array('Icon 1');

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->getMock('Symfony\Component\Form\FormView');
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';
        $form = $this->getMock('Symfony\Component\Form\FormInterface');
        $type->buildView($view, $form, array(
            'source_field' => 'SomeField',
            'format_field' => 'SomeFormat',
            'format_field_options' => 'SomeOptions',
            'ckeditor_context' => null,
            'ckeditor_basepath' => '',
            'ckeditor_toolbar_icons' => $ckEditorToolBarIcons,
        ));

        $this->assertSame($view->vars['ckeditor_configuration'], array('toolbar' => $defaultConfigValues['toolbar']));
    }
}
