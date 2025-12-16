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

namespace Sonata\FormatterBundle\Tests\Fixtures;

class Entity
{
    private int|string|null $id = null;

    public function setId(int|string $id): void
    {
        $this->id = $id;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }
}
