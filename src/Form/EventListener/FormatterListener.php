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
use Symfony\Component\PropertyAccess\PropertyAccess;

final class FormatterListener
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var string
     */
    private $formatField;

    /**
     * @var string
     */
    private $sourceField;

    /**
     * @var string
     */
    private $targetField;

    public function __construct(PoolInterface $pool, string $formatField, string $sourceField, string $targetField)
    {
        $this->pool = $pool;

        $this->formatField = $formatField;
        $this->sourceField = $sourceField;
        $this->targetField = $targetField;
    }

    public function postSubmit(FormEvent $event): void
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $format = $accessor->getValue($event->getData(), $this->formatField);
        $source = $accessor->getValue($event->getData(), $this->sourceField);

        // make sure the listener works with array
        $data = $event->getData();

        $accessor->setValue($data, $this->targetField, $source ? $this->pool->transform($format, $source) : null);

        $event->setData($data);
    }
}
