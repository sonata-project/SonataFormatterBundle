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

abstract class BaseProxyExtension extends AbstractExtension implements ExtensionInterface
{
    /**
     * @return \Twig_ExtensionInterface
     */
    abstract public function getTwigExtension();

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
        $this->getTwigExtension()->initRuntime($environment);
    }

    public function getTokenParsers()
    {
        return $this->getTwigExtension()->getTokenParsers();
    }

    public function getNodeVisitors()
    {
        return $this->getTwigExtension()->getNodeVisitors();
    }

    public function getFilters()
    {
        return $this->getTwigExtension()->getFilters();
    }

    public function getTests()
    {
        return $this->getTwigExtension()->getTests();
    }

    public function getFunctions()
    {
        return $this->getTwigExtension()->getFunctions();
    }

    public function getOperators()
    {
        return $this->getTwigExtension()->getOperators();
    }

    public function getGlobals()
    {
        return $this->getTwigExtension()->getGlobals();
    }
}
