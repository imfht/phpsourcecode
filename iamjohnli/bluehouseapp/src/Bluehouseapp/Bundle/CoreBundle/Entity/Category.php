<?php

namespace Bluehouseapp\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @UniqueEntity(
 *     fields={"name"},
 *     message="分类名称不能与已有的分类重复"
 * )
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bluehouseapp\Bundle\CoreBundle\Entity\CategoryRepository")
 */
class Category
{

    public function __construct()
    {

        $this->nodes = new ArrayCollection();
        $this->created = new \DateTime();
        $this->modified = $this->created;
        $this->no = 0;
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="no", type="integer")
     */
    private $no;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;


    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="category",cascade="remove")
     * @ORM\OrderBy({"no" = "ASC"})
     */
    protected $nodes;

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
     * Add nodes
     *
     * @param \Bluehouseapp\Bundle\CoreBundle\Entity\Node $nodes
     * @return Post
     */
    public function addNode(\Bluehouseapp\Bundle\CoreBundle\Entity\Node $nodes)
    {
        $this->nodes[] = $nodes;

        return $this;
    }

    /**
     * Remove nodes
     *
     * @param \Bluehouseapp\Bundle\CoreBundle\Entity\Node $nodes
     */
    public function removeNode(\Bluehouseapp\Bundle\CoreBundle\Entity\Node $nodes)
    {
        $this->nodes->removeElement($nodes);
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
     * @return Category
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
     * @param integer $status
     * @return Category
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set no
     *
     * @param integer $no
     * @return Category
     */
    public function setNo($no)
    {
        $this->no = $no;

        return $this;
    }

    /**
     * Get no
     *
     * @return integer 
     */
    public function getNo()
    {
        return $this->no;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Category
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
     * @param mixed $nodes
     */
    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @return mixed
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    public  function __toString(){
        return $this->getName();
    }



}
