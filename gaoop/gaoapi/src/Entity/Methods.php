<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Methods
 *
 * @ORM\Table(name="methods")
 * @ORM\Entity(repositoryClass="App\Repository\MethodsRepository")
 */
class Methods
{
    const GET = 1;
    const POST = 2;
    const PUT = 3;
    const DELETE = 4;

    public static $methods = [
        self::GET => 'GET',
        self::POST => 'POST',
        self::PUT => 'PUT',
        self::DELETE => 'DELETE',
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function __toString()
    {
        return $this->getValue();
    }

}
