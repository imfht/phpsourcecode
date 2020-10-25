<?php

namespace Bluehouseapp\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserBehavior
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserBehavior
{

    public function __construct()
    {
       $this->actionTimestamp = new \DateTime();
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
     * @ORM\Column(name="actionName", type="string", length=100)
     */
    private $actionName;

    /**
     * @var string
     *
     * @ORM\Column(name="clientIP", type="string", length=100,nullable = true)
     */    
    private $clientIP;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="actionTimestamp", type="datetime")
     */
    private $actionTimestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="userId", type="bigint")
     */
    private $userId;


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
     * Set actionName
     *
     * @param string $actionName
     * @return UserBehavior
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * Get actionName
     *
     * @return string 
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * Set actionTimestamp
     *
     * @param \DateTime $actionTimestamp
     * @return UserBehavior
     */
    public function setActionTimestamp($actionTimestamp)
    {
        $this->actionTimestamp = $actionTimestamp;

        return $this;
    }

    /**
     * Get actionTimestamp
     *
     * @return \DateTime 
     */
    public function getActionTimestamp()
    {
        return $this->actionTimestamp;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserBehavior
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set clientIP
     *
     * @param string $clientIP
     * @return UserBehavior
     */
    public function setClientIP($clientIP)
    {
        $this->clientIP = $clientIP;

        return $this;
    }

    /**
     * Get clientIP
     *
     * @return string 
     */
    public function getClientIP()
    {
        return $this->clientIP;
    }
}
