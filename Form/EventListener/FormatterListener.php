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
use Symfony\Component\Form\Event\FilterDataEvent;
use Symfony\Component\Form\Util\PropertyPath;

class FormatterListener
{
    protected $pool;

    protected $sourceProperty;

    protected $targetProperty;

    /**
     * @param \Sonata\FormatterBundle\Formatter\Pool $pool
     * @param $sourceProperty
     * @param $targetProperty
     */
    public function __construct(Pool $pool, $sourceProperty, $targetProperty)
    {
        $this->pool = $pool;
        $this->sourceProperty = $sourceProperty;
        $this->targetProperty = $targetProperty;
    }

    /**
     * @param \Symfony\Component\Form\Event\FilterDataEvent $event
     * @return void
     */
    public function postBind(FilterDataEvent $event)
    {
        $targetPropertyPath = new PropertyPath($this->targetProperty);

        $sourceField = $event->getForm()->getParent()->get($this->sourceProperty);
        $object = $event->getForm()->getParent()->getData();

        // transform the value
        $targetPropertyPath->setValue(
            $object,
            $this->pool->transform($event->getData(), $sourceField->getData())
        );
    }
}