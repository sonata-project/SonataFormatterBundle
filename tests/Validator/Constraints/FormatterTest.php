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

namespace Sonata\FormatterBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Validator\Constraints\Formatter;

class FormatterTest extends TestCase
{
    public function testConstraint(): void
    {
        $constraint = new Formatter();

        static::assertSame('property', $constraint->getTargets());
        static::assertSame('sonata.formatter.validator.formatter', $constraint->validatedBy());
    }
}
