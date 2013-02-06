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

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

use Sonata\FormatterBundle\Form\EventListener\FormatterListener;

use Sonata\FormatterBundle\Formatter\Pool;

class FormatterType extends AbstractType
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * The listener option only work if the source field is after the current field
         */
        if ($options['listener']) {
            $listener = new FormatterListener(
                $this->pool,
                $options['source'],
                $options['target'],
                $builder->getName()
            );

            $builder->getParent()->addEventListener(FormEvents::BIND, array($listener, 'postBind'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->set('source_id', str_replace($view->get('name'), $options['source'], $view->get('id')));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $pool = $this->pool;
        $translator = $this->translator;

        $resolver->setDefaults(array(
            'source' => function (Options $options, $previousValue) {
                if ($options['listener'] && !$previousValue) {
                    throw new \RuntimeException('Please provide a source property name');
                }
                return null;
            },
            'target' => function (Options $options, $previousValue) {
                if ($options['listener'] && !$previousValue) {
                    throw new \RuntimeException('Please provide a target property name');
                }
                return null;
            },
            'listener' => false,
            'choices' => function (Options $options) use ($pool, $translator) {
                $formatters = array();
                foreach($pool->getFormatters() as $code => $instance) {
                    $formatters[$code] = $translator->trans($code, array(), 'SonataFormatterBundle');
                }
                return $formatters;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_formatter_type_selector';
    }
}
