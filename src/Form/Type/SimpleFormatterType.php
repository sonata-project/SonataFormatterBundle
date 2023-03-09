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
    public function __construct(
        private CKEditorConfigurationInterface $ckEditorConfiguration
    ) {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $defaultConfig = $this->ckEditorConfiguration->getDefaultConfig();

        if (null === $defaultConfig) {
            throw new \RuntimeException('You must add a default configuration for the CKEditor.');
        }

        $ckeditorConfiguration = $this->ckEditorConfiguration->getConfig($defaultConfig);
        $ckeditorConfiguration['toolbar'] ??= array_values($options['ckeditor_toolbar_icons']);

        if (\is_string($options['ckeditor_context'])) {
            $contextConfig = $this->ckEditorConfiguration->getConfig($options['ckeditor_context']);

            $ckeditorConfiguration = array_merge($ckeditorConfiguration, $contextConfig);
        }

        if (\is_string($options['ckeditor_image_format'])) {
            $ckeditorConfiguration['filebrowserImageUploadRouteParameters']['format'] = $options['ckeditor_image_format'];
        }

        if (\is_string($ckeditorConfiguration['toolbar'])) {
            $ckeditorConfiguration['toolbar'] = $this->ckEditorConfiguration->getToolbar($ckeditorConfiguration['toolbar']);
        }

        $view->vars['ckeditor_configuration'] = $ckeditorConfiguration;
        $view->vars['ckeditor_plugins'] = $this->ckEditorConfiguration->getPlugins();
        $view->vars['ckeditor_templates'] = $this->ckEditorConfiguration->getTemplates();
        $view->vars['ckeditor_style_sets'] = $this->ckEditorConfiguration->getStyles();
        $view->vars['format'] = $options['format'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'ckeditor_toolbar_icons' => [
                [
                    'Bold', 'Italic', 'Underline',
                    '-', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord',
                    '-', 'Undo', 'Redo',
                    '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
                    '-', 'Blockquote',
                    '-', 'Image', 'Link', 'Unlink', 'Table',
                ],
                ['Maximize', 'Source'],
            ],
            'ckeditor_context' => null,
            'ckeditor_image_format' => null,
        ]);

        $resolver->setRequired([
            'format',
        ]);

        $resolver->setAllowedTypes('format', 'string');
        $resolver->setAllowedTypes('ckeditor_toolbar_icons', 'array');
        $resolver->setAllowedTypes('ckeditor_context', ['null', 'string']);
        $resolver->setAllowedTypes('ckeditor_image_format', ['null', 'string']);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sonata_simple_formatter_type';
    }
}
