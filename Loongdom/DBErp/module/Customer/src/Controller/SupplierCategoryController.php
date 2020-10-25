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

namespace Customer\Controller;

use Customer\Entity\SupplierCategory;
use Customer\Form\SupplierCategoryForm;
use Customer\Service\SupplierCategoryManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class SupplierCategoryController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $supplierCategoryManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        SupplierCategoryManager $supplierCategoryManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->supplierCategoryManager = $supplierCategoryManager;
    }

    /**
     * 供应商分类列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $array = [];

        $array['supplierCategory'] = $this->entityManager->getRepository(SupplierCategory::class)->findBy([], ['supplierCategorySort' => 'ASC']);

        return $array;
    }

    /**
     * 添加供应商分类
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new SupplierCategoryForm($this->entityManager);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->supplierCategoryManager->addSupplierCategory($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('供应商分类 %s 添加成功！'), $data['supplierCategoryName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('供应商分类'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('supplier-category');
            }
        }

        return ['form' => $form];
    }

    /**
     * 编辑供应商分类
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $categoryId = (int) $this->params()->fromRoute('id', -1);

        $supplierCategory = $this->entityManager->getRepository(SupplierCategory::class)->findOneBySupplierCategoryId($categoryId);
        if($supplierCategory == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该供应商分类不存在！'));
            return $this->redirect()->toRoute('supplier-category');
        }

        $form = new SupplierCategoryForm($this->entityManager, $supplierCategory);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->supplierCategoryManager->editSupplierCategory($data, $supplierCategory);

                $message = sprintf($this->translator->translate('供应商分类 %s 编辑成功！'), $data['supplierCategoryName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('供应商分类'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('supplier-category');
            }
        } else $form->setData($supplierCategory->valuesArray());

        return ['supplierCategory' => $supplierCategory, 'form' => $form];
    }

    /**
     * 批量修改
     * @return \Zend\Http\Response
     */
    public function updateAllAction()
    {
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            if(!empty($data['select_id']) and !empty($data['editAllState'])) {
                $this->supplierCategoryManager->editAllSupplierCategory($data);

                $message = $this->translator->translate('批量处理成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('供应商分类'));
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }
        return $this->redirect()->toRoute('supplier-category');
    }

    /**
     * 删除供应商分类
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $categoryId = (int) $this->params()->fromRoute('id', -1);

        $supplierCategory = $this->entityManager->getRepository(SupplierCategory::class)->findOneBySupplierCategoryId($categoryId);
        if($supplierCategory == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该供应商分类不存在！'));
            return $this->redirect()->toRoute('supplier-category');
        }

        $delState = $this->supplierCategoryManager->deleteSupplierCategory($supplierCategory);
        if($delState) {
            $message = sprintf($this->translator->translate('供应商分类 %s 删除成功！'), $supplierCategory->getSupplierCategoryName());
            $this->adminCommon()->addOperLog($message, $this->translator->translate('供应商分类'));
            $this->flashMessenger()->addSuccessMessage($message);
        }
        else $this->flashMessenger()->addErrorMessage(sprintf($this->translator->translate('供应商分类 %s 删除失败！其下还有供应商信息'), $supplierCategory->getSupplierCategoryName()));

        return $this->adminCommon()->toReferer();
    }
}