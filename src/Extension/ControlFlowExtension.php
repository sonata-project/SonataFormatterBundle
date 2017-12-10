<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
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
     * {@inheritdoc}
     */
    public function getAllowedFilters()
    {
        return [
            'escape',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedTags()
    {
        return [
            'if',
            'for',
        ];
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
    public function getName()
    {
        return 'sonata_formatter_extension_flow';
    }
}
