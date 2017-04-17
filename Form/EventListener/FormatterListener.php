<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * (c) Dmitry Vapelnik <dvapelnik@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Form\EventListener;

use Sonata\FormatterBundle\Formatter\Pool;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class FormatterListener
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var string
     */
    protected $formatField;

    /**
     * @var string
     */
    protected $sourceField;

    /**
     * @var string
     */
    protected $targetField;

    /**
     * @param Pool   $pool
     * @param string $formatField
     * @param string $sourceField
     * @param string $targetField
     */
    public function __construct(Pool $pool, $formatField, $sourceField, $targetField)
    {
        $this->pool = $pool;

        $this->formatField = $formatField;
        $this->sourceField = $sourceField;
        $this->targetField = $targetField;
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        // make sure the listener works with array
        $data = $event->getData();
        $accessor = PropertyAccess::createPropertyAccessor();

        try {
            $this->transformField($data, $accessor);
        } catch (NoSuchPropertyException $e) {
            $translations = $accessor->getValue($data, 'translations');

            foreach ($translations as $translation) {
                $this->transformField($translation, $accessor);
            }
        }

        $event->setData($data);
    }

    /**
     * @param                  $data
     * @param PropertyAccessor $accessor
     */
    private function transformField(&$data, $accessor)
    {
        $format = $accessor->getValue($data, $this->formatField);
        $source = $accessor->getValue($data, $this->sourceField);

        $accessor->setValue($data, $this->targetField, $this->pool->transform($format, $source));
    }
}
