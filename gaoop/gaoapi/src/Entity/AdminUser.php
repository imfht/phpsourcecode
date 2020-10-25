<?php

namespace App\Entity;

use App\Library\Helper\GeneralHelper;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="admin_user")
 * @ORM\Entity(repositoryClass="App\Repository\AdminUserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 * @Vich\Uploadable()
 */
class AdminUser implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true, options={"comment"="用户名"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *     pattern="/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9a-zA-Z]+$/",
     *     message="密码应包含数字和字母"
     * )
     * @Assert\Length(
     *      min = 6,
     *      max = 20,
     *      minMessage = "密码最少 {{ limit }} 个字符",
     *      maxMessage = "密码最多 {{ limit }} 个字符"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default"="0","comment"="模块组ID"})
     */
    private $admin_user_group_id = 0;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $avatar;

    /**
     * @Vich\UploadableField(mapping="admin_user_avatar", fileNameProperty="avatar.name", size="avatar.size", mimeType="avatar.mimeType", originalName="avatar.originalName", dimensions="avatar.dimensions")
     *
     * @var File|null
     */
    private $avatar_file;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    public function __construct()
    {
        $this->avatar = new EmbeddedFile();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getAdminUserGroupId(): ?int
    {
        return $this->admin_user_group_id;
    }

    public function setAdminUserGroupId(?int $admin_user_group_id): self
    {
        $this->admin_user_group_id = $admin_user_group_id;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAdminUserGroupName(): ?string
    {
        return GeneralHelper::getOneInstance()->getAdminUserGroupNameById($this->getAdminUserGroupId());
    }

    /**
     * 新建之前
     * @ORM\PrePersist()
     */
    public function prePersistHook()
    {
        // 添加默认用户组
        $this->roles = ['ROLE_ADMIN', 'ROLE_SONATA_ADMIN'];
        // 添加创建时间
        $this->created_at = new \DateTime();
    }

    /**
     * 更新之前
     * @ORM\PreUpdate
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdateHook(PreUpdateEventArgs $eventArgs)
    {
        // 处理密码
        $this->processPreUpdatePassword($eventArgs);
    }

    /**
     * @ORM\PostUpdate()
     */
    public function postUpdateHook()
    {
        $this->avatar_file = null;
    }

    /**
     * User: Gao
     * Date: 2020/3/20
     * Description: 编辑：用户密码为空时处理
     * @param $eventArgs
     */
    public function processPreUpdatePassword($eventArgs)
    {
        if ($eventArgs->hasChangedField('password')) {
            if ($eventArgs->getNewValue('password') === null) {
                $this->password = $eventArgs->getOldValue('password');
            }
        }
    }

    /**
     * @param File|UploadedFile|null $avatarFile
     * @throws \Exception
     */
    public function setAvatarFile(?File $avatarFile = null)
    {
        $this->avatar_file = $avatarFile;
        if ($this->avatar_file instanceof UploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatar_file;
    }

    public function setAvatar(EmbeddedFile $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getAvatar(): ?EmbeddedFile
    {
        return $this->avatar;
    }

}
