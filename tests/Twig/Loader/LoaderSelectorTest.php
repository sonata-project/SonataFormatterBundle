<?php

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
use Sonata\FormatterBundle\Twig\Loader\LoaderSelector;
use Twig\Loader\LoaderInterface;

class LoaderSelectorTest extends TestCase
{
    public function testCanBeInstanciated()
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
