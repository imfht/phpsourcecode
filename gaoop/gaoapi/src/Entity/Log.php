<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Log
{
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_REMOVE = 'remove';

    const PATH_CREATE = 1;
    const PATH_UPDATE = 2;
    const PATH_REMOVE = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="info_id", type="integer", nullable=false, options={"comment"="关联元数据表ID"})
     */
    private $info_id;

    /**
     * @ORM\Column(name="version", type="string", length=255, nullable=true, options={"comment"="api版本"})
     */
    private $version;

    /**
     * @ORM\Column(name="path", type="string", length=255, nullable=true, options={"comment"="请求路径"})
     */
    private $path;

    /**
     * @ORM\Column(name="action", type="integer", nullable=true, options={"default"="0", "comment"="更新方式 1新建 2更新 3移除"})
     */
    private $action = '0';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="接口变更详情"})
     */
    private $body;

    /**
     * @ORM\Column(type="integer",  nullable=true, options={"comment"="操作用户ID"})
     */
    private $admin_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInfoId(): ?int
    {
        return $this->info_id;
    }

    public function setInfoId(int $info_id): self
    {
        $this->info_id = $info_id;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAtValue()
    {
        $this->created_at = new \DateTime();
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getAction(): ?int
    {
        return $this->action;
    }

    public function setAction(?int $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAdminId(): ?int
    {
        return $this->admin_id;
    }

    public function setAdminId(?int $admin_id): self
    {
        $this->admin_id = $admin_id;

        return $this;
    }
}
