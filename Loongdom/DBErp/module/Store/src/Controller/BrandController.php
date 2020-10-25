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
use Store\Entity\Brand;
use Store\Entity\Goods;
use Store\Form\BrandForm;
use Store\Service\BrandManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class BrandController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $brandManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        BrandManager    $brandManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->brandManager     = $brandManager;
    }

    /**
     * 品牌列表
     * @return array
     */
    public function indexAction()
    {
        $brandList = $this->entityManager->getRepository(Brand::class)->findBy([], ['brandSort' => 'ASC']);

        return ['brandList' => $brandList];
    }

    /**
     * 添加商品品牌
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new BrandForm();

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->brandManager->addBrand($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('商品品牌 %s 添加成功！'), $data['brandName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商品品牌'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('brand');
            }
        }

        return ['form' => $form];
    }

    /**
     * 编辑更新品牌
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $brandId = (int) $this->params()->fromRoute('id', -1);

        $brandInfo = $this->entityManager->getRepository(Brand::class)->findOneByBrandId($brandId);
        if($brandInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该品牌不存在！'));
            return $this->redirect()->toRoute('brand');
        }

        $form = new BrandForm();

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->brandManager->editBrand($data, $brandInfo);

                $message = sprintf($this->translator->translate('商品品牌 %s 编辑成功！'), $data['brandName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商品品牌'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('brand');
            }
        } else $form->setData($brandInfo->valuesArray());

        return ['brand' => $brandInfo, 'form' => $form];
    }

    /**
     * 批量处理
     * @return \Zend\Http\Response
     */
    public function updateAllAction()
    {
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            if(!empty($data['select_id']) and !empty($data['editAllState'])) {
                $this->brandManager->updateAllBrand($data);

                $message = $this->translator->translate('批量处理成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商品品牌'));
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }
        return $this->redirect()->toRoute('brand');
    }

    /**
     * 品牌删除
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $brandId = (int) $this->params()->fromRoute('id', -1);

        $brandInfo = $this->entityManager->getRepository(Brand::class)->findOneByBrandId($brandId);
        if($brandInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该品牌不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $oneGoods = $this->entityManager->getRepository(Goods::class)->findOneByBrandId($brandId);
        if($oneGoods) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该品牌在商品中有所引用，不能删除！'));
            return $this->adminCommon()->toReferer();
        }

        $this->brandManager->deleteBrand($brandInfo);

        $message = sprintf($this->translator->translate('商品品牌 %s 删除成功！'), $brandInfo->getBrandName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('商品品牌'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }
}