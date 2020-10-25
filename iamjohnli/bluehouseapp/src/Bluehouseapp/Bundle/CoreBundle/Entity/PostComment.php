<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 14-10-4
 * Time: 下午10:05
 */

namespace Bluehouseapp\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bluehouseapp\Bundle\CoreBundle\Entity\PostCommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PostComment {

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = $this->created;
        $this->enabled = true;
        $this->status = true;
    }
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "3",
     *      minMessage = "评论内容至少需要{{ limit }}个字"
     * )
     * @ORM\Column(name="content", type="text")
     */
    private $content;

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
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;


    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;


    /**
     * @ORM\Column(type="string", length=255, name="attachment",nullable = true)
     *
     * @var string $imageName
     */
    private $attachment;

    /**
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/png","image/jpeg","image/pjpeg",
     *                          "image/jpg","image/gif"}
     * )
     * @Vich\UploadableField(mapping="discuss_image", fileNameProperty="attachment")
     *
     * @var File $image
     */
    private $image;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;


    public function setImage($image)
    {
        if($image){
            $this->attachment = $image->getFileName();
        }
        $this->image = $image;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }


    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @param mixed $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }


    /**
     * @ORM\PostPersist
     */
    public function increasePostCommentCount()
    {
        $this->post->setCommentCount($this->post->getCommentCount()+1);
    }

    /**
     * @ORM\PostRemove
     */
    public function decreasePostCommentCount()
    {
        $count = $this->post->getCommentCount()-1;
        $this->post->setCommentCount($count>=0?$count:0);
    }

    /**
     * @param mixed $member
     */
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @return mixed
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param string $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }


    public function  getMemberName()
    {
        $member = $this->getMember();
        $name = $member->getNickname() ? $member->getNickname() : $member->getUsername();
        return $name;
    }

    public function  getPostTitle()
    {
        $post = $this->getPost();
        $title = $post->getTitle();
        return $title;
    }
    public  function  getMemberImageURL(){
        $member = $this->getMember();
        return $member->getUserimageurl();
    }

} 