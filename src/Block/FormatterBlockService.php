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

namespace Sonata\FormatterBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\Service\EditableBlockService;
use Sonata\BlockBundle\Form\Mapper\FormMapper;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Meta\MetadataInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class FormatterBlockService extends AbstractBlockService implements EditableBlockService
{
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        return $this->renderResponse($blockContext->getTemplate(), [
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ], $response);
    }

    public function configureEditForm(FormMapper $form, BlockInterface $block): void
    {
        $form->add('settings', ImmutableArrayType::class, [
            'keys' => [
                ['content', FormatterType::class, static function (FormBuilderInterface $formBuilder) {
                    return [
                        'event_dispatcher' => $formBuilder->getEventDispatcher(),
                        'format_field' => ['format', '[format]'],
                        'source_field' => ['rawContent', '[rawContent]'],
                        'target_field' => '[content]',
                        'label' => 'form.label_content',
                    ];
                }],
            ],
            'translation_domain' => 'SonataFormatterBundle',
        ]);
    }

    public function configureCreateForm(FormMapper $form, BlockInterface $block): void
    {
        $this->configureEditForm($form, $block);
    }

    public function validate(ErrorElement $errorElement, BlockInterface $block): void
    {
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'format' => 'richhtml',
            'rawContent' => '<b>Insert your custom content here</b>',
            'content' => '<b>Insert your custom content here</b>',
            'template' => '@SonataFormatter/Block/block_formatter.html.twig',
        ]);
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata(
            'sonata.formatter.block.formatter',
            null,
            null,
            'SonataFormatterBundle',
            ['class' => 'fa fa-file-text-o']
        );
    }
}
