<?php

namespace Bluehouseapp\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Post
 * @Vich\Uploadable
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bluehouseapp\Bundle\CoreBundle\Entity\PostRepository")
 */
class Post
{

    public function __construct()
    {

        $this->comments = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = $this->created;
        $this->lastCommentTime = $this->created;
        $this->commentCount = 0;
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
     *
     * @ORM\Column(name="title", type="string", length=254)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "3",
     *      max = "254",
     *      minMessage = "标题至少需要{{ limit }}个字符 ",
     *      maxMessage = "标题不能多于{{ limit }}个字符"
     * )
     */
    private $title;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\Length(
     *      min = "1",
     *      minMessage = "不能发布空内容的帖子"
     * )
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
     * @var integer
     *
     * @ORM\Column(name="commentCount", type="integer")
     */
    private $commentCount;

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


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastCommentTime", type="datetime")
     */
    private $lastCommentTime;


    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;

    /**
     * @ORM\OneToMany(targetEntity="PostComment", mappedBy="post",cascade="remove")
     */
    protected $comments;


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
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
     */
    protected $node;

    /**
     * @param mixed $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }


    public function setImage($image)
    {
        if ($image) {
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

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param \DateTime $lastCommentTime
     */
    public function setLastCommentTime($lastCommentTime)
    {
        $this->lastCommentTime = $lastCommentTime;
    }

    /**
     * @return \DateTime
     */
    public function getLastCommentTime()
    {
        return $this->lastCommentTime;
    }

    /**
     * @param int $commentCount
     */
    public function setCommentCount($commentCount)
    {
        $this->commentCount = $commentCount;
    }

    /**
     * @return int
     */
    public function getCommentCount()
    {
        return $this->commentCount;
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
     * Add comments
     *
     * @param \Bluehouseapp\Bundle\CoreBundle\Entity\PostComment $comments
     * @return Post
     */
    public function addComment(\Bluehouseapp\Bundle\CoreBundle\Entity\PostComment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Bluehouseapp\Bundle\CoreBundle\Entity\PostComment $comments
     */
    public function removeComment(\Bluehouseapp\Bundle\CoreBundle\Entity\PostComment $comments)
    {
        $this->comments->removeElement($comments);
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
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function  getMemberName()
    {
        $member = $this->getMember();
        $name = $member->getNickname() ? $member->getNickname() : $member->getUsername();
        return $name;
    }


   public  function  getNodeName(){
       $node=$this->getNode();
       return $node->getName();
   }

    public  function  getMemberImageURL(){
        $member = $this->getMember();
        return $member->getUserimageurl();
    }

}
