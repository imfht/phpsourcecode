<?php
namespace Jykj\User\Domain\Repository;

/***
 *
 * This file is part of the "用户管理" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 杨世昌 <yangshichang@ngoos.org>, 极益科技
 *
 ***/

/**
 * The repository for Users
 */
class UserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
{
	/**
	 * 查询所有用户
	 * @param string $keyword
	*/
	public function findAlls($keyword){
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE); //忽略pid
		//$query->getQuerySettings()->setIgnoreEnableFields(TRUE); //忽略disable=1
		$arr=array();
		if($keyword != ''){
			$arr[] = $query->logicalOr(array(
					$query->like('name', '%' . $keyword . '%'),
					$query->like('idcard', '%' . $keyword . '%'),
					$query->like('ranks', '%' . $keyword . '%'),
					$query->like('community.name', '%' . $keyword . '%'),
					$query->like('telephone', '%' . $keyword . '%')
			));
		}
		$arr[]=$query->greaterThan('uid',1);//超级管理员不显示
		$arr[]=$query->greaterThan('community',0);//没有社区的不显示
		
		if(!empty($arr)){
			$query->matching($query->logicalAnd($arr));
		}
	
		$query->setOrderings(array(
				'crdate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
		));
		return $query->execute();
	}
	
	/**
	 * 新增或修改的时候，检测用户或邮箱是否存在
	 * @param string $username
	 * @param string $email
	 * @param int $uid
	 */
	public function checkExist($username,$email,$telephone,$uid){
	    $query = $this->createQuery();
	    $query->getQuerySettings()->setRespectStoragePage(FALSE); //忽略pid
	    $query->getQuerySettings()->setIgnoreEnableFields(TRUE); //忽略disable=1
	    $arr=array();
	    if($uid==0){
	        if($username!=""){
	            $arr[] = $query->equals('username',$username);
	        }else if($email!=""){
	            $arr[] = $query->equals('email',$email);
	        }else if($telephone!=""){
	            $arr[] = $query->equals('telephone',$telephone);
	        }
	    }else{
	        if($username!=""){
	            $arr[] = $query->equals('username',$username);
	        }else if($email!=""){
	            $arr[] = $query->equals('email',$email);
	        }else if($telephone!=""){
	            $arr[] = $query->equals('telephone',$telephone);
	        }
	        $arr[] = $query->logicalNot($query->equals('uid',$uid));
	    }
	    $query->matching($query->logicalAnd($arr));
	    return $query->execute()->count();
	}
	
	
	/**
	 * 通过openid查询用户是否存在
	 * @param string $openid
	 */
	public function findUser2Openid($openid){
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE); //忽略pid
		$arr=array();
		$arr[]=$query->equals("openid",$openid);
		$query->matching($query->logicalAnd($arr));
		return $query->execute()->getFirst();
	}
	
	/**
	 * 用户名校验
	 * @param string $username
	 * @param int $usergroup 用户组
	 */
	public function userCheck($username,$usergroup){
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE); //忽略pid
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE); //忽略disable=1
		$arr=array();
		$arr[] = $query->equals('username',$username);
		if($usergroup>0){
			$arr[] = $query->equals('usergroup',$usergroup);
		}
		$query->matching($query->logicalAnd($arr));
		return $query->execute();
	}
	
	/**
	 * 用户名列表
	 */
	public function findUserList($group,$uid=0){
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$arr=array();
		$arr[]=$query->in("usergroup",$group);
		if($uid!=0){
			$arr[]=$query->equals('uid',$uid);
		}else{
			$arr[] = $query->logicalNot($query->equals('uid',143));
		}
		$query->matching($query->logicalAnd($arr));
		return $query->execute();
	}

	/**
	 * 查询数据 主要是disable=1情况无法查询
	 * @param int $uid
	 */
	public function findUserInfo($uid){
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE); //忽略pid
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE); //忽略disable=1
		$arr=array();
		$arr[]=$query->equals("uid",$uid);
		$query->matching($query->logicalAnd($arr));
		return $query->execute()->getFirst();
	}
	
}
