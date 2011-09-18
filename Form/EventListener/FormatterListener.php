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
use Symfony\Component\Form\Event\DataEvent;
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
     * @param \Symfony\Component\Form\Event\DataEvent $event
     * @return void
     */
    public function postBind(DataEvent $event)
    {
        $sourcePropertyPath = new PropertyPath($this->sourceProperty);
        $targetPropertyPath = new PropertyPath($this->targetProperty);

        $data = $event->getForm()->getParent()->getData();

        // transform the value
        $targetPropertyPath->setValue(
            $data,
            $this->pool->transform($event->getData(), $sourcePropertyPath->getValue($data))
        );
    }
}