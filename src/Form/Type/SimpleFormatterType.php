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

use Ivory\CKEditorBundle\Model\ConfigManagerInterface;
use Ivory\CKEditorBundle\Model\PluginManagerInterface;
use Ivory\CKEditorBundle\Model\TemplateManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimpleFormatterType extends AbstractType
{
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * @var PluginManagerInterface
     */
    protected $pluginManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @param ConfigManagerInterface        $configManager   An Ivory CKEditor bundle configuration manager
     * @param PluginManagerInterface|null   $pluginManager   An Ivory CKEditor bundle plugin manager
     * @param TemplateManagerInterface|null $templateManager An Ivory CKEditor bundle template manager
     */
    public function __construct(
        ConfigManagerInterface $configManager,
        PluginManagerInterface $pluginManager = null,
        TemplateManagerInterface $templateManager = null
    ) {
        $this->configManager = $configManager;
        $this->pluginManager = $pluginManager;
        $this->templateManager = $templateManager;
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

        $view->vars['ckeditor_configuration'] = $ckeditorConfiguration;
        $view->vars['ckeditor_basepath'] = $options['ckeditor_basepath'];
        $view->vars['ckeditor_plugins'] = $options['ckeditor_plugins'];
        $view->vars['ckeditor_templates'] = $options['ckeditor_templates'];

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
