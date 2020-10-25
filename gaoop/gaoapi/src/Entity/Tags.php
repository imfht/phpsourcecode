<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints as MyAssert;

/**
 * Tags
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="App\Repository\TagsRepository")
 * @MyAssert\ContainsTags
 */
class Tags
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false, options={"comment"="名称"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false, options={"comment"="描述"})
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="doc_description", type="string", length=255, nullable=true, options={"comment"="外部文档描述"})
     */
    private $docDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="doc_url", type="string", length=255, nullable=true, options={"comment"="外部文档链接"})
     */
    private $docUrl;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDocDescription(): ?string
    {
        return $this->docDescription;
    }

    public function setDocDescription(?string $docDescription): self
    {
        $this->docDescription = $docDescription;

        return $this;
    }

    public function getDocUrl(): ?string
    {
        return $this->docUrl;
    }

    public function setDocUrl(?string $docUrl): self
    {
        $this->docUrl = $docUrl;

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
