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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SimpleFormatterType extends AbstractType
{
    /**
     * @var CKEditorConfigurationInterface
     */
    private $ckEditorConfiguration;

    public function __construct(
        CKEditorConfigurationInterface $ckEditorConfiguration
    ) {
        $this->ckEditorConfiguration = $ckEditorConfiguration;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $defaultConfig = $this->ckEditorConfiguration->getDefaultConfig();

        $ckeditorConfiguration = $this->ckEditorConfiguration->getConfig($defaultConfig);

        if (!\array_key_exists('toolbar', $ckeditorConfiguration)) {
            $ckeditorConfiguration['toolbar'] = array_values($options['ckeditor_toolbar_icons']);
        }

        if ($options['ckeditor_context']) {
            $contextConfig = $this->ckEditorConfiguration->getConfig($options['ckeditor_context']);
            $ckeditorConfiguration = array_merge($ckeditorConfiguration, $contextConfig);
        }

        if ($options['ckeditor_image_format']) {
            $ckeditorConfiguration['filebrowserImageUploadRouteParameters']['format'] = $options['ckeditor_image_format'];
        }

        $options['ckeditor_plugins'] = $this->ckEditorConfiguration->getPlugins();
        $options['ckeditor_templates'] = $this->ckEditorConfiguration->getTemplates();
        $options['ckeditor_style_sets'] = $this->ckEditorConfiguration->getStyles();

        if (\is_string($ckeditorConfiguration['toolbar'])) {
            $ckeditorConfiguration['toolbar'] = $this->ckEditorConfiguration->getToolbar($ckeditorConfiguration['toolbar']);
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
