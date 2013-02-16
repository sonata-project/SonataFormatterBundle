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

use Sonata\FormatterBundle\Twig\TokenParser\GistTokenParser;

class GistExtension extends BaseExtension
{
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
        return array(
            'gist'
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
     * @return array
     */
    public function getTokenParsers()
    {
        return array(
            new GistTokenParser()
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'sonata_formatter_extension_gist';
    }
}
