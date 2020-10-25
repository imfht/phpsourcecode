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

namespace Admin\Plugin;

use Admin\Entity\AdminUserGroup;
use Admin\Entity\App;
use Admin\Entity\Region;
use Admin\Service\OperlogManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\I18n\Translator;
use Zend\Session\Container;
use Zend\Validator\Csrf;

class AdminCommonPlugin extends AbstractPlugin
{
    private $entityManager;
    private $translator;
    private $operlogManager;
    private $sessionAdmin;

    public function __construct(
        EntityManager   $entityManager,
        Translator      $translator,
        OperlogManager  $operlogManager
    )
    {
        $this->entityManager    = $entityManager;
        $this->translator       = $translator;
        $this->operlogManager   = $operlogManager;

        if($this->sessionAdmin == null) {
            $this->sessionAdmin     = new Container('admin');
        }
    }

    /**
     * 验证删除的CSRF Token
     * @return bool
     */
    public function validatorCsrf()
    {
        $csrfValue = $this->getController()->getRequest()->getQuery('qToken');
        $csrf = new Csrf(['name' => 'queryToken']);
        if(!$csrf->isValid($csrfValue)) {
            $this->getController()->flashMessenger()->addErrorMessage($this->translator->translate('不正确的请求！'));
            return false;
        }
        return true;
    }

    /**
     * 公共分页方法
     * @param Query $query
     * @param int $pageNumber
     * @param int $itemCountPerPage
     * @param bool $fetchJoinCollection
     * @return \Zend\Paginator\Paginator
     */
    public function erpPaginator(Query $query, int $pageNumber, $itemCountPerPage = 16, $fetchJoinCollection = false)
    {
        $adapter    = new DoctrinePaginator(new Paginator($query, $fetchJoinCollection));
        $paginator  = new \Zend\Paginator\Paginator($adapter);
        $paginator->setItemCountPerPage($itemCountPerPage);
        $paginator->setCurrentPageNumber($pageNumber);

        return $paginator;
    }

    /**
     * 返回上一页
     * @return mixed
     */
    public function toReferer()
    {
        $referer = $this->getController()->params()->fromHeader('Referer');
        if($referer) {
            $refererUrl     = $referer->uri()->getPath();
            $refererHost    = $referer->uri()->getHost();
            $host           = $this->getController()->getRequest()->getUri()->getHost();
            if ($refererUrl && $refererHost == $host) {
                return $this->getController()->redirect()->toUrl($refererUrl);
            }
        }
        return $this->getController()->redirect()->toRoute('home');
    }

    /**
     * 添加操作日志
     * @param $logBody
     * @param $operClassName
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addOperLog($logBody, $operClassName)
    {
        $this->operlogManager->addOperLog(
            [
                'logOperUser'       => $this->sessionAdmin->admin_name,
                'logOperUserGroup'  => $this->sessionAdmin->admin_group_name,
                'logTime'           => time(),
                'logIp'             => $this->getController()->getRequest()->getServer('REMOTE_ADDR'),
                'logBody'           => '['.$operClassName.'] '.$logBody
            ]
        );
    }

    /**
     * 获取地区下级
     * @param int $topId
     * @return array|object[]|null
     */
    public function getRegionSub($topId = 0)
    {
        $region = $this->entityManager->getRepository(Region::class)->findBy(['regionTopId' => $topId], ['regionSort' => 'ASC']);
        return $region ? $region : null;
    }

    /**
     * 获取绑定商城列表
     * @param string $topName
     * @return array
     */
    public function appShopOptions($topName = '')
    {
        $appArray   = [0 => empty($topName) ? $this->translator->translate('选择商城') : $topName];
        $appList    = $this->entityManager->getRepository(App::class)->findBy([]);
        if($appList) {
            foreach ($appList as $value) {
                $appArray[$value->getAppId()] = $value->getAppName();
            }
        }
        return $appArray;
    }

    /**
     * 获取品牌列表
     * @param string $topName
     * @return array
     */
    public function adminGroupOptions($topName = '')
    {
        $groupList  = [0 => empty($topName) ? $this->translator->translate('选择管理组') : $topName];
        $group      = $this->entityManager->getRepository(AdminUserGroup::class)->findBy([], ['adminGroupId' => 'ASC']);
        if($group) {
            foreach ($group as $value) {
                $groupList[$value->getAdminGroupId()] = $value->getAdminGroupName();
            }
        }
        return $groupList;
    }
    /**
     * 平铺无限分类-暂时未使用
     * @param $items
     * @param string $id
     * @param string $pid
     * @param string $name
     * @param string $path
     * @param string $son
     * @return array
     */
    public function genTree(
        $items,
        $id='getGoodsCategoryId',
        $pid='getGoodsCategoryTopId',
        $name='getGoodsCategoryName',
        $path='getGoodsCategoryPath',
        $son = 'children'
    )
    {
        $tree = []; //格式化的树
        $tmpMap = [];  //临时扁平数据

        foreach ($items as $item) {
            $tmpMap[$item->$id()] = [
                'id'    => $item->$id(),
                'top_id'=> $item->$pid(),
                'name'  => $item->$name(),
                'path'  => $item->$path()
            ];
        }

        foreach ($items as $item) {
            if (isset($tmpMap[$item->$pid()])) {
                $tmpMap[$item->$pid()][$son][] = &$tmpMap[$item->$id()];
                //$tmpMap[$item->$pid()][$son][] = $tmpMap[$item->$id()];
            } else {
                $tree[] = &$tmpMap[$item->$id()];
                //$tree[] = $tmpMap[$item->$id()];
            }
        }
        unset($tmpMap);
        return $tree;
    }
}