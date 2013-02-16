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

class ControlFlowExtension extends BaseExtension
{
    /**
     * Returns an array of available filters
     *
     * @return array
     */
    public function getAllowedFilters()
    {
        return array(
            'escape'
        );
    }

    /**
     * Returns an array of available tags
     *
     * @return array
     */
    public function getAllowedTags()
    {
        return array(
            'if',
            'for'
        );
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
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'sonata_formatter_extension_flow';
    }
}
