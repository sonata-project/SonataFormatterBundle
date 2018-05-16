<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Form\Type;

use FOS\CKEditorBundle\Model\ConfigManagerInterface as FOSConfigManagerInterface;
use FOS\CKEditorBundle\Model\PluginManagerInterface as FOSPluginManagerInterface;
use FOS\CKEditorBundle\Model\StylesSetManagerInterface as FOSStylesSetManagerInterface;
use FOS\CKEditorBundle\Model\TemplateManagerInterface as FOSTemplateManagerInterface;
use FOS\CKEditorBundle\Model\ToolbarManagerInterface as FOSToolbarManagerInterface;
use Ivory\CKEditorBundle\Model\ConfigManagerInterface as IvoryConfigManagerInterface;
use Ivory\CKEditorBundle\Model\PluginManagerInterface as IvoryPluginManagerInterface;
use Ivory\CKEditorBundle\Model\StylesSetManagerInterface as IvoryStylesSetManagerInterface;
use Ivory\CKEditorBundle\Model\TemplateManagerInterface as IvoryTemplateManagerInterface;
use Ivory\CKEditorBundle\Model\ToolbarManagerInterface as IvoryToolbarManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimpleFormatterType extends AbstractType
{
    /**
     * @var FOSConfigManagerInterface|IvoryConfigManagerInterface
     */
    protected $configManager;

    /**
     * @var FOSPluginManagerInterface|IvoryPluginManagerInterface
     */
    protected $pluginManager;

    /**
     * @var FOSStylesSetManagerInterface|IvoryStylesSetManagerInterface
     */
    private $stylesSetManager;

    /**
     * @var FOSTemplateManagerInterface|IvoryTemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var FOSToolbarManagerInterface|IvoryToolbarManagerInterface
     */
    private $toolbarManager;

    /**
     * @param FOSConfigManagerInterface|IvoryConfigManagerInterface
     * $configManager   A CKEditor bundle configuration manager
     * @param FOSPluginManagerInterface|IvoryPluginManagerInterface|null
     * $pluginManager   A CKEditor bundle plugin manager
     * @param FOSTemplateManagerInterface|IvoryTemplateManagerInterface|null
     * $templateManager A CKEditor bundle template manager
     * @param FOSStylesSetManagerInterface|IvoryStylesSetManagerInterface|null
     * $stylesSetManager A CKEditor bundle styles set manager
     * @param FOSToolbarManagerInterface|IvoryToolbarManagerInterface|null
     * $toolbarManager A CKEditor bundle toolbar manager
     */
    public function __construct(
        $configManager,
        $pluginManager = null,
        $templateManager = null,
        $stylesSetManager = null,
        $toolbarManager = null
    ) {
        if (!$configManager instanceof IvoryConfigManagerInterface
            && !$configManager instanceof FOSConfigManagerInterface
        ) {
            throw new \InvalidArgumentException(sprintf(
                '$configManager should be of type "%s" or "%s".',
                FOSConfigManagerInterface::class,
                IvoryConfigManagerInterface::class
            ));
        }

        if ($pluginManager
            && !$pluginManager instanceof IvoryPluginManagerInterface
            && !$pluginManager instanceof FOSPluginManagerInterface
        ) {
            throw new \InvalidArgumentException(sprintf(
                '$pluginManager should be of type "%s" or "%s".',
                FOSPluginManagerInterface::class,
                IvoryPluginManagerInterface::class
            ));
        }

        if ($templateManager
            && !$templateManager instanceof IvoryTemplateManagerInterface
            && !$templateManager instanceof FOSTemplateManagerInterface
        ) {
            throw new \InvalidArgumentException(sprintf(
                '$templateManager should be of type "%s" or "%s".',
                FOSTemplateManagerInterface::class,
                IvoryTemplateManagerInterface::class
            ));
        }

        if ($stylesSetManager
            && !$stylesSetManager instanceof IvoryStylesSetManagerInterface
            && !$stylesSetManager instanceof FOSStylesSetManagerInterface
        ) {
            throw new \InvalidArgumentException(sprintf(
                '$stylesSetManager should be of type "%s" or "%s".',
                FOSStylesSetManagerInterface::class,
                IvoryStylesSetManagerInterface::class
            ));
        }

        if ($toolbarManager
            && !$toolbarManager instanceof IvoryToolbarManagerInterface
            && !$toolbarManager instanceof FOSToolbarManagerInterface
        ) {
            throw new \InvalidArgumentException(sprintf(
                '$toolbarManager should be of type "%s" or "%s".',
                FOSToolbarManagerInterface::class,
                IvoryToolbarManagerInterface::class
            ));
        }

        $this->configManager = $configManager;
        $this->pluginManager = $pluginManager;
        $this->templateManager = $templateManager;
        $this->stylesSetManager = $stylesSetManager;
        $this->toolbarManager = $toolbarManager;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $ckeditorConfiguration = [
            'toolbar' => array_values($options['ckeditor_toolbar_icons']),
        ];

        if ($options['ckeditor_context']) {
            $contextConfig = $this->configManager->getConfig($options['ckeditor_context']);
            $ckeditorConfiguration = array_merge($ckeditorConfiguration, $contextConfig);
        }

        if ($options['ckeditor_image_format']) {
            $ckeditorConfiguration['filebrowserImageUploadRouteParameters']['format'] = $options['ckeditor_image_format'];
        }

        if (null !== $this->pluginManager && $this->pluginManager->hasPlugins()) {
            $options['ckeditor_plugins'] = $this->pluginManager->getPlugins();
        }

        if (null !== $this->templateManager && $this->templateManager->hasTemplates()) {
            $options['ckeditor_templates'] = $this->templateManager->getTemplates();
        }

        if (null !== $this->stylesSetManager && $this->stylesSetManager->hasStylesSets()) {
            $options['ckeditor_style_sets'] = $this->stylesSetManager->getStylesSets();
        } else {
            $options['ckeditor_style_sets'] = [];
        }

        if (null !== $this->toolbarManager && is_string($ckeditorConfiguration['toolbar'])) {
            $ckeditorConfiguration['toolbar'] = $this->toolbarManager->resolveToolbar($ckeditorConfiguration['toolbar']);
        }

        $view->vars['ckeditor_configuration'] = $ckeditorConfiguration;
        $view->vars['ckeditor_basepath'] = $options['ckeditor_basepath'];
        $view->vars['ckeditor_plugins'] = $options['ckeditor_plugins'];
        $view->vars['ckeditor_templates'] = $options['ckeditor_templates'];
        $view->vars['ckeditor_style_sets'] = $options['ckeditor_style_sets'];

        $view->vars['format'] = $options['format'];
    }

    /**
     * Symfony >= 3.
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
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

    public function getParent()
    {
        return TextareaType::class;
    }

    public function getBlockPrefix()
    {
        return 'sonata_simple_formatter_type';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
