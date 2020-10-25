<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Admin\Service;

use Admin\Entity\AdminUserGroup;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Admin\Entity\AdminUser;
use Zend\Crypt\Password\Bcrypt;

class AuthAdapter implements AdapterInterface
{
    private $name;

    private $password;

    private $adminState;

    private $user;

    private $userGroup;

    private $entityManager;
        
    /**
     * Constructor.
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Sets user name.
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Sets password.     
     */
    public function setPassword($password) 
    {
        $this->password = (string)$password;        
    }

    /**
     * @param mixed $adminState
     */
    public function setAdminState($adminState)
    {
        $this->adminState = $adminState;
    }
    
    /**
     * Performs an authentication attempt.
     */
    public function authenticate()
    {
        $this->user = $this->entityManager->getRepository(AdminUser::class)
            ->findOneBy([
                'adminName' => $this->name,
                'adminState' => $this->adminState
            ]);

        $bcPassword = new Bcrypt();

        if ($this->user == null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid credentials.']);
        } elseif ($bcPassword->verify($this->password, $this->user->getAdminPassword())) {
            $this->userGroup = $this->entityManager->getRepository(AdminUserGroup::class)
                ->findOneBy(['adminGroupId' => $this->user->getAdminGroupId()]);

            return new Result(
                Result::SUCCESS,
                $this->user->getAdminId(),
                ['Authenticated successfully.']);
        }

        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid credentials.']);
    }

    public function checkAdmin($adminId)
    {
        $admin = $this->entityManager->getRepository(AdminUser::class)->findOneBy(['adminId' => $adminId, 'adminState' => 1]);
        if($admin) return true;
        else return false;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUserGroup()
    {
        return $this->userGroup;
    }
}


