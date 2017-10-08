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

abstract class BaseExtension extends \Twig_Extension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllowedFilters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedTags()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedFunctions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedProperties()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedMethods()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Twig_Environment $environment)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTests()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return [];
    }
}
