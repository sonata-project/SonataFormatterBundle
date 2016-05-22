<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Formatter;

use Psr\Log\LoggerInterface;
use Twig_Environment;
use Twig_Error_Syntax;
use Twig_Sandbox_SecurityError;

class Pool
{
    /**
     * @var array
     */
    protected $formatters = array();

    /**
     * @var string
     */
    protected $defaultFormatter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface|null $logger
     * @param string|null          $defaultFormatter
     */
    public function __construct(LoggerInterface $logger = null, $defaultFormatter = null)
    {
        $this->logger = $logger;

        // TODO: This should become a required first parameter when the major version is changed
        $this->defaultFormatter = $defaultFormatter;
    }

    /**
     * @param string                 $code
     * @param FormatterInterface     $formatter
     * @param \Twig_Environment|null $env
     */
    public function add($code, FormatterInterface $formatter, Twig_Environment $env = null)
    {
        $this->formatters[$code] = array($formatter, $env);
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function has($code)
    {
        return isset($this->formatters[$code]);
    }

    /**
     * @param string $code
     *
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
     * @param string $code
     * @param string $text
     *
     * @return string
     */
    public function transform($code, $text)
    {
        list($formatter, $env) = $this->get($code);

        // apply the selected formatter
        $text = $formatter->transform($text);

        try {
            // apply custom extension
            if ($env) {
                $text = $env->render($text);
            }
        } catch (Twig_Error_Syntax $e) {
            if ($this->logger) {
                $this->logger->critical(sprintf('[FormatterBundle::transform] %s - Error while parsing twig template : %s', $code, $e->getMessage()), array(
                    'text' => $text,
                ));
            }
        } catch (Twig_Sandbox_SecurityError $e) {
            if ($this->logger) {
                $this->logger->critical(sprintf('[FormatterBundle::transform] %s - the user try an non white-listed keyword : %s', $code, $e->getMessage()), array(
                    'text' => $text,
                ));
            }
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

    /**
     * @return string
     */
    public function getDefaultFormatter()
    {
        // TODO: This should be removed when the major version is changed
        if (is_null($this->defaultFormatter)) {
            reset($this->formatters);

            $this->defaultFormatter = key($this->formatters);
        }

        return $this->defaultFormatter;
    }
}
