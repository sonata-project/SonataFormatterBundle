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

namespace Sonata\FormatterBundle\Tests\App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Sonata\FormatterBundle\Tests\App\Entity\TextEntity;

/**
 * @extends AbstractAdmin<TextEntity>
 */
final class TextEntityAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('simpleText', SimpleFormatterType::class, [
                'format' => 'rawhtml',
            ])
            ->add('text', FormatterType::class, [
                'format_field' => 'textFormat',
                'source_field' => 'rawText',
                'target_field' => 'text',
            ]);
    }
}
