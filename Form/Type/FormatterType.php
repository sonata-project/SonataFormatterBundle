<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Form\Type;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;

use Sonata\FormatterBundle\Form\EventListener\FormatterListener;

use Sonata\FormatterBundle\Formatter\Pool;

class FormatterType extends ChoiceType
{
    protected $pool;

    protected $translator;

    /**
     * @param \Sonata\FormatterBundle\Formatter\Pool $pool
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(Pool $pool, TranslatorInterface $translator)
    {
        $this->pool = $pool;
        $this->translator = $translator;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilder $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        /**
         * The listener option only work if the source field is after the current field
         *
         */
        if ($options['listener']) {
            $listener = new FormatterListener(
                $this->pool,
                $options['source'],
                $options['target']
            );

            $builder->addEventListener(FormEvents::BIND_CLIENT_DATA, array($listener, 'postBind'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        $multiple = isset($options['multiple']) && $options['multiple'];
        $expanded = isset($options['expanded']) && $options['expanded'];

        $defaultOptions = array(
            'multiple'          => false,
            'expanded'          => false,
            'choice_list'       => null,
            'choices'           => null,
            'preferred_choices' => array(),
            'empty_data'        => $multiple || $expanded ? array() : '',
            'empty_value'       => $multiple || $expanded || !isset($options['empty_value']) ? null : '',
            'error_bubbling'    => false,

            // field names
            'source'            => null,
            'target'            => null,
            'listener'          => false,
        );

        $options = array_replace($defaultOptions, $options);

        if (!$options['choices']) {
            $options['choices'] = array();
            foreach($this->pool->getFormatters() as $code => $instance) {
                $options['choices'][$code] = $this->translator->trans($code, array(), 'SonataFormatterBundle');
            }
        }

        if ($options['listener']) {
            if (!$options['source']) {
                throw new \RuntimeException('Please provide a source property name');
            }

            if (!$options['target']) {
                throw new \RuntimeException('Please provide a target property name');
            }
        }

        return $options;
    }
}