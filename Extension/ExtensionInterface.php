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

interface ExtensionInterface
{
    /**
     * Returns an array of available filters.
     *
     * @abstract
     *
     * @return array
     */
    public function getAllowedFilters();

    /**
     * Returns an array of available tags.
     *
     * @abstract
     *
     * @return array
     */
    public function getAllowedTags();

    /**
     * Returns an array of available functions.
     *
     * @abstract
     *
     * @return array
     */
    public function getAllowedFunctions();

    /**
     * @abstract
     *
     * @return array
     */
    public function getAllowedProperties();

    /**
     * @abstract
     *
     * @return array
     */
    public function getAllowedMethods();
}
