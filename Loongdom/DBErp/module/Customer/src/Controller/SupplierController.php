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

use Customer\Entity\Supplier;
use Customer\Form\SearchSupplierForm;
use Customer\Form\SupplierForm;
use Customer\Service\SupplierCategoryManager;
use Customer\Service\SupplierManager;
use Doctrine\ORM\EntityManager;
use Purchase\Entity\Order;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class SupplierController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $supplierCategoryManager;
    private $supplierManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        SupplierCategoryManager $supplierCategoryManager,
        SupplierManager $supplierManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->supplierCategoryManager  = $supplierCategoryManager;
        $this->supplierManager  = $supplierManager;
    }

    /**
     * 供应商列表
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchForm = new SearchSupplierForm();
        $searchForm->get('supplier_category_id')->setValueOptions($this->customerCommon()->supplierCategoryOptions());
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(Supplier::class)->findAllSupplier($search);
        $array['supplierList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 供应商添加
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new SupplierForm($this->entityManager);
        $form->get('supplierCategoryId')->setValueOptions($this->customerCommon()->supplierCategoryOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->supplierManager->addSupplier($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('供应商 %s 添加成功！'), $data['supplierName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('供应商'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('supplier');
            }
        }

        return ['form' => $form, 'region' => $this->adminCommon()->getRegionSub()];
    }

    /**
     * 编辑供应商
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $supplierId = (int) $this->params()->fromRoute('id', -1);

        $supplierInfo = $this->entityManager->getRepository(Supplier::class)->findOneBySupplierId($supplierId);
        if($supplierInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该供应商不存在！'));
            return $this->redirect()->toRoute('supplier');
        }

        $form = new SupplierForm($this->entityManager, $supplierInfo);
        $form->get('supplierCategoryId')->setValueOptions($this->customerCommon()->supplierCategoryOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->supplierManager->editSupplier($data, $supplierInfo);

                $message = sprintf($this->translator->translate('供应商 %s 编辑成功！'), $data['supplierName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('供应商'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('supplier');
            }
        } else $form->setData($supplierInfo->valuesArray());

        return ['supplier' => $supplierInfo, 'form' => $form, 'region' => $this->adminCommon()->getRegionSub()];
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
                $this->supplierManager->editAllSupplier($data);

                $message = $this->translator->translate('批量处理成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('供应商'));
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }
        return $this->redirect()->toRoute('supplier');
    }

    /**
     * 删除供应商
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $supplierId = (int) $this->params()->fromRoute('id', -1);

        $supplierInfo = $this->entityManager->getRepository(Supplier::class)->findOneBySupplierId($supplierId);
        if($supplierInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该供应商不存在！'));
            return $this->redirect()->toRoute('supplier');
        }

        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['supplierId' => $supplierId]);
        if($order) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('有该供应商的采购订单，不能删除！'));
            return $this->redirect()->toRoute('supplier');
        }

        $this->supplierManager->deleteSupplier($supplierInfo);

        $message = sprintf($this->translator->translate('供应商 %s 删除成功！'), $supplierInfo->getSupplierName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('供应商'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }

    /**
     * 通过供应商id检索供应商
     * @return JsonModel
     */
    public function supplierIdSearchAction()
    {
        $array = ['state' => 'false'];

        $supplierId = (int) $this->request->getPost('supplierId', 0);
        $supplierInfo = $this->entityManager->getRepository(Supplier::class)->findOneBySupplierId($supplierId);
        if($supplierInfo) {
            $array['state'] = 'ok';
            $array['result']= $supplierInfo->valuesArray();
        }

        return new JsonModel($array);
    }
}