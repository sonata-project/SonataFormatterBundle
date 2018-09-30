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

namespace Sonata\FormatterBundle\Formatter;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Sandbox\SecurityError;

final class Pool implements LoggerAwareInterface, PoolInterface
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

    public function __construct(string $defaultFormatter)
    {
        $this->defaultFormatter = $defaultFormatter;
        $this->logger = new NullLogger();
    }

    public function add(string $code, FormatterInterface $formatter, Environment $env = null): void
    {
        $this->formatters[$code] = [$formatter, $env];
    }

    public function has(string $code): bool
    {
        return isset($this->formatters[$code]);
    }

    public function get(string $code): array
    {
        if (!$this->has($code)) {
            throw new \RuntimeException(sprintf('Unable to get the formatter : %s', $code));
        }

        return $this->formatters[$code];
    }

    public function transform(string $code, string $text): string
    {
        list($formatter, $env) = $this->get($code);

        // apply the selected formatter
        $text = $formatter->transform($text);

        try {
            // apply custom extension
            if ($env) {
                $template = $env->createTemplate($text ?: '');
                $text = $template->render([]);
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

    public function getFormatters(): array
    {
        return $this->formatters;
    }

    public function getDefaultFormatter(): string
    {
        return $this->defaultFormatter;
    }
}
