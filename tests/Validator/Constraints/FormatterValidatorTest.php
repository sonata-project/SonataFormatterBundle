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
use Sonata\FormatterBundle\Formatter\FormatterInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Validator\Constraints\Formatter;
use Sonata\FormatterBundle\Validator\Constraints\FormatterValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FormatterValidatorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var Formatter
     */
    private $constraint;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->pool = new Pool('');
        $this->constraint = new Formatter();
    }

    public function testValidator(): void
    {
        $validator = new FormatterValidator($this->pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);
    }

    /**
     * @group legacy
     */
    public function testInvalidCase(): void
    {
        $this->constraint->message = $message = 'Constraint message';

        $this->context->expects($this->once())
            ->method('addViolation')
            ->with($message);

        $validator = new FormatterValidator($this->pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);

        $validator->initialize($this->context);

        $validator->validate('existingFormatter', $this->constraint);
    }

    public function testValidCase(): void
    {
        $this->pool->add('existingFormatter', $this->createMock(FormatterInterface::class));

        $this->constraint->message = $message = 'Constraint message';

        $this->context->expects($this->never())
            ->method('addViolation');

        $validator = new FormatterValidator($this->pool);
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $validator);

        $validator->initialize($this->context);

        $validator->validate('existingFormatter', $this->constraint);
    }
}
