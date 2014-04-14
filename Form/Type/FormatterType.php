<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Form\Type;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

use Ivory\CKEditorBundle\Model\ConfigManagerInterface;

use Sonata\FormatterBundle\Form\EventListener\FormatterListener;
use Sonata\FormatterBundle\Formatter\Pool;

class FormatterType extends AbstractType
{
    protected $pool;

    protected $translator;

    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * Constructor
     *
     * @param Pool                   $pool          A Formatter Pool service
     * @param TranslatorInterface    $translator    A Symfony Translator service
     * @param ConfigManagerInterface $configManager An Ivory CKEditor bundle configuration manager
     */
    public function __construct(Pool $pool, TranslatorInterface $translator, ConfigManagerInterface $configManager)
    {
        $this->pool = $pool;
        $this->translator = $translator;
        $this->configManager = $configManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (is_array($options['format_field'])) {
            list($formatField, $formatPropertyPath) = $options['format_field'];
            $options['format_field_options']['property_path'] = $formatPropertyPath;
        } else {
            $formatField = $options['format_field'];
            $options['format_field_options']['property_path'] = $formatField;
        }

        if (is_array($options['source_field'])) {
            list($sourceField, $sourcePropertyPath) = $options['source_field'];
            $options['source_field_options']['property_path'] = $sourcePropertyPath;
        } else {
            $sourceField = $options['source_field'];
            $options['source_field_options']['property_path'] = $sourceField;
        }

        $builder
            ->add($formatField, 'choice', $options['format_field_options'])
            ->add($sourceField, 'textarea', $options['source_field_options']);

        /**
         * The listener option only work if the source field is after the current field
         */
        if ($options['listener']) {

            if (!$options['event_dispatcher'] instanceof EventDispatcherInterface) {
                throw new \RuntimeException('The event_dispatcher option but be an instance of EventDispatcherInterface');
            }

            $listener = new FormatterListener(
                $this->pool,
                $options['format_field_options']['property_path'],
                $options['source_field_options']['property_path'],
                $options['target_field']
            );

            $options['event_dispatcher']->addListener(FormEvents::POST_SUBMIT, array($listener, 'postSubmit'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {

        if (is_array($options['source_field'])) {
            list($sourceField, ) = $options['source_field'];
            $view->vars['source_field'] = $sourceField;
        } else {
            $view->vars['source_field'] = $options['source_field'];
        }

        if (is_array($options['format_field'])) {
            list($formatField, ) = $options['format_field'];
            $view->vars['format_field'] = $formatField;
        } else {
            $view->vars['format_field'] = $options['format_field'];
        }

        $ckeditorConfiguration = array(
            'toolbar'       => array_values($options['ckeditor_toolbar_icons']),
            'customConfig'  => false,
            'contentsCss'   => false,
        );

        if ($options['ckeditor_context']) {
            $contextConfig = $this->configManager->getConfig($options['ckeditor_context']);
            $ckeditorConfiguration = array_merge($ckeditorConfiguration, $contextConfig);
        }

        $view->vars['ckeditor_configuration'] = $ckeditorConfiguration;
        $view->vars['ckeditor_basepath'] = $options['ckeditor_basepath'];

        $view->vars['source_id'] = str_replace($view->vars['name'], $view->vars['source_field'], $view->vars['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $pool = $this->pool;
        $translator = $this->translator;

        $resolver->setDefaults(array(
            'inherit_data'              => true,
            'event_dispatcher'          => null,
            'format_field'              => null,
            'ckeditor_toolbar_icons'    => array( array(
                 'Bold', 'Italic', 'Underline',
                 '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord',
                 '-', 'Undo', 'Redo',
                 '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
                 '-', 'Blockquote',
                 '-', 'Image', 'Link', 'Unlink', 'Table'),
                 array('Maximize', 'Source')
            ),
            'ckeditor_context'          => null,
            'ckeditor_basepath'         => 'bundles/sonataformatter/vendor/ckeditor',
            'format_field_options'      => array(
                'choices'               => function (Options $options) use ($pool, $translator) {
                    $formatters = array();
                    foreach ($pool->getFormatters() as $code => $instance) {
                        $formatters[$code] = $translator->trans($code, array(), 'SonataFormatterBundle');
                    }

                    return $formatters;
                }
            ),
            'source_field' => null,
            'source_field_options'      => array(
                'attr' => array('class' => 'span10 col-sm-10 col-md-10', 'rows' => 20)
            ),
            'target_field' => null,
            'listener'     => true,
        ));

        $resolver->setRequired(array(
            'format_field',
            'source_field',
            'target_field'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_formatter_type';
    }
}