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
use Sonata\FormatterBundle\Form\EventListener\FormatterListener;
use Sonata\FormatterBundle\Formatter\Pool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class FormatterType extends AbstractType
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

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
     * @param ConfigManagerInterface|null   $configManager   An Ivory CKEditor bundle configuration manager
     * @param PluginManagerInterface|null   $pluginManager   An Ivory CKEditor bundle plugin manager
     * @param TemplateManagerInterface|null $templateManager An Ivory CKEditor bundle template manager
     */
    public function __construct(
        Pool $pool,
        TranslatorInterface $translator,
        ConfigManagerInterface $configManager,
        PluginManagerInterface $pluginManager = null,
        TemplateManagerInterface $templateManager = null
    ) {
        $this->pool = $pool;
        $this->translator = $translator;
        $this->configManager = $configManager;
        $this->pluginManager = $pluginManager;
        $this->templateManager = $templateManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (is_array($options['format_field'])) {
            list($formatField, $formatPropertyPath) = $options['format_field'];
            $options['format_field_options']['property_path'] = $formatPropertyPath;
        } else {
            $formatField = $options['format_field'];
            $options['format_field_options']['property_path'] = $formatField;
        }

        if (!array_key_exists('data', $options['format_field_options']) ||
             !array_key_exists($options['format_field_options']['data'], $this->pool->getFormatters())) {
            $options['format_field_options']['data'] = $this->pool->getDefaultFormatter();
        }

        if (is_array($options['source_field'])) {
            list($sourceField, $sourcePropertyPath) = $options['source_field'];
            $options['source_field_options']['property_path'] = $sourcePropertyPath;
        } else {
            $sourceField = $options['source_field'];
            $options['source_field_options']['property_path'] = $sourceField;
        }

        $builder->add($formatField, ChoiceType::class, $options['format_field_options']);

        // If there's only one possible format, do not display the choices
        $formatChoices = $builder->get($formatField)->getOption('choices');

        if (1 === count($formatChoices)) {
            // Remove the choice field
            unset($options['format_field_options']['choices']);
            $builder->remove($formatField);

            // Replace it with an hidden field
            $builder->add($formatField, HiddenType::class, $options['format_field_options']);
        }

        $builder
            ->add($sourceField, TextareaType::class, $options['source_field_options']);

        /*
         * The listener option only work if the source field is after the current field
         */
        if ($options['listener']) {
            if (!$options['event_dispatcher'] instanceof EventDispatcherInterface) {
                throw new \RuntimeException('The event_dispatcher option must be an instance of EventDispatcherInterface');
            }

            $listener = new FormatterListener(
                $this->pool,
                $options['format_field_options']['property_path'],
                $options['source_field_options']['property_path'],
                $options['target_field']
            );

            $options['event_dispatcher']->addListener(FormEvents::SUBMIT, [$listener, 'postSubmit']);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (is_array($options['source_field'])) {
            list($sourceField) = $options['source_field'];
            $view->vars['source_field'] = $sourceField;
        } else {
            $view->vars['source_field'] = $options['source_field'];
        }

        if (is_array($options['format_field'])) {
            list($formatField) = $options['format_field'];
            $view->vars['format_field'] = $formatField;
        } else {
            $view->vars['format_field'] = $options['format_field'];
        }

        $view->vars['format_field_options'] = $options['format_field_options'];

        $defaultConfig = $this->configManager->getDefaultConfig();

        if ($this->configManager->hasConfig($defaultConfig)) {
            $ckeditorConfiguration = $this->configManager->getConfig($defaultConfig);
        } else {
            $ckeditorConfiguration = [];
        }

        if (!array_key_exists('toolbar', $ckeditorConfiguration)) {
            $ckeditorConfiguration['toolbar'] = array_values($options['ckeditor_toolbar_icons']);
        }

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

        $view->vars['source_id'] = str_replace($view->vars['name'], $view->vars['source_field'], $view->vars['id']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $pool = $this->pool;
        $translator = $this->translator;

        $formatters = [];
        foreach ($pool->getFormatters() as $code => $instance) {
            $formatters[$code] = $code;
        }

        $formatFieldOptions = [
            'choices' => $formatters,
        ];

        if (count($formatters) > 1) {
            $formatFieldOptions['choice_translation_domain'] = 'SonataFormatterBundle';

            // choices_as_values options is not needed in SF 3.0+
            if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
                $formatFieldOptions['choices_as_values'] = true;
            }
        }

        $resolver->setDefaults([
            'inherit_data' => true,
            'event_dispatcher' => null,
            'format_field' => null,
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
            'format_field_options' => $formatFieldOptions,
            'source_field' => null,
            'source_field_options' => [
                'attr' => ['class' => 'span10 col-sm-10 col-md-10', 'rows' => 20],
            ],
            'target_field' => null,
            'listener' => true,
        ]);

        $resolver->setRequired([
            'format_field',
            'source_field',
            'target_field',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'sonata_formatter_type';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
