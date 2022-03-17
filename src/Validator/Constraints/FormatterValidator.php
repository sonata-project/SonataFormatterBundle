<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Validator\Constraints;

use Sonata\FormatterBundle\Formatter\PoolInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
final class FormatterValidator extends ConstraintValidator
{
    /**
     * @var PoolInterface
     */
    protected $pool;

    public function __construct(PoolInterface $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Formatter) {
            throw new UnexpectedTypeException($constraint, Formatter::class);
        }

        if (!$this->pool->has($value)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
