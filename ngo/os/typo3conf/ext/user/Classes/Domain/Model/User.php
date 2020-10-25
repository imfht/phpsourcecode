<?php
namespace Jykj\User\Domain\Model;

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
 * 用户信息扩展
 */
class User extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
{
	/**
	 * openid
	 * 小程序openid
	 * @var string
	 */
	protected $openid = '';
	
    /**
     * headimgurl
     * 头像
     * @var string
     */
    protected $headimgurl = '';
    
    
    /**
     * Returns the openid
     *
     * @return string $openid
     */
    public function getOpenid()
    {
    	return $this->openid;
    }
    
    /**
     * Sets the openid
     *
     * @param string $openid
     * @return void
     */
    public function setOpenid($openid)
    {
    	$this->openid = $openid;
    }
    
    /**
     * Returns the headimgurl
     *
     * @return string $headimgurl
     */
    public function getHeadimgurl()
    {
    	return $this->headimgurl;
    }
    
    /**
     * Sets the headimgurl
     *
     * @param string $headimgurl
     * @return void
     */
    public function setHeadimgurl($headimgurl)
    {
    	$this->headimgurl = $headimgurl;
    }
}
