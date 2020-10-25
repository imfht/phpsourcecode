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

namespace Store\Controller;

use Doctrine\ORM\EntityManager;
use Store\Entity\Goods;
use Store\Entity\Unit;
use Store\Form\UnitForm;
use Store\Service\UnitManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class UnitController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $unitManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        UnitManager     $unitManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;

        $this->unitManager      = $unitManager;
    }

    /**
     * 单位列表
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $array['unitList'] = $this->entityManager->getRepository(Unit::class)->findBy([], ['unitSort' => 'ASC']);

        return $array;
    }

    /**
     * 添加计量单位
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new UnitForm();

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->unitManager->addUnit($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('计量单位 %s 添加成功！'), $data['unitName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('计量单位'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('unit');
            }
        }

        return ['form' => $form];
    }

    /**
     * 编辑更新单位
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $unitId = (int) $this->params()->fromRoute('id', -1);

        $unitInfo = $this->entityManager->getRepository(Unit::class)->findOneByUnitId($unitId);
        if($unitInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该单位不存在！'));
            return $this->redirect()->toRoute('unit');
        }

        $form = new UnitForm();

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->unitManager->updateUnit($data, $unitInfo);

                $message = sprintf($this->translator->translate('计量单位 %s 编辑成功！'), $data['unitName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('计量单位'));
                $this->flashMessenger()->addSuccessMessage();

                return $this->redirect()->toRoute('unit');
            }
        } else $form->setData($unitInfo->valuesArray());

        return ['unit' => $unitInfo, 'form' => $form];
    }

    /**
     * 删除计量单位
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $unitId = (int) $this->params()->fromRoute('id', -1);

        $unitInfo = $this->entityManager->getRepository(Unit::class)->findOneByUnitId($unitId);;
        if($unitInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该单位不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $oneGoods = $this->entityManager->getRepository(Goods::class)->findOneByUnitId($unitId);
        if($oneGoods) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该单位在商品中有所引用，不能删除！'));
            return $this->adminCommon()->toReferer();
        }

        $this->unitManager->deleteUnit($unitInfo);

        $message = sprintf($this->translator->translate('单位 %s 删除成功！'), $unitInfo->getUnitName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('计量单位'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }
}