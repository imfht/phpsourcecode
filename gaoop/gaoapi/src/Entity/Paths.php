<?php

namespace App\Entity;

use App\Library\Helper\GetterHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * Paths
 *
 * @ORM\Table(name="paths")
 * @ORM\Entity(repositoryClass="App\Repository\PathsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Paths
{
    const CATEGORY_PARAMETER = 1;
    const CATEGORY_BODY = 2;

    const ACTION_CREATE = 1;
    const ACTION_UPDATE = 2;
    const ACTION_REMOVE = 3;

    private $entityChangeSet = [];

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
     * @ORM\Column(name="url", type="string", length=255, nullable=false, options={"comment"="请求路径"})
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="method_id", type="integer", nullable=false, options={"comment"="请求方法"})
     */
    private $methodId;

    /**
     * @var int
     *
     * @ORM\Column(name="tag_id", type="integer", nullable=false, options={"comment"="关联标签ID"})
     */
    private $tagId;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255, nullable=false, options={"comment"="摘要"})
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="operation_id", type="string", length=255, nullable=false, options={"comment"="swagger-ui操作唯一符"})
     */
    private $operationId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true, options={"comment"="描述"})
     */
    private $description;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_security", type="boolean", nullable=true, options={"default"="1","comment"="是否开启安全校验"})
     */
    private $isSecurity = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="create_admin_id", type="integer", nullable=true, options={"default"="0", "comment"="创建人ID"})
     */
    private $create_admin_id = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="update_admin_id", type="integer", nullable=true, options={"default"="0", "comment"="更新人ID"})
     */
    private $update_admin_id = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="status", type="boolean", nullable=true, options={"default"="1","comment"="状态 -1删除 1正常"})
     */
    private $status = '1';

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

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="成功返回示例"})
     */
    private $success_code;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment"="备注"})
     */
    private $remark;

    public $params;

    public $other;

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

    public function getMethodId(): ?int
    {
        return $this->methodId;
    }

    public function setMethodId(int $methodId): self
    {
        $this->methodId = $methodId;

        return $this;
    }

    public function getTagId(): ?int
    {
        return $this->tagId;
    }

    public function setTagId(?int $tagId): self
    {
        $this->tagId = $tagId;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getOperationId(): ?string
    {
        return $this->operationId;
    }

    public function setOperationId(string $operationId): self
    {
        $this->operationId = $operationId;

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

    public function getIsSecurity(): ?bool
    {
        return $this->isSecurity;
    }

    public function setIsSecurity(?bool $isSecurity): self
    {
        $this->isSecurity = $isSecurity;

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

    /**
     * User: gao
     * Date: 2019/12/25
     * Description: 删除对应参数
     * @ORM\PreRemove()
     */
    public function doPreRemove()
    {
        $em = GetterHelper::getEntityManager();
        $sql = $em->createQuery('delete from App\Entity\Parameters p where p.pathsId = ' . $this->getId());
        $numUpdated = $sql->execute();
    }

    public function getCreateAdminId(): ?int
    {
        return $this->create_admin_id;
    }

    public function setCreateAdminId(?int $create_admin_id): self
    {
        $this->create_admin_id = $create_admin_id;

        return $this;
    }

    public function getUpdateAdminId(): ?int
    {
        return $this->update_admin_id;
    }

    public function setUpdateAdminId(int $update_admin_id): self
    {
        $this->update_admin_id = $update_admin_id;

        return $this;
    }

    public function getSuccessCode(): ?string
    {
        return $this->success_code;
    }

    public function setSuccessCode(?string $success_code): self
    {
        $this->success_code = $success_code;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }
}
