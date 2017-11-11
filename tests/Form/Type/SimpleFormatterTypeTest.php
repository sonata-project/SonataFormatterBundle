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

use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

class SimpleFormatterTypeTest extends TestCase
{
    public function testBuildForm()
    {
        $configManager = $this->createMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');

        $type = new SimpleFormatterType($configManager);

        $options = ['format' => 'format'];

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
            ->will($this->returnValue(['toolbar' => ['Button1']]));
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';

        $type = new SimpleFormatterType($configManager);

        $type->buildView($view, $form, [
            'format' => 'format',
            'ckeditor_context' => 'context',
            'ckeditor_image_format' => 'format',
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => [],
            'ckeditor_templates' => [],
            'ckeditor_toolbar_icons' => [],
        ]);

        $this->assertSame(
            $view->vars['ckeditor_configuration'],
            ['toolbar' => ['Button1'], 'filebrowserImageUploadRouteParameters' => ['format' => 'format']]
        );
    }
}
