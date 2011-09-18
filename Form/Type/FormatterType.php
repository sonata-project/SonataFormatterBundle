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
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        $multiple = isset($options['multiple']) && $options['multiple'];
        $expanded = isset($options['expanded']) && $options['expanded'];

        $options = array(
            'multiple'          => false,
            'expanded'          => false,
            'choice_list'       => null,
            'choices'           => null,
            'preferred_choices' => array(),
            'empty_data'        => $multiple || $expanded ? array() : '',
            'empty_value'       => $multiple || $expanded || !isset($options['empty_value']) ? null : '',
            'error_bubbling'    => false,
        );

        if (!$options['choices']) {
            $options['choices'] = array();
            foreach($this->pool->getFormatters() as $code => $instance) {
                $options['choices'][] = $this->translator->trans($code, array(), 'SonataFormatterBundle');
            }
        }

        return $options;
    }
}