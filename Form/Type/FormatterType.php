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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

use Sonata\FormatterBundle\Form\EventListener\FormatterListener;
use Sonata\FormatterBundle\Formatter\Pool;

class FormatterType extends ChoiceType
{
    /** @var \Sonata\FormatterBundle\Formatter\Pool */
    protected $pool;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
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
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        /**
         * The listener option only work if the source field is after the current field
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $pool = $this->pool;
        $translator = $this->translator;

        $resolver->setDefaults(array(
            'multiple'          => false,
            'expanded'          => false,
            'choice_list'       => null,
            'preferred_choices' => array(),

            'choices'           => function (Options $options, $previousValue) use ($pool, $translator)
            {
                if ($previousValue) {
                    return $previousValue;
                }

                $choices = array();

                foreach($pool->getFormatters() as $code => $instance) {
                    $choices[$code] = $translator->trans($code, array(), 'SonataFormatterBundle');
                }

                return $choices;
            },

            'empty_data'        => function (Options $options)
            {
                return isset($options['multiple']) && $options['multiple']
                    || isset($options['expanded']) && $options['expanded']
                     ? array() : '';
            },

            'empty_value'       => function (Options $options)
            {
                return isset($options['multiple']) && $options['multiple']
                    || isset($options['expanded']) && $options['expanded']
                    ? null : '';
            },

            'error_bubbling'    => false,

            // field names
            'source'            => null,
            'target'            => null,

            'listener'          => function (Options $options, $previousValue)
            {
                if ($previousValue) {
                    if (!$options['source']) {
                        throw new \RuntimeException('Please provide a source property name');
                    }

                    if (!$options['target']) {
                        throw new \RuntimeException('Please provide a target property name');
                    }
                }
            }
        ));
    }
}