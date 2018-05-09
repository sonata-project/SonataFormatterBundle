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
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

class SimpleFormatterTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $configManager = $this->createMock('FOS\CKEditorBundle\Model\ConfigManagerInterface');
        $formBuilder = $this->createMock('Symfony\Component\Form\FormBuilderInterface');

        $type = new SimpleFormatterType($configManager);

        $options = ['format' => 'format'];

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildViewWithDefaultConfig(): void
    {
        $configManager = $this->createMock('FOS\CKEditorBundle\Model\ConfigManagerInterface');
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

    public function testBuildViewWithStylesSet(): void
    {
        $configManager = $this->createMock('FOS\CKEditorBundle\Model\ConfigManagerInterface');
        $stylesSetManager = $this->createMock('FOS\CKEditorBundle\Model\StylesSetManagerInterface');
        $view = $this->createMock('Symfony\Component\Form\FormView');
        $form = $this->createMock('Symfony\Component\Form\FormInterface');

        $styleSets = [
            'my_styleset' => [
                ['name' => 'Blue Title', 'element' => 'h2', 'styles' => ['color' => 'Blue']],
                ['name' => 'CSS Style', 'element' => 'span', 'attributes' => ['class' => 'my_style']],
                ['name' => 'Multiple Element Style', 'element' => ['h2', 'span'], 'attributes' => ['class' => 'my_class']],
                ['name' => 'Widget Style', 'type' => 'widget', 'widget' => 'my_widget', 'attributes' => ['class' => 'my_widget_style']],
            ],
        ];

        $configManager->expects($this->once())
            ->method('getConfig')
            ->with('context')
            ->will($this->returnValue(['toolbar' => ['Button1']]));
        $stylesSetManager->expects($this->once())
            ->method('getStylesSets')
            ->will($this->returnValue($styleSets));
        $stylesSetManager->expects($this->once())
            ->method('hasStylesSets')
            ->will($this->returnValue(true));

        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';

        $type = new SimpleFormatterType($configManager, null, null, $stylesSetManager);

        $type->buildView($view, $form, [
            'format' => 'format',
            'ckeditor_context' => 'context',
            'ckeditor_image_format' => 'format',
            'ckeditor_basepath' => '',
            'ckeditor_plugins' => [],
            'ckeditor_templates' => [],
            'ckeditor_style_sets' => [],
            'ckeditor_toolbar_icons' => [],
        ]);

        $this->assertSame($view->vars['ckeditor_style_sets'], $styleSets);
    }
}
