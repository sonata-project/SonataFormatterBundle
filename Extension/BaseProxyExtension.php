<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Extension;

use \Twig_Environment;

abstract class BaseProxyExtension implements \Twig_ExtensionInterface, ExtensionInterface
{
    /**
     * @abstract
     * @return \Twig_ExtensionInterface
     */
    abstract public function getTwigExtension();

    /**
     * Returns an array of available filters
     *
     * @return array
     */
    public function getAllowedFilters()
    {
        return array();
    }

    /**
     * Returns an array of available tags
     *
     * @return array
     */
    public function getAllowedTags()
    {
        return array();
    }

    /**
     * Returns an array of available functions
     *
     * @return array
     */
    public function getAllowedFunctions()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getAllowedProperties()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return array();
    }

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(Twig_Environment $environment)
    {
        $this->getTwigExtension()->initRuntime($environment);
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return $this->getTwigExtension()->getTokenParsers();
    }

    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return array An array of Twig_NodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        return $this->getTwigExtension()->getNodeVisitors();
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return $this->getTwigExtension()->getFilters();
    }

    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return array An array of tests
     */
    public function getTests()
    {
        return $this->getTwigExtension()->getTests();
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return $this->getTwigExtension()->getFunctions();
    }

    /**
     * Returns a list of operators to add to the existing list.
     *
     * @return array An array of operators
     */
    public function getOperators()
    {
        return $this->getTwigExtension()->getOperators();
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getGlobals()
    {
        return $this->getTwigExtension()->getGlobals();
    }
}
