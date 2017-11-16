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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Twig_Environment;
use Twig_Error_Syntax;
use Twig_Sandbox_SecurityError;

class Pool implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    protected $formatters = [];

    /**
     * @var string
     */
    protected $defaultFormatter;

    /**
     * @param string $defaultFormatter
     */
    public function __construct($defaultFormatter)
    {
        $this->defaultFormatter = $defaultFormatter;
        $this->logger = new NullLogger();
    }

    /**
     * @param string                 $code
     * @param FormatterInterface     $formatter
     * @param \Twig_Environment|null $env
     */
    public function add($code, FormatterInterface $formatter, Twig_Environment $env = null)
    {
        $this->formatters[$code] = [$formatter, $env];
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
                // NEXT_MAJOR: remove this if block
                if (class_exists('\Twig_Loader_Array')) {
                    $template = $env->createTemplate($text ?: '');
                    $text = $template->render([]);
                } else {
                    $text = $env->render($text);
                }
            }
        } catch (Twig_Error_Syntax $e) {
            $this->logger->critical(sprintf(
                '[FormatterBundle::transform] %s - Error while parsing twig template : %s',
                $code,
                $e->getMessage()
            ), [
                'text' => $text,
                'exception' => $e,
            ]);
        } catch (Twig_Sandbox_SecurityError $e) {
            $this->logger->critical(sprintf(
                '[FormatterBundle::transform] %s - the user try an non white-listed keyword : %s',
                $code,
                $e->getMessage()
            ), [
                'text' => $text,
                'exception' => $e,
            ]);
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
        return $this->defaultFormatter;
    }
}
