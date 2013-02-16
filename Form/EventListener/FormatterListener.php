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
use Symfony\Component\Form\Util\PropertyPath;

class FormatterListener
{
    protected $pool;

    protected $sourceProperty;

    protected $targetProperty;

    protected $format;

    /**
     * @param Pool   $pool
     * @param string $source
     * @param string $target
     * @param string $format
     */
    public function __construct(Pool $pool, $source, $target, $format)
    {
        $this->pool = $pool;
        $this->source = $source;
        $this->target = $target;
        $this->format = $format;
    }

    /**
     * @param FormEvent $event
     */
    public function postBind(FormEvent $event)
    {
        $target = new PropertyPath($this->target);

        $format = $event->getForm()->get($this->format)->getData();
        $source = $event->getForm()->get($this->source)->getData();
        $object = $event->getForm()->getData();

        // transform the value
        $target->setValue($object, $this->pool->transform($format, $source));

        $event->setData($object);
    }
}
