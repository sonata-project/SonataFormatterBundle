<?php
/*
 * This file is part of sonata-project.
 *
 * (c) 2010 Thomas Rabaix
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\FormatterBundle\Tests\Validator\Constraints;

use Sonata\FormatterBundle\Validator\Constraints\FormatterValidator;

class FormatterValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
        $pool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');

        $validator = new FormatterValidator($pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);
    }

    public function testInvalidCase()
    {
        $pool = $this->getMock('Sonata\FormatterBundle\Formatter\Pool');
        $pool->expects($this->any())
            ->method('has')
            ->will($this->returnValue(false));

        $message = 'Constraint message';
        $constraint = $this->getMock('Sonata\FormatterBundle\Validator\Constraints\Formatter');
        $constraint->message = $message;

        $context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $context->expects($this->once())
            ->method('addViolation')
            ->with($message);

        $validator = new FormatterValidator($pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);

        $validator->initialize($context);

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

        $context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $context->expects($this->never())
            ->method('addViolation');

        $validator = new FormatterValidator($pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);

        $validator->initialize($context);

        $validator->validate('existingFormatter', $constraint);
    }
}
 