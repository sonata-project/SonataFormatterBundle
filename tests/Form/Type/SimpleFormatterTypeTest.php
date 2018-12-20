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

namespace Sonata\FormatterBundle\Tests\Form\Type;

use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\FormBuilderInterface;

class SimpleFormatterTypeTest extends TestCase
{
    /**
     * @var SimpleFormatterType
     */
    private $formType;

    public function setUp(): void
    {
        parent::setUp();

        $this->formType = new SimpleFormatterType();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testBuildForm(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);

        $options = ['format' => 'format'];

        $this->formType->buildForm($formBuilder, $options);
    }
}
