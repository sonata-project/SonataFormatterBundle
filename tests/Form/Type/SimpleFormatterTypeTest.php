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
use FOS\CKEditorBundle\Model\StylesSetManagerInterface;
use FOS\CKEditorBundle\Model\TemplateManagerInterface;
use FOS\CKEditorBundle\Model\ToolbarManagerInterface;
use PHPUnit\Framework\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class SimpleFormatterTypeTest extends TestCase
{
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
     * @var StylesSetManagerInterface|MockObject
     */
    private $stylesSetManager;

    /**
     * @var ToolbarManagerInterface|MockObject
     */
    private $toolbarManager;

    /**
     * @var SimpleFormatterType
     */
    private $formType;

    public function setUp(): void
    {
        parent::setUp();

        $this->configManager = $this->createMock(ConfigManagerInterface::class);
        $this->pluginManager = $this->createMock(PluginManagerInterface::class);
        $this->templateManager = $this->createMock(TemplateManagerInterface::class);
        $this->stylesSetManager = $this->createMock(StylesSetManagerInterface::class);
        $this->toolbarManager = $this->createMock(ToolbarManagerInterface::class);

        $this->formType = new SimpleFormatterType(
            $this->configManager,
            $this->pluginManager,
            $this->templateManager,
            $this->stylesSetManager,
            $this->toolbarManager
        );
    }

    public function testBuildForm(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);

        $options = ['format' => 'format'];

        $this->formType->buildForm($formBuilder, $options);
    }

    public function testBuildViewWithDefaultConfig(): void
    {
        $view = $this->createMock(FormView::class);
        $form = $this->createMock(FormInterface::class);

        $this->configManager->expects($this->once())
            ->method('getConfig')
            ->with('context')
            ->will($this->returnValue(['toolbar' => ['Button1']]));
        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';

        $this->formType->buildView($view, $form, [
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

        $this->configManager->expects($this->once())
            ->method('getConfig')
            ->with('context')
            ->will($this->returnValue(['toolbar' => ['Button1']]));
        $this->stylesSetManager->expects($this->once())
            ->method('getStylesSets')
            ->will($this->returnValue($styleSets));
        $this->stylesSetManager->expects($this->once())
            ->method('hasStylesSets')
            ->will($this->returnValue(true));

        $view->vars['id'] = 'SomeId';
        $view->vars['name'] = 'SomeName';

        $this->formType->buildView($view, $form, [
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
