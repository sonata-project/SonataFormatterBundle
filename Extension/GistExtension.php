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

use Sonata\FormatterBundle\Twig\TokenParser\GistTokenParser;

class GistExtension extends BaseExtension
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
        return array(
            'gist',
        );
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
    public function getTokenParsers()
    {
        return array(
            new GistTokenParser(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_formatter_extension_gist';
    }
}
