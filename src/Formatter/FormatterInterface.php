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

@trigger_error(
    'The '.__NAMESPACE__.'\FormatterInterface interface is deprecated since sonata-project/formatter-bundle 4.x, to be removed in 5.0. '.
    'Use Formatter or ExtendableFormatter instead.',
    E_USER_DEPRECATED
);

/**
 * @deprecated since sonata-project/formatter-bundle 4.x, to be removed in 5.0.
 */
interface FormatterInterface extends ExtendableFormatter
{
}
