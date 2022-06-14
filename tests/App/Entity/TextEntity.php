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

namespace Sonata\FormatterBundle\Tests\App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TextEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $simpleText = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $textFormat = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $rawText = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $text = null;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setSimpleText(?string $simpleText = null): void
    {
        $this->simpleText = $simpleText;
    }

    public function getSimpleText(): ?string
    {
        return $this->simpleText;
    }

    public function setTextFormat(?string $textFormat = null): void
    {
        $this->textFormat = $textFormat;
    }

    public function gettextFormat(): ?string
    {
        return $this->textFormat;
    }

    public function setRawText(?string $rawText = null): void
    {
        $this->rawText = $rawText;
    }

    public function getRawText(): ?string
    {
        return $this->rawText;
    }

    public function setText(?string $text = null): void
    {
        $this->text = $text;
    }

    public function getText(): ?string
    {
        return $this->text;
    }
}
