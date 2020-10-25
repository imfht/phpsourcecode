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

use Customer\Entity\Customer;
use Customer\Form\CustomerForm;
use Customer\Form\SearchCustomerForm;
use Customer\Service\CustomerManager;
use Doctrine\ORM\EntityManager;
use Sales\Entity\SalesOrder;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class CustomerController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $customerManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        CustomerManager $customerManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->customerManager  = $customerManager;
    }

    /**
     * 客户列表
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchForm = new SearchCustomerForm();
        $searchForm->get('customer_category_id')->setValueOptions($this->customerCommon()->customerCategoryOptions());
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(Customer::class)->findAllCustomer($search);
        $array['customerList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加客户
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new CustomerForm($this->entityManager);

        $form->get('customerCategoryId')->setValueOptions($this->customerCommon()->customerCategoryOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->customerManager->addCustomer($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('客户 %s 添加成功！'), $data['customerName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('客户'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('customer');
            }
        }

        return ['form' => $form, 'region' => $this->adminCommon()->getRegionSub()];
    }

    /**
     * 编辑客户
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $customerId = (int) $this->params()->fromRoute('id', -1);

        $customerInfo = $this->entityManager->getRepository(Customer::class)->findOneByCustomerId($customerId);
        if($customerInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该客户不存在！'));
            return $this->redirect()->toRoute('customer');
        }

        $form = new CustomerForm($this->entityManager, $customerInfo);
        $form->get('customerCategoryId')->setValueOptions($this->customerCommon()->customerCategoryOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->customerManager->editCustomer($data, $customerInfo);

                $message = sprintf($this->translator->translate('客户 %s 编辑成功！'), $data['customerName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('客户'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('customer');
            }
        } else $form->setData($customerInfo->valuesArray());

        return ['customer' => $customerInfo, 'form' => $form, 'region' => $this->adminCommon()->getRegionSub()];
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
                $this->customerManager->editAllCustomer($data);

                $message = $this->translator->translate('批量处理成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('客户'));
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }
        return $this->redirect()->toRoute('customer');
    }

    /**
     * 删除客户
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $customerId = (int) $this->params()->fromRoute('id', -1);

        $customerInfo = $this->entityManager->getRepository(Customer::class)->findOneByCustomerId($customerId);
        if($customerInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该客户不存在！'));
            return $this->redirect()->toRoute('customer');
        }

        $salesOrder = $this->entityManager->getRepository(SalesOrder::class)->findOneBy(['customerId' => $customerId]);
        if($salesOrder) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该客户有销售订单存在，不能删除！'));
            return $this->redirect()->toRoute('customer');
        }

        $this->customerManager->deleteCustomer($customerInfo);

        $message = sprintf($this->translator->translate('客户 %s 删除成功！'), $customerInfo->getCustomerName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('客户'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }

    /**
     * 通过客户id检索客户
     * @return JsonModel
     */
    public function customerIdSearchAction()
    {
        $array = ['state' => 'false'];

        $customerId = (int) $this->request->getPost('customerId', 0);
        $customerInfo = $this->entityManager->getRepository(Customer::class)->findOneByCustomerId($customerId);
        if($customerInfo) {
            $array['state'] = 'ok';
            $array['result']= $customerInfo->valuesArray();
        }

        return new JsonModel($array);
    }
}