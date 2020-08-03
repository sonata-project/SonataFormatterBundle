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

class LegacySimpleFormatterTypeTest extends TestCase
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

    protected function setUp(): void
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

    /**
     * @doesNotPerformAssertions
     */
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
            ->willReturn(['toolbar' => ['Button1']]);
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
            ->willReturn(['toolbar' => ['Button1']]);
        $this->stylesSetManager->expects($this->once())
            ->method('getStylesSets')
            ->willReturn($styleSets);
        $this->stylesSetManager->expects($this->once())
            ->method('hasStylesSets')
            ->willReturn(true);

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

    public function testBuildViewWithToolbarOptionsSetAsPredefinedString(): void
    {
        $defaultConfig = 'default';
        $defaultConfigValues = ['toolbar' => 'basic'];
        $basicToolbarSets = [
            0 => [
                0 => 'Bold',
                1 => 'Italic',
            ],
            1 => [
                0 => 'NumberedList',
                1 => 'BulletedList',

            ],
        ];

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
            ->with('basic')
            ->willReturn($basicToolbarSets);

        /** @var \Symfony\Component\Form\FormView $view */
        $view = $this->createMock(FormView::class);
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
            'format' => [],
        ]);

        $defaultConfigValues['toolbar'] = $basicToolbarSets;
        $this->assertSame($view->vars['ckeditor_configuration'], $defaultConfigValues);
    }
}
