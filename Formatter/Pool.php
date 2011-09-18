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

class Pool
{
    protected $formatters = array();

    /**
     * @param $code
     * @param \Sonata\FormatterBundle\Formatter\FormatterInterface $formatter
     * @return void
     */
    public function add($code, FormatterInterface $formatter)
    {
        $this->formatters[$code] = $formatter;
    }

    /**
     * @param $code
     * @return bool
     */
    public function has($code)
    {
        return isset($this->formatters[$code]);
    }

    /**
     * @param $code
     * @return null|\Sonata\FormatterBundle\Formatter\FormatterInterface
     */
    public function get($code)
    {
        if (!$this->has($code)) {
            throw new \RuntimeException(sprintf('Unable to get the formatter : %s', $code));
        }

        return $this->formatters[$code];
    }

    /**
     * @param $code
     * @param $text
     * @return string
     */
    public function transform($code, $text)
    {
        $text = $this->get($code)->transform($text);

        foreach($this->get($code)->getExtensions() as $extension) {
            $text = $extension->transform($text);
        }

        return $text;
    }

    /**
     * @return array
     */
    public function getFormatters()
    {
        return $this->formatters;
    }
}