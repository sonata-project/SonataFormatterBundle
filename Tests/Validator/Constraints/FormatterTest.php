<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Validator\Constraints;


use Sonata\FormatterBundle\Validator\Constraints\Formatter;

class FormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testConstraint()
    {
        $constraint = new Formatter();

        $this->assertEquals('property', $constraint->getTargets());
        $this->assertEquals('sonata.formatter.validator.formatter', $constraint->validatedBy());
    }
}
 