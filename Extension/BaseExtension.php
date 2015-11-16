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

abstract class BaseExtension implements \Twig_ExtensionInterface, ExtensionInterface
{
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
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return array();
    }
}
