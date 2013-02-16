<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Formatter;

class RawFormatter extends BaseFormatter
{
    /**
     * @param $text
     * @return string
     */
    public function transform($text)
    {
        return $text;
    }
}
