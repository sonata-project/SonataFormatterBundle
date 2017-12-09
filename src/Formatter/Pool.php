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
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Sandbox\SecurityError;

class Pool implements LoggerAwareInterface
{
    /**
     * @var array
     */
    protected $formatters = [];

    /**
     * @var string
     */
    protected $defaultFormatter;

    /**
     * NEXT_MAJOR: use LoggerAwareTrait.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param string|null $defaultFormatter
     */
    public function __construct($defaultFormatter = null)
    {
        if (2 == func_num_args()) {
            list($logger, $defaultFormatter) = func_get_args();
            $this->logger = $logger;
            $this->defaultFormatter = $defaultFormatter;
        } elseif (1 == func_num_args()) {
            if ($defaultFormatter instanceof LoggerInterface) {
                $this->logger = $defaultFormatter;
            } else {
                // NEXT_MAJOR: Only keep this block
                $this->defaultFormatter = $defaultFormatter;
            }
        }

        // NEXT_MAJOR: keep the else block only
        if ($this->logger) {
            @trigger_error(sprintf(
                'Providing a logger to %s through the constructor is deprecated since 3.2'.
                ' and will no longer be possible in 4.0'.
                ' This argument should be provided through the setLogger() method.',
                __CLASS__
            ), E_USER_DEPRECATED);
        } else {
            $this->logger = new NullLogger();
        }

        // NEXT_MAJOR: make defaultFormatter required
        if (null === $this->defaultFormatter) {
            @trigger_error(sprintf(
                'Not providing the defaultFormatter argument to %s is deprecated since 3.2.'.
                ' This argument will become mandatory in 4.0.',
                __METHOD__
            ), E_USER_DEPRECATED);
        }
    }

    /**
     * NEXT_MAJOR: use Psr\Log\LoggerAwareTrait.
     *
     * @param LoggerInterface will be used to report errors
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $code
     */
    public function add($code, FormatterInterface $formatter, Environment $env = null)
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
        } catch (SyntaxError $e) {
            $this->logger->critical(sprintf(
                '[FormatterBundle::transform] %s - Error while parsing twig template : %s',
                $code,
                $e->getMessage()
            ), [
                'text' => $text,
                'exception' => $e,
            ]);
        } catch (SecurityError $e) {
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
        // NEXT_MAJOR: This should be removed
        if (null === $this->defaultFormatter) {
            reset($this->formatters);

            $this->defaultFormatter = key($this->formatters);
        }

        return $this->defaultFormatter;
    }
}
