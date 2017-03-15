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

use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Sonata\FormatterBundle\Tests\TestCase;

class SimpleFormatterTypeTest extends TestCase
{
    public function testBuildForm()
    {
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');

        $type = new SimpleFormatterType($configManager);

        $options = array('format' => 'format');

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildViewWithDefaultConfig()
    {
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $form = $this->createMock('Symfony\Component\Form\FormInterface');

        $configManager->expects($this->once())
            ->method('getConfig')
            ->with('context')
            ->will($this->returnValue(array('toolbar' => array('Button1'))));
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';

        $type = new SimpleFormatterType($configManager);

        $type->buildView($view, $form, array(
            'format' => 'format',
            'ckeditor_context' => 'context',
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => array(),
            'ckeditor_templates' => array(),
            'ckeditor_toolbar_icons' => array(),
        ));

        $this->assertSame($view->vars['ckeditor_configuration'], array('toolbar' => array('Button1')));
    }
}
