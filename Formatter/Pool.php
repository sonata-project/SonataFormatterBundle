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

use \Twig_Environment;
use \Twig_Error_Syntax;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Pool
{
    protected $formatters = array();

    protected $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param $code
     * @param FormatterInterface $formatter
     * @param \Twig_Environment $env
     * @return void
     */
    public function add($code, FormatterInterface $formatter, Twig_Environment $env = null)
    {
        $this->formatters[$code] = array($formatter, $env);
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
     * @return array
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
        list($formatter, $env) = $this->get($code);

        try {
            // apply custom extension
            if ($env) {
                $text = $env->render($text);
            }
        } catch (Twig_Error_Syntax $e) {
            if ($this->logger) {
                $this->logger->crit(sprintf('[FormatterBundle::transform] Error while parsing twig template : %s, %s', $code, $e->getMessage()), array(
                    'text' => $text
                ));
            }
        }

        // apply the selected formatter
        $text = $formatter->transform($text);

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