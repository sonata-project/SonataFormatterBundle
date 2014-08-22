<?php

/*
* This file is part of the "Outil Auteur" project.
*
* (c) 2014 - DED (CanalPlus Group)
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sonata\FormatterBundle\Tests\Form\Type;

use Sonata\FormatterBundle\Form\Type\FormatterType;

/**
 * Class FormatterTypeTest
 *
 * @package Sonata\FormatterBundle\Tests\Form\Type
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
                'property_path' => ''
            ),
            'source_field_options' => array(
                'property_path' => ''
            ),
            'listener' => false
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
                'property_path' => ''
            ),
            'source_field_options' => array(
                'property_path' => ''
            ),
            'listener' => false
        );

        $type->buildForm($formBuilder, $options);
    }

}
