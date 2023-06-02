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
use Sonata\FormatterBundle\Form\EventListener\FormatterListener;
use Sonata\FormatterBundle\Formatter\PoolInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @psalm-suppress MissingTemplateParam https://github.com/phpstan/phpstan-symfony/issues/320
 */
final class FormatterType extends AbstractType
{
    public function __construct(
        private PoolInterface $pool,
        private CKEditorConfigurationInterface $ckEditorConfiguration
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formatField = $options['format_field'];
        \assert(\is_string($formatField));
        $sourceField = $options['source_field'];
        \assert(\is_string($sourceField));

        if (!isset($options['format_field_options']['empty_data'])
             || !\array_key_exists($options['format_field_options']['empty_data'], $this->pool->getFormatters())) {
            $options['format_field_options']['empty_data'] = $this->pool->getDefaultFormatter();
        }

        $formatType = ChoiceType::class;
        $formatOptions = $options['format_field_options'];
        $formatOptions['choices'] ??= $this->getChoices();

        if (1 >= \count($formatOptions['choices'])) {
            unset($formatOptions['choices']);
            $formatType = HiddenType::class;
        } else {
            $formatOptions['choice_translation_domain'] = 'SonataFormatterBundle';
        }

        $sourceOptions = $options['source_field_options'];

        $builder->add($formatField, $formatType, $formatOptions);
        $builder->add($sourceField, TextareaType::class, $sourceOptions);

        /*
         * The listener option only work if the source field is after the current field
         */
        if (true === $options['listener']) {
            $targetField = $options['target_field'];
            \assert(\is_string($targetField));

            $builder->add($targetField, HiddenType::class);

            $listener = new FormatterListener(
                $this->pool,
                $formatField,
                $sourceField,
                $targetField
            );

            $builder->addEventListener(FormEvents::PRE_SUBMIT, [$listener, 'postSubmit']);
        }
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

        $view->vars['source_field'] = $options['source_field'];
        $view->vars['format_field'] = $options['format_field'];
        $view->vars['ckeditor_configuration'] = $ckeditorConfiguration;
        $view->vars['ckeditor_plugins'] = $this->ckEditorConfiguration->getPlugins();
        $view->vars['ckeditor_templates'] = $this->ckEditorConfiguration->getTemplates();
        $view->vars['ckeditor_style_sets'] = $this->ckEditorConfiguration->getStyles();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'inherit_data' => true,
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
            'format_field_options' => [],
            'source_field_options' => [
                'attr' => ['class' => 'span10 col-sm-10 col-md-10', 'rows' => 20],
            ],
            'target_field' => null,
            'listener' => true,
        ]);

        $resolver->setRequired([
            'format_field',
            'source_field',
        ]);

        $resolver->setNormalizer('target_field', static function (Options $options, ?string $value): ?string {
            if (true === $options['listener'] && null === $value) {
                throw new InvalidOptionsException('When "listener" option is set to "true", "target_field" option is required');
            }

            return $value;
        });

        $resolver->setAllowedTypes('format_field', 'string');
        $resolver->setAllowedTypes('source_field', 'string');
        $resolver->setAllowedTypes('target_field', ['null', 'string']);
        $resolver->setAllowedTypes('ckeditor_toolbar_icons', 'array');
        $resolver->setAllowedTypes('ckeditor_context', ['null', 'string']);
        $resolver->setAllowedTypes('ckeditor_image_format', ['null', 'string']);
        $resolver->setAllowedTypes('format_field_options', 'array');
        $resolver->setAllowedTypes('source_field_options', 'array');
        $resolver->setAllowedTypes('listener', 'bool');
    }

    public function getBlockPrefix(): string
    {
        return 'sonata_formatter_type';
    }

    /**
     * @return array<string, string>
     */
    private function getChoices(): array
    {
        $choices = [];

        foreach ($this->pool->getFormatters() as $code => $instance) {
            $choices[$code] = $code;
        }

        return $choices;
    }
}
