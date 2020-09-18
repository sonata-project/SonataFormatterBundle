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

namespace Sonata\FormatterBundle\Tests\Twig\Loader;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sonata\FormatterBundle\Twig\Loader\LoaderSelector;
use Twig\Loader\LoaderInterface;

class LoaderSelectorTest extends TestCase
{
    use ProphecyTrait;

    public function testCanBeInstanciated(): void
    {
        $loaderSelector = new LoaderSelector(
            $this->prophesize(LoaderInterface::class)->reveal(),
            $this->prophesize(LoaderInterface::class)->reveal()
        );
        $this->assertInstanceOf(
            LoaderSelector::class,
            $loaderSelector
        );
    }
}
