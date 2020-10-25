<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parameters
 *
 * @ORM\Table(name="parameters")
 * @ORM\Entity(repositoryClass="App\Repository\ParametersRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Parameters
{
    const TYPE_PARAMS = 1;
    const TYPE_BODY = 2;

    const ACTION_CREATE = 1;
    const ACTION_UPDATE = 2;
    const ACTION_REMOVE = 3;

    const FORMAT_STRING = 1;
    const FORMAT_PASSWORD = 2;
    const FORMAT_INTEGER = 3;
    const FORMAT_BOOLEAN = 4;
    const FORMAT_DATE = 5;
    const FORMAT_DATETIME = 6;
    const FORMAT_BINARY = 7;
    const FORMAT_BYTE = 8;
    const IN_QUERY = 1;
    const IN_HEADER = 2;
    const IN_PATH = 3;
    const IN_COOKIE = 4;
    const IN_BODY = 5;

    public static $formats = [
        self::FORMAT_STRING => 'string',
        self::FORMAT_PASSWORD => 'password',
        self::FORMAT_INTEGER => 'integer',
        self::FORMAT_BOOLEAN => 'boolean',
        self::FORMAT_DATE => 'date',
        self::FORMAT_DATETIME => 'dateTime',
        self::FORMAT_BINARY => 'binary',
        self::FORMAT_BYTE => 'byte',
    ];

    public static $format_json = [
        self::FORMAT_STRING => 'string',
        self::FORMAT_INTEGER => 'integer',
        self::FORMAT_BOOLEAN => 'boolean',
    ];

    public static $categories = [
        self::IN_QUERY => 'query',
        self::IN_HEADER => 'header',
        self::IN_PATH => 'path',
        self::IN_COOKIE => 'cookie',
        self::IN_BODY => 'body',
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
     * @var int
     *
     * @ORM\Column(name="info_id", type="integer", nullable=false, options={"comment"="关联元数据表ID"})
     */
    private $infoId;

    /**
     * @var int
     *
     * @ORM\Column(name="paths_id", type="integer", nullable=false, options={"comment"="关联接口表ID"})
     */
    private $pathsId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true, options={"comment"="名称"})
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="category", type="integer", nullable=true, options={"default"="1","comment"="参数获取方式 1query 2header 3path 4cookie 5body (jsonn)"})
     */
    private $category = '1';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true, options={"comment"="描述"})
     */
    private $description;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="required", type="boolean", nullable=true, options={"default"="0","comment"="是否必填 0否 1是"})
     */
    private $required = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="version", type="string", length=255, nullable=true, options={"comment"="版本"})
     */
    private $version;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", length=1, nullable=true, options={"default"="1","comment"="状态 -1删除 1正常"})
     */
    private $status = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="format", type="integer", length=2, nullable=true, options={"default"="1","comment"="参数格式 1string 2password 3integer 4boolean 5date 6datetime"})
     */
    private $format = '1';

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

    public function getPathsId(): ?int
    {
        return $this->pathsId;
    }

    public function setPathsId(int $pathsId): self
    {
        $this->pathsId = $pathsId;

        return $this;
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): self
    {
        $this->required = $required;

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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * User: gao
     * Date: 2019/11/16
     * Description: ~
     * @throws \Exception
     * @ORM\PrePersist()
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(?int $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getFormat(): ?int
    {
        return $this->format;
    }

    public function setFormat(?int $format): self
    {
        $this->format = $format;

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
