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

use Customer\Entity\CustomerCategory;
use Customer\Form\CustomerCategoryForm;
use Customer\Service\CustomerCategoryManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class CustomerCategoryController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $customerCategoryManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        CustomerCategoryManager $customerCategoryManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->customerCategoryManager = $customerCategoryManager;
    }

    /**
     * 客户分类列表
     * @return array
     */
    public function indexAction()
    {
        $array = [];

        $array['customerCategory'] = $this->entityManager->getRepository(CustomerCategory::class)->findBy([], ['customerCategorySort' => 'ASC']);

        return $array;
    }

    /**
     * 添加客户分类
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new CustomerCategoryForm($this->entityManager);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->customerCategoryManager->addCustomerCategory($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('客户分类 %s 添加成功！'), $data['customerCategoryName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('客户分类'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('customer-category');
            }
        }

        return ['form' => $form];
    }

    /**
     * 编辑客户分类
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $categoryId = (int) $this->params()->fromRoute('id', -1);

        $customerCategory = $this->entityManager->getRepository(CustomerCategory::class)->findOneByCustomerCategoryId($categoryId);
        if($customerCategory == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该客户分类不存在！'));
            return $this->redirect()->toRoute('customer-category');
        }

        $form = new CustomerCategoryForm($this->entityManager, $customerCategory);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->customerCategoryManager->editCustomerCategory($data, $customerCategory);

                $message = sprintf($this->translator->translate('客户分类 %s 编辑成功！'), $data['customerCategoryName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('客户分类'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('customer-category');
            }
        } else $form->setData($customerCategory->valuesArray());

        return ['customerCategory' => $customerCategory, 'form' => $form];
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
                $this->customerCategoryManager->editAllCustomerCategory($data);

                $message = $this->translator->translate('批量处理成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('客户分类'));
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }
        return $this->redirect()->toRoute('customer-category');
    }

    /**
     * 客户分类删除
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $categoryId = (int) $this->params()->fromRoute('id', -1);

        $customerCategory = $this->entityManager->getRepository(CustomerCategory::class)->findOneByCustomerCategoryId($categoryId);
        if($customerCategory == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该客户分类不存在！'));
            return $this->redirect()->toRoute('customer-category');
        }

        $delState = $this->customerCategoryManager->deleteCustomerCategory($customerCategory);
        if($delState) {
            $message = sprintf($this->translator->translate('客户分类 %s 删除成功！'), $customerCategory->getCustomerCategoryName());
            $this->adminCommon()->addOperLog($message, $this->translator->translate('客户分类'));
            $this->flashMessenger()->addSuccessMessage($message);
        }
        else $this->flashMessenger()->addErrorMessage(sprintf($this->translator->translate('客户分类 %s 删除失败！其下还有客户信息'), $customerCategory->getCustomerCategoryName()));

        return $this->adminCommon()->toReferer();
    }
}