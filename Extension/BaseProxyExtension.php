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

use Twig_Environment;

abstract class BaseProxyExtension implements \Twig_ExtensionInterface, ExtensionInterface
{
    /**
     * @return \Twig_ExtensionInterface
     */
    abstract public function getTwigExtension();

    /**
     * {@inheritdoc}
     */
    public function getAllowedFilters()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedTags()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedFunctions()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedProperties()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedMethods()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Twig_Environment $environment)
    {
        $this->getTwigExtension()->initRuntime($environment);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return $this->getTwigExtension()->getTokenParsers();
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return $this->getTwigExtension()->getNodeVisitors();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->getTwigExtension()->getFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return $this->getTwigExtension()->getTests();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return $this->getTwigExtension()->getFunctions();
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return $this->getTwigExtension()->getOperators();
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return $this->getTwigExtension()->getGlobals();
    }
}
