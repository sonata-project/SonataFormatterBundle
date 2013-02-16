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

use Sonata\FormatterBundle\Extension\ExtensionInterface;

interface FormatterInterface
{
    /**
     * @abstract
     * @param $text
     * @return void
     */
    public function transform($text);

    /**
     * @abstract
     * @param  \Sonata\FormatterBundle\Extension\ExtensionInterface $extensionInterface
     * @return void
     */
    public function addExtension(ExtensionInterface $extensionInterface);

    /**
     * @abstract
     * @return array
     */
    public function getExtensions();
}
