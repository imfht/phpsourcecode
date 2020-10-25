<?php

namespace Bluehouseapp\Bundle\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity(
 *     fields={"username", "email"},
 *     message="用户名或者电子信箱不能与已注册的用户重复"
 * )
 * @Vich\Uploadable
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bluehouseapp\Bundle\CoreBundle\Entity\MemberRepository")
 */
class Member extends BaseUser
{

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->created = new \DateTime();
        $this->modified = $this->created;
        $this->addRole(self::ROLE_USER);
    }

    /**
     *
     * @ORM\Column(name="nickname",type="string",length=255,nullable=true)
     * @Assert\Length(
     *     min="2",
     *     max="36",
     *     minMessage="昵称不能少于2个字符",
     *     maxMessage="昵称不能多于36个字符"
     * )
     */
    protected $nickname;

    /**
     * @ORM\Column(name="website", type="string",length=500, nullable=true)
     * @Assert\Url(message="请使用合法的URL地址")
     */
    protected $website;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\Length(
     *     max="400",
     *     maxMessage="个人介绍不能超过400个字"
     * )
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime")
     */
    private $modified;


    /**
     * @ORM\Column(type="string", length=255, name="avatar",nullable = true)
     *
     * @var string $imageName
     */
    private $avatar;


    /**
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png","image/jpeg","image/pjpeg",
     *                          "image/jpg","image/gif"}
     * )
     * @Vich\UploadableField(mapping="memeber_image", fileNameProperty="avatar")
     *
     * @var File $image
     */
    private $userImage;

    /**
     * @Assert\NotBlank(message="用户名不可为空")
     * @Assert\Length(
     *     min="4",
     *     max="36",
     *     minMessage="用户名不能少于4个字符",
     *     maxMessage="用户名不能多于36个字符"
     * )
     * @Assert\Regex(
     *    pattern="/^[A-z0-9]*$/i",
     *    message="用户名只能使用英文字母和数字"
     * )
     */
    protected $username;


    /**
     * @ORM\Column(name="weibo",type="string",length=255,nullable=true)
     * @assert\Length(
     *         max="255",
     *         maxMessage="不能超过255个字符"
     * )
     * @Assert\Regex(
     *    pattern="/^(http):\/\/weibo.com\//",
     *    message="请使用合法的微博地址"
     * )
     */
    protected $weibo;

    /**
     * @ORM\Column(name="github",type="string",length=255,nullable=true)
     * @assert\Length(
     *         max="255",
     *         maxMessage="不能超过255个字符"
     * )
     * @Assert\Regex(
     *    pattern="/^(http|https):\/\/github.com\//",
     *    message="请使用合法的github地址"
     * )
     */
    protected $github;

    /**
     * @ORM\Column(name="oschina",type="string",length=255,nullable=true)
     * @assert\Length(
     *         max="255",
     *         maxMessage="不能超过255个字符"
     * )
     * @Assert\Regex(
     *    pattern="/^(http|https):\/\/my.oschina.net\//",
     *    message="请使用合法的oschina地址"
     * )
     */
    protected $oschina;


    /**
     * @ORM\Column(name="city",type="string",length=60,nullable=true)
     * @Assert\Length(
     *    max="60",
     *    maxMessage="所在位置不能超过60个字"
     * )
     */
    protected $city;



    /*
   * @UniqueEntity(
   *     fields={"username", "email"},
   *     message="用户名或者电子信箱不能与已注册的用户重复"
       * )

           /**
            * @param mixed $username
            */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }



    /**
     * @param \DateTime $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return mixed
     */
    public function getNickname()
    {
        return $this->nickname;
    }


    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $github
     */
    public function setGithub($github)
    {
        $this->github = $github;
    }

    /**
     * @return mixed
     */
    public function getGithub()
    {
        return $this->github;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File $userImage
     */
    public function setUserImage($userImage)
    {
        $this->userImage = $userImage;
        if ($userImage) {
            $this->avatar = $userImage->getFileName();
        }
        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function getUserImage()
    {
        return $this->userImage;
    }


    /**
     * @param mixed $weibo
     */
    public function setWeibo($weibo)
    {
        $this->weibo = $weibo;
    }

    /**
     * @return mixed
     */
    public function getWeibo()
    {
        return $this->weibo;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $oschina
     */
    public function setOschina($oschina)
    {
        $this->oschina = $oschina;
    }

    /**
     * @return mixed
     */
    public function getOschina()
    {
        return $this->oschina;
    }


    private $userimageurl;

    /**
     * @param mixed $userimageurl
     */
    public function setUserimageurl($userimageurl)
    {
        $this->userimageurl = $userimageurl;
    }

    /**
     * @return mixed
     */
    public function getUserimageurl()
    {
        return $this->userimageurl;
    }







}
