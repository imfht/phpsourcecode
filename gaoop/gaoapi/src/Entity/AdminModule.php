<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminModuleRepository")
 */
class AdminModule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default"="0","comment"="上级模块ID 0为顶级模块"})
     */
    private $pid = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="模块名称"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="url"})
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="路由名称"})
     */
    private $route_name;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default"="0", "comment"="排序"})
     */
    private $sort = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default"="fa fa-folder", "comment"="icon图标"})
     */
    private $icon = 'fa fa-folder';

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default"="1", "comment"="状态 0停用 1启用"})
     */
    private $status = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="sonata_admin 服务标签"})
     */
    private $sonata_admin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPid(): ?int
    {
        return $this->pid;
    }

    public function setPid(?int $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getRouteName(): ?string
    {
        return $this->route_name;
    }

    public function setRouteName(?string $route_name): self
    {
        $this->route_name = $route_name;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

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

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function setController(?string $controller): self
    {
        $this->controller = $controller;

        return $this;
    }

    public function getSonataAdmin(): ?string
    {
        return $this->sonata_admin;
    }

    public function setSonataAdmin(?string $sonata_admin): self
    {
        $this->sonata_admin = $sonata_admin;

        return $this;
    }
}
