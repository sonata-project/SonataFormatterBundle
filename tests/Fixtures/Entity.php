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
    /**
     * @var string|int|null
     */
    private $id;

    /**
     * @param int|string $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }
}
