<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Formatter extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The formatter is not valid';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sonata.formatter.validator.formatter';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
