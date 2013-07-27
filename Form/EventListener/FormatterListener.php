<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Form\EventListener;

use Sonata\FormatterBundle\Formatter\Pool;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

class FormatterListener
{
    protected $pool;

    protected $formatField;

    protected $sourcefield;

    protected $targetField;

    /**
     * @param Pool   $pool
     * @param string $format
     * @param string $source
     * @param string $target
     */
    public function __construct(Pool $pool, $formatField, $sourcefield, $targetField)
    {
        $this->pool = $pool;

        $this->formatField = $formatField;
        $this->sourcefield = $sourcefield;
        $this->targetField = $targetField;
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        $format = $accessor->getValue($event->getData(), $this->formatField);
        $source = $accessor->getValue($event->getData(), $this->sourcefield);

        // make sure the listener works with array
        $data = $event->getData();

        $accessor->setValue($data, $this->targetField, $this->pool->transform($format, $source));

        $event->setData($data);
    }
}