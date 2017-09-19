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

use Sonata\FormatterBundle\Twig\Loader\LoaderSelector;

class LoaderSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInstanciated()
    {
        $loaderSelector = new LoaderSelector(
            $this->prophesize('\Twig_LoaderInterface')->reveal(),
            $this->prophesize('\Twig_LoaderInterface')->reveal()
        );
        $this->assertInstanceOf(
            'Sonata\FormatterBundle\Twig\Loader\LoaderSelector',
            $loaderSelector
        );
    }
}
