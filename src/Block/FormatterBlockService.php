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

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class FormatterBlockService extends AbstractAdminBlockService
{
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        return $this->renderResponse($blockContext->getTemplate(), [
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
        ], $response);
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block): void
    {
        $formMapper->add('settings', ImmutableArrayType::class, [
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

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'format' => 'richhtml',
            'rawContent' => '<b>Insert your custom content here</b>',
            'content' => '<b>Insert your custom content here</b>',
            'template' => '@SonataFormatter/Block/block_formatter.html.twig',
        ]);
    }

    public function getBlockMetadata($code = null): Metadata
    {
        return new Metadata(
            $this->getName(),
            null !== $code ? $code : $this->getName(),
            false,
            'SonataFormatterBundle',
            ['class' => 'fa fa-file-text-o']
        );
    }
}
