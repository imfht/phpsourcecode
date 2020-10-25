<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Info
 *
 * @ORM\Table(name="info")
 * @ORM\Entity(repositoryClass="App\Repository\InfoRepository")
 * @UniqueEntity("tag")
 * @Vich\Uploadable()
 */
class Info
{
    const REDIS_CURRENT_INFO_KEY = 'info:current:object';
    const REDIS_DOCUMENT_PATH_PREFIX_KEY = 'path';
    const REDIS_DOCUMENT_MENU_PREFIX_KEY = 'menu';

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
     * @ORM\Column(name="title", type="string", length=255, nullable=false, options={"comment"="应用名称"})
     */
    private $title;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $logo;

    /**
     * @Vich\UploadableField(mapping="info_logo", fileNameProperty="logo.name", size="logo.size", mimeType="logo.mimeType", originalName="logo.originalName", dimensions="logo.dimensions")
     *
     * @var File|null
     */
    private $logo_file;

    /**
     * @var string|null
     *
     * @ORM\Column(name="version", type="string", length=255, nullable=true, options={"default"="1.0.0", "comment"="版本号"})
     */
    private $version = '1.0.0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="tag", type="string", length=255, nullable=false, options={"comment"="标签标示"})
     */
    private $tag;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_current", type="boolean", nullable=true, options={"default"="0","comment"="当前编辑使用的info"})
     */
    private $isCurrent = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_auto_update", type="boolean", nullable=true, options={"default"="0","comment"="是否自动更新文档"})
     */
    private $isAutoUpdate = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_show_doc", type="boolean", nullable=true, options={"default"="1","comment"="是否展开文档"})
     */
    private $isShowDoc = '1';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true, options={"comment"="应用描述"})
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

    public function __construct()
    {
        $this->logo = new EmbeddedFile();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

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

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getIsCurrent(): ?bool
    {
        return $this->isCurrent;
    }

    public function setIsCurrent(?bool $isCurrent): self
    {
        $this->isCurrent = $isCurrent;

        return $this;
    }

    public function getIsAutoUpdate(): ?bool
    {
        return $this->isAutoUpdate;
    }

    public function setIsAutoUpdate(?bool $isAutoUpdate): self
    {
        $this->isAutoUpdate = $isAutoUpdate;

        return $this;
    }

    public function getIsShowDoc(): ?bool
    {
        return $this->isShowDoc;
    }

    public function setIsShowDoc(?bool $isShowDoc): self
    {
        $this->isShowDoc = $isShowDoc;

        return $this;
    }

    /**
     * @param File|UploadedFile|null $logoFile
     * @throws \Exception
     */
    public function setLogoFile(?File $logoFile = null)
    {
        $this->logo_file = $logoFile;

        if (null !== $logoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getLogoFile(): ?File
    {
        return $this->logo_file;
    }

    public function setLogo(EmbeddedFile $logo): void
    {
        $this->logo = $logo;
    }

    public function getLogo(): ?EmbeddedFile
    {
        return $this->logo;
    }

}
