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

namespace Sonata\FormatterBundle\Form\EventListener;

use Sonata\FormatterBundle\Formatter\PoolInterface;
use Symfony\Component\Form\FormEvent;

final class FormatterListener
{
    public function __construct(
        private PoolInterface $pool,
        private string $formatField,
        private string $sourceField,
        private string $targetField
    ) {
    }

    public function postSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if (!\is_array($data)) {
            return;
        }

        $source = $data[$this->sourceField] ?? null;
        $format = $data[$this->formatField] ?? null;

        $data[$this->targetField] = null !== $source && null !== $format ?
            $this->pool->transform($format, $source) :
            null;

        $event->setData($data);
    }
}
