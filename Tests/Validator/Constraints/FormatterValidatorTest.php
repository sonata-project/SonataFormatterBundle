<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Validator\Constraints;

use Sonata\FormatterBundle\Validator\Constraints\FormatterValidator;

class FormatterValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    protected function setUp()
    {
        $this->context = $this->getMock(interface_exists('Symfony\Component\Validator\Context\ExecutionContextInterface') ? 'Symfony\Component\Validator\Context\ExecutionContextInterface' : 'Symfony\Component\Validator\ExecutionContextInterface');
    }

    public function testValidator()
    {
        $pool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');

        $validator = new FormatterValidator($pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);
    }

    /**
     * @group legacy
     */
    public function testInvalidCase()
    {
        $pool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');
        $pool->expects($this->any())
            ->method('has')
            ->will($this->returnValue(false));

        $message = 'Constraint message';
        $constraint = $this->getMock('Sonata\FormatterBundle\Validator\Constraints\Formatter');
        $constraint->message = $message;

        $this->context->expects($this->once())
            ->method('addViolation')
            ->with($message);

        $validator = new FormatterValidator($pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);

        $validator->initialize($this->context);

        $validator->validate('existingFormatter', $constraint);
    }

    public function testValidCase()
    {
        $pool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');
        $pool->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));

        $message = 'Constraint message';
        $constraint = $this->getMock('Sonata\FormatterBundle\Validator\Constraints\Formatter');
        $constraint->message = $message;

        $this->context->expects($this->never())
            ->method('addViolation');

        $validator = new FormatterValidator($pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);

        $validator->initialize($this->context);

        $validator->validate('existingFormatter', $constraint);
    }
}
