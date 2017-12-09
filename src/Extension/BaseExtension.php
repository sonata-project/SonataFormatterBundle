<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;

abstract class BaseExtension extends AbstractExtension implements ExtensionInterface
{
    public function getAllowedFilters()
    {
        return [];
    }

    public function getAllowedTags()
    {
        return [];
    }

    public function getAllowedFunctions()
    {
        return [];
    }

    public function getAllowedProperties()
    {
        return [];
    }

    public function getAllowedMethods()
    {
        return [];
    }

    public function initRuntime(Environment $environment)
    {
        return [];
    }

    public function getTokenParsers()
    {
        return [];
    }

    public function getNodeVisitors()
    {
        return [];
    }

    public function getFilters()
    {
        return [];
    }

    public function getTests()
    {
        return [];
    }

    public function getFunctions()
    {
        return [];
    }

    public function getOperators()
    {
        return [];
    }

    public function getGlobals()
    {
        return [];
    }
}
