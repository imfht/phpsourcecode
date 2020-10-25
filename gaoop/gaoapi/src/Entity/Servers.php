<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Servers
 *
 * @ORM\Table(name="servers")
 * @ORM\Entity
 */
class Servers
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="info_id", type="integer", nullable=false, options={"comment"="关联元数据表ID"})
     */
    private $infoId;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false, options={"comment"="url链接"})
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true, options={"comment"="描述"})
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getInfoId(): ?int
    {
        return $this->infoId;
    }

    public function setInfoId(int $infoId): self
    {
        $this->infoId = $infoId;

        return $this;
    }


}
