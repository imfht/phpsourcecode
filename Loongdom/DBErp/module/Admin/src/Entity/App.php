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

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 系统绑定
 * @package Admin\Entity
 * @ORM\Entity(repositoryClass="Admin\Repository\AppRepository")
 * @ORM\Table(name="dberp_app")
 */
class App extends BaseEntity
{
    /**
     * 自增id
     * @ORM\Id()
     * @ORM\Column(name="app_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $appId;

    /**
     * 系统名称
     * @ORM\Column(name="app_name", type="string", length=100)
     */
    private $appName;

    /**
     *
     * @ORM\Column(name="app_access_id", type="string", length=30)
     */
    private $appAccessId;

    /**
     *
     * @ORM\Column(name="app_access_secret", type="string", length=50)
     */
    private $appAccessSecret;

    /**
     * 系统url地址
     * @ORM\Column(name="app_url", type="string", length=100)
     */
    private $appUrl;

    /**
     * url端口，默认是 80
     * @ORM\Column(name="app_url_port", type="string", length=10)
     */
    private $appUrlPort;

    /**
     * 应用类别，DBShop 或 其他
     * @ORM\Column(name="app_type", type="string", length=20)
     */
    private $appType;

    /**
     * 商品绑定类型
     * @ORM\Column(name="app_goods_bind_type", type="string", length=20)
     */
    private $appGoodsBindType;

    /**
     * 是否启用商品绑定，0 关闭，1 启用
     * @ORM\Column(name="app_goods_bind", type="integer", length=1)
     */
    private $appGoodsBind;

    /**
     * 状态, 0 禁用，1 启用
     * @ORM\Column(name="app_state", type="integer", length=2)
     */
    private $appState;

    /**
     * 添加时间
     * @ORM\Column(name="app_add_time", type="integer", length=10)
     */
    private $appAddTime;

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return mixed
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @param mixed $appName
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    /**
     * @return mixed
     */
    public function getAppAccessId()
    {
        return $this->appAccessId;
    }

    /**
     * @param mixed $appAccessId
     */
    public function setAppAccessId($appAccessId)
    {
        $this->appAccessId = $appAccessId;
    }

    /**
     * @return mixed
     */
    public function getAppAccessSecret()
    {
        return $this->appAccessSecret;
    }

    /**
     * @param mixed $appAccessSecret
     */
    public function setAppAccessSecret($appAccessSecret)
    {
        $this->appAccessSecret = $appAccessSecret;
    }

    /**
     * @return mixed
     */
    public function getAppUrl()
    {
        return $this->appUrl;
    }

    /**
     * @param mixed $appUrl
     */
    public function setAppUrl($appUrl)
    {
        $this->appUrl = $appUrl;
    }

    /**
     * @return mixed
     */
    public function getAppUrlPort()
    {
        return $this->appUrlPort;
    }

    /**
     * @param mixed $appUrlPort
     */
    public function setAppUrlPort($appUrlPort)
    {
        $this->appUrlPort = $appUrlPort;
    }

    /**
     * @return mixed
     */
    public function getAppType()
    {
        return $this->appType;
    }

    /**
     * @param mixed $appType
     */
    public function setAppType($appType)
    {
        $this->appType = $appType;
    }

    /**
     * @return mixed
     */
    public function getAppState()
    {
        return $this->appState;
    }

    /**
     * @return mixed
     */
    public function getAppGoodsBindType()
    {
        return $this->appGoodsBindType;
    }

    /**
     * @param mixed $appGoodsBindType
     */
    public function setAppGoodsBindType($appGoodsBindType)
    {
        $this->appGoodsBindType = $appGoodsBindType;
    }

    /**
     * @return mixed
     */
    public function getAppGoodsBind()
    {
        return $this->appGoodsBind;
    }

    /**
     * @param mixed $appGoodsBind
     */
    public function setAppGoodsBind($appGoodsBind)
    {
        $this->appGoodsBind = $appGoodsBind;
    }

    /**
     * @param mixed $appState
     */
    public function setAppState($appState)
    {
        $this->appState = $appState;
    }

    /**
     * @return mixed
     */
    public function getAppAddTime()
    {
        return $this->appAddTime;
    }

    /**
     * @param mixed $appAddTime
     */
    public function setAppAddTime($appAddTime)
    {
        $this->appAddTime = $appAddTime;
    }
}