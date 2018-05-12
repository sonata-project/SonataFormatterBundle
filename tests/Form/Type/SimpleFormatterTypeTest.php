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

use FOS\CKEditorBundle\Model\ConfigManagerInterface;
use FOS\CKEditorBundle\Model\StylesSetManagerInterface;
use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class SimpleFormatterTypeTest extends TestCase
{
    public function testBuildForm()
    {
        $configManager = $this->createMock(ConfigManagerInterface::class);
        $formBuilder = $this->createMock(FormBuilderInterface::class);

        $type = new SimpleFormatterType($configManager);

        $options = ['format' => 'format'];

        $type->buildForm($formBuilder, $options);
    }

    public function testBuildViewWithDefaultConfig()
    {
        $configManager = $this->createMock(ConfigManagerInterface::class);
        $view = $this->createMock(FormView::class);
        $form = $this->createMock(FormInterface::class);

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

    public function testBuildViewWithStylesSet()
    {
        $configManager = $this->createMock(ConfigManagerInterface::class);
        $stylesSetManager = $this->createMock(StylesSetManagerInterface::class);
        $view = $this->createMock(FormView::class);
        $form = $this->createMock(FormInterface::class);

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
