<?php

namespace Bluehouseapp\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity(
 *     fields={"code"},
 *     message="节点代码不能与已有的节点重复"
 * )
 * @ORM\Table()
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="Bluehouseapp\Bundle\CoreBundle\Entity\NodeRepository")
 */
class Node
{

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = $this->created;
        $this->enabled = true;
        $this->status = true;
        $this->no = 0;
        $this->parentId =0;
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;


/**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     * @Assert\NotBlank(message="节点代码不可为空")
     * @Assert\Length(
     *     min="2",
     *     max="16",
     *     minMessage="节点代码不能少于2个字符",
     *     maxMessage="节点代码不能多于16个字符"
     * )
     * @Assert\Regex(
     *    pattern="/^[A-z0-9]*$/i",
     *    message="节点代码只能使用英文字母和数字"
     * )
     */
    private $code;

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
     * @Vich\UploadableField(mapping="node_image", fileNameProperty="attachment")
     *
     * @var File $image
     */
    private $image;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512,nullable = true)
     * @Assert\NotBlank(message="节点描述不可为空")
     */
    private $description;


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
     * @var integer
     *
     * @ORM\Column(name="no", type="integer")
     */
    private $no;


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
     * @ORM\Column(name="parent_id", type="integer")
     */
    private $parentId;


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
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }





    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="nodes")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

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
     * @param int $no
     */
    public function setNo($no)
    {
        $this->no = $no;
    }

    /**
     * @return int
     */
    public function getNo()
    {
        return $this->no;
    }






    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Node
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Node
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Node
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return Node
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    public  function __toString(){
        return $this->getName();
    }


    private $nodeimageurl;

    /**
     * @param mixed $nodeimageurl
     */
    public function setNodeimageurl($nodeimageurl)
    {
        $this->nodeimageurl = $nodeimageurl;
    }

    /**
     * @return mixed
     */
    public function getNodeimageurl()
    {
        return $this->nodeimageurl;
    }






}
