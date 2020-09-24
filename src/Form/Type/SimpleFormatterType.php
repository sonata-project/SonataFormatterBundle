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

namespace Sonata\FormatterBundle\Form\Type;

use FOS\CKEditorBundle\Config\CKEditorConfigurationInterface;
use FOS\CKEditorBundle\Model\ConfigManagerInterface;
use FOS\CKEditorBundle\Model\PluginManagerInterface;
use FOS\CKEditorBundle\Model\StylesSetManagerInterface;
use FOS\CKEditorBundle\Model\TemplateManagerInterface;
use FOS\CKEditorBundle\Model\ToolbarManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SimpleFormatterType extends AbstractType
{
    /**
     * @var ConfigManagerInterface
     *
     * @deprecated since sonata-project/formatter bundle 4.3 and will be removed in version 5.0.
     */
    protected $configManager;

    /**
     * @var PluginManagerInterface
     */
    protected $pluginManager;

    /**
     * @var CKEditorConfigurationInterface
     */
    private $ckEditorConfiguration;

    /**
     * @var StylesSetManagerInterface
     */
    private $stylesSetManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var ToolbarManagerInterface
     */
    private $toolbarManager;

    /**
     * NEXT_MAJOR: Change signature for (CKEditorConfigurationInterface $ckEditorConfiguration).
     *
     * @param ConfigManagerInterface|CKEditorConfigurationInterface $configManagerOrCkEditorConfiguration
     */
    public function __construct(
        object $configManagerOrCkEditorConfiguration,
        ?PluginManagerInterface $pluginManager = null,
        ?TemplateManagerInterface $templateManager = null,
        ?StylesSetManagerInterface $stylesSetManager = null,
        ?ToolbarManagerInterface $toolbarManager = null
    ) {
        if (!$configManagerOrCkEditorConfiguration instanceof CKEditorConfigurationInterface) {
            @trigger_error(sprintf(
                'Passing %s as argument 1 to %s() is deprecated since sonata-project/formatter-bundle 4.3'
                .' and will throw a \TypeError in version 5.0. You must pass an instance of %s instead.',
                \get_class($configManagerOrCkEditorConfiguration),
                __METHOD__,
                CKEditorConfigurationInterface::class
            ), E_USER_DEPRECATED);

            if (!$configManagerOrCkEditorConfiguration instanceof ConfigManagerInterface) {
                throw new \TypeError(sprintf(
                    'Argument 3 passed to %s() must be an instance of %s or %s, %s given.',
                    __METHOD__,
                    CKEditorConfigurationInterface::class,
                    ConfigManagerInterface::class,
                    \get_class($configManagerOrCkEditorConfiguration)
                ));
            }

            $this->configManager = $configManagerOrCkEditorConfiguration;
        } else {
            $this->ckEditorConfiguration = $configManagerOrCkEditorConfiguration;
        }

        $this->pluginManager = $pluginManager;
        $this->templateManager = $templateManager;
        $this->stylesSetManager = $stylesSetManager;
        $this->toolbarManager = $toolbarManager;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (null !== $this->ckEditorConfiguration) {
            $defaultConfig = $this->ckEditorConfiguration->getDefaultConfig();
            $ckeditorConfiguration = $this->ckEditorConfiguration->getConfig($defaultConfig);
        } else {//NEXT_MAJOR: Remove this case
            $defaultConfig = $this->configManager->getDefaultConfig();

            if ($this->configManager->hasConfig($defaultConfig)) {
                $ckeditorConfiguration = $this->configManager->getConfig($defaultConfig);
            } else {
                $ckeditorConfiguration = [];
            }
        }

        if (!\array_key_exists('toolbar', $ckeditorConfiguration)) {
            $ckeditorConfiguration['toolbar'] = array_values($options['ckeditor_toolbar_icons']);
        }

        if ($options['ckeditor_context']) {
            if (null !== $this->configManager) {//NEXT_MAJOR: Remove this case
                $contextConfig = $this->configManager->getConfig($options['ckeditor_context']);
            } else {
                $contextConfig = $this->ckEditorConfiguration->getConfig($options['ckeditor_context']);
            }

            $ckeditorConfiguration = array_merge($ckeditorConfiguration, $contextConfig);
        }

        if ($options['ckeditor_image_format']) {
            $ckeditorConfiguration['filebrowserImageUploadRouteParameters']['format'] = $options['ckeditor_image_format'];
        }

        if (null !== $this->ckEditorConfiguration) {
            $options['ckeditor_plugins'] = $this->ckEditorConfiguration->getPlugins();
            $options['ckeditor_templates'] = $this->ckEditorConfiguration->getTemplates();
            $options['ckeditor_style_sets'] = $this->ckEditorConfiguration->getStyles();

            if (\is_string($ckeditorConfiguration['toolbar'])) {
                $ckeditorConfiguration['toolbar'] = $this->ckEditorConfiguration->getToolbar($ckeditorConfiguration['toolbar']);
            }
        } else {//NEXT_MAJOR: Remove this case
            if ($this->pluginManager->hasPlugins()) {
                $options['ckeditor_plugins'] = $this->pluginManager->getPlugins();
            }

            if ($this->templateManager->hasTemplates()) {
                $options['ckeditor_templates'] = $this->templateManager->getTemplates();
            }

            if ($this->stylesSetManager->hasStylesSets()) {
                $options['ckeditor_style_sets'] = $this->stylesSetManager->getStylesSets();
            } else {
                $options['ckeditor_style_sets'] = [];
            }

            if (\is_string($ckeditorConfiguration['toolbar'])) {
                $ckeditorConfiguration['toolbar'] = $this->toolbarManager->resolveToolbar($ckeditorConfiguration['toolbar']);
            }
        }

        $view->vars['ckeditor_configuration'] = $ckeditorConfiguration;
        $view->vars['ckeditor_basepath'] = $options['ckeditor_basepath'];
        $view->vars['ckeditor_plugins'] = $options['ckeditor_plugins'];
        $view->vars['ckeditor_templates'] = $options['ckeditor_templates'];
        $view->vars['ckeditor_style_sets'] = $options['ckeditor_style_sets'];

        $view->vars['format'] = $options['format'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'ckeditor_toolbar_icons' => [[
                 'Bold', 'Italic', 'Underline',
                 '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord',
                 '-', 'Undo', 'Redo',
                 '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
                 '-', 'Blockquote',
                 '-', 'Image', 'Link', 'Unlink', 'Table', ],
                 ['Maximize', 'Source'],
            ],
            'ckeditor_basepath' => 'bundles/sonataformatter/vendor/ckeditor',
            'ckeditor_context' => null,
            'ckeditor_image_format' => null,
            'ckeditor_plugins' => [],
            'ckeditor_templates' => [],
            'format_options' => [
                'attr' => [
                    'class' => 'span10 col-sm-10 col-md-10',
                    'rows' => 20,
                ],
            ],
        ]);

        $resolver->setRequired([
            'format',
        ]);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix()
    {
        return 'sonata_simple_formatter_type';
    }
}
