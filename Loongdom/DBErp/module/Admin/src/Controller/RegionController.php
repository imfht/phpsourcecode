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

namespace Admin\Controller;

use Admin\Entity\Region;
use Admin\Form\RegionForm;
use Admin\Form\SearchRegionForm;
use Admin\Service\RegionManager;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class RegionController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $regionManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        RegionManager   $regionManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->regionManager    = $regionManager;
    }

    /**
     * 地区列表
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $regionTopId = (int) $this->params()->fromRoute('id', 0);

        $criteria   = Criteria::create();
        $expr       = Criteria::expr();
        $criteria->where($expr->eq('regionTopId', $regionTopId));

        $searchForm = new SearchRegionForm();
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) {
                $criteria = $searchForm->criteriaSearchData($criteria, $expr);
            }
        }
        $array['searchForm'] = $searchForm;

        $regionList = $this->entityManager->getRepository(Region::class)->matching($criteria);
        $array['regionList'] = $regionList;

        if($regionTopId > 0) {
            $regionInfo = $this->entityManager->getRepository(Region::class)->findOneByRegionId($regionTopId);
            if($regionInfo) {
                $array['regionInfo'] = $regionInfo;
            }
        }
        $array['regionTopId'] = $regionTopId;

        return $array;
    }

    /**
     * 地区添加
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $array = [];

        $regionTopId = (int) $this->params()->fromRoute('id', 0);

        $form = new RegionForm();
        $array['form'] = $form;

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->regionManager->addRegion($data);

                $message = $this->translator->translate('地区添加成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('地区管理'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('region', ['action' => 'index', 'id' => $regionTopId]);
            }
        }

        if($regionTopId > 0) {
            $regionTopInfo = $this->entityManager->getRepository(Region::class)->findOneByRegionId($regionTopId);
            if($regionTopInfo) {
                if(substr_count($regionTopInfo->getRegionPath(), ',') >= 3) {
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('地区数据最多添加三级！'));
                    return $this->redirect()->toRoute('region', ['action' => 'index', 'id' => $regionTopId]);
                }

                $array['regionTopInfo'] = $regionTopInfo;
                $form->get('regionTopId')->setValue($regionTopId);
            }
        }

        return $array;
    }

    /**
     * 编辑地区
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $regionId = (int) $this->params()->fromRoute('id', 0);

        $regionInfo = $this->entityManager->getRepository(Region::class)->findOneByRegionId($regionId);
        if($regionInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该地区不存在！'));
            return $this->redirect()->toRoute('region');
        }

        $form = new RegionForm();

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->regionManager->editRegion($data, $regionInfo);

                $message = sprintf($this->translator->translate('地区 %s 编辑成功！'), $data['regionName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('地区管理'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('region', ['action'=> 'index', 'id' => $regionInfo->getRegionTopId()]);
            }
        } else $form->setData($regionInfo->valuesArray());

        $form->get('regionName')->setAttribute('type', 'text');
        $form->get('regionTopId')->setValue($regionInfo->getRegionTopId());

        return ['form' => $form, 'region' => $regionInfo];
    }

    /**
     * 地区批量处理
     * @return \Zend\Http\Response
     */
    public function updateAllAction()
    {
        $regionTopId = (int) $this->params()->fromRoute('id', 0);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            if(!empty($data['select_id']) and !empty($data['editAllState'])) {
                $this->regionManager->updateAllRegion($data);

                $message = $this->translator->translate('批量处理成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('地区管理'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('region', ['action'=> 'index', 'id' => $data['region_top_id']]);
            }
        }
        return $this->redirect()->toRoute('region', ['action' => 'index', 'id' => $regionTopId]);
    }
    /**
     * 删除地区
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $regionId = (int) $this->params()->fromRoute('id', 0);

        $regionInfo = $this->entityManager->getRepository(Region::class)->findOneByRegionId($regionId);
        if($regionInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该地区不存在！'));
            return $this->redirect()->toRoute('region');
        }

        $regionArray = $this->entityManager->getRepository(Region::class)->findBy(['regionTopId' => $regionId]);
        if($regionArray) {
            $this->flashMessenger()->addErrorMessage(sprintf($this->translator->translate('地区 %s 删除失败！其下级还有其他地区！'), $regionInfo->getRegionName()));
            return $this->redirect()->toRoute('region', ['action'=> 'index', 'id' => $regionInfo->getRegionTopId()]);
        }

        $this->regionManager->deleteRegion($regionInfo);

        $message = sprintf($this->translator->translate('地区 %s 删除成功！'), $regionInfo->getRegionName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('地区管理'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }

    /**
     * ajax获取下级地区
     * @return JsonModel
     */
    public function ajaxRegionAction()
    {
        $regionTopId    = (int) $this->request->getPost('region_id');
        $regionList     = $this->entityManager->getRepository(Region::class)->findBy(['regionTopId' => $regionTopId], ['regionSort' => 'ASC']);
        $regionArray    = [];
        if($regionList) {
            foreach ($regionList as $value) {
                $regionArray[] = [
                    'region_id' => $value->getRegionId(),
                    'region_name' => $value->getRegionName()
                ];
            }
        }

        return new JsonModel($regionArray);
    }
}