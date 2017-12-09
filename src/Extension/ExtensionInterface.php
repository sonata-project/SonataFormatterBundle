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
     * @return array
     */
    public function getAllowedFilters();

    /**
     * @return array
     */
    public function getAllowedTags();

    /**
     * @return array
     */
    public function getAllowedFunctions();

    /**
     * @return array
     */
    public function getAllowedProperties();

    /**
     * @return array
     */
    public function getAllowedMethods();
}
