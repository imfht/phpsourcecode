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

use Admin\Entity\AdminUserGroup;
use Admin\Form\AdminUserGroupForm;
use Admin\Service\AdminUserGroupManager;
use Admin\Service\AdminUserManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class AdminGroupController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $adminUserManager;
    private $adminGroupManager;
    private $permissionArray;

    public function __construct(
        Translator              $translator,
        EntityManager           $entityManager,
        AdminUserManager        $adminUserManager,
        AdminUserGroupManager   $adminGroupManager,
        $permissionArray
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->adminUserManager = $adminUserManager;
        $this->adminGroupManager= $adminGroupManager;

        $this->permissionArray = $permissionArray;
    }

    public function indexAction()
    {
        return $this->redirect()->toRoute('admin-group', ['action'=> 'adminGroupList']);
    }

    /**
     * 管理员组列表
     * @return array
     */
    public function adminGroupListAction()
    {
        $adminUserGroup = $this->entityManager->getRepository(AdminUserGroup::class)->findAll();

        return ['adminGroup'=>$adminUserGroup];
    }

    /**
     * 添加管理员组
     * @return array|\Zend\Http\Response
     */
    public function addAdminGroupAction()
    {
        $array = [];

        $array['form'] = new AdminUserGroupForm();
        $form = $array['form'];

        $array['permissionArray'] = $this->permissionArray;
        $valueArray = [];
        foreach ($this->permissionArray as $key => $permission) {
            foreach ($permission['controllers'] as $controllerKey => $controllerValue) {
                foreach ($controllerValue['action'] as $value) {
                    $valueArray[str_replace('\\', '_', $controllerKey). '_' . $value] = $controllerValue['actionNames'][$value];
                }
            }
        }
        $form->get('adminGroupPermission')->setValueOptions($valueArray);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->adminGroupManager->addAdminGroup($data);

                $message = sprintf($this->translator->translate('管理员组 %s 添加成功！'), $data['adminGroupName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('管理员组'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('admin-group', ['action'=> 'adminGroupList']);
            }
        }

        return $array;
    }


    /**
     * 编辑管理员组
     * @return array|\Zend\Http\Response
     */
    public function editAdminGroupAction()
    {
        $adminGroupId = (int) $this->params()->fromRoute('id', -1);

        if($adminGroupId == 1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该管理员组不可编辑！'));
            return $this->redirect()->toRoute('admin-group', ['action'=> 'adminGroupList']);
        }

        $adminGroup = $this->entityManager->getRepository(AdminUserGroup::class)->findOneByAdminGroupId($adminGroupId);
        if($adminGroup == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该管理员组不存在！'));
            return $this->redirect()->toRoute('admin-group', ['action'=> 'adminGroupList']);
        }

        $form = new AdminUserGroupForm();

        $valueArray = [];
        foreach ($this->permissionArray as $key => $permission) {
            foreach ($permission['controllers'] as $controllerKey => $controllerValue) {
                foreach ($controllerValue['action'] as $value) {
                    $valueArray[str_replace('\\', '_', $controllerKey). '_' . $value] = $controllerValue['actionNames'][$value];
                }
            }
        }
        $form->get('adminGroupPermission')->setValueOptions($valueArray);

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->adminGroupManager->updateAdminGroup($adminGroup, $data);

                $message = sprintf($this->translator->translate('管理员组 %s 编辑成功！'), $data['adminGroupName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('管理员组'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('admin-group', ['action'=> 'adminGroupList']);
            }
        } else $form->setData([
            'adminGroupId'  => $adminGroup->getAdminGroupId(),
            'adminGroupName'=> $adminGroup->getAdminGroupName()
        ]);

        return ['group'=>$adminGroup, 'form'=>$form, 'permissionArray' => $this->permissionArray];
    }

    /**
     * 删除管理员组
     * @return \Zend\Http\Response
     */
    public function deleteAdminGroupAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->redirect()->toRoute('admin-group', ['action'=> 'adminGroupList']);

        $adminGroupId = (int) $this->params()->fromRoute('id', -1);
        if($adminGroupId == 1) {
            $this->flashMessenger()->addErrorMessage($this->translator->translate('该管理员组不能删除！'));
            return $this->redirect()->toRoute('admin-group', ['action'=> 'adminGroupList']);
        }

        $adminGroup = $this->entityManager->getRepository(AdminUserGroup::class)->findOneByAdminGroupId($adminGroupId);
        if($adminGroup == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该管理员组不存在！'));
            return $this->redirect()->toRoute('admin-group', ['action'=> 'adminGroupList']);
        }

        $delState = $this->adminGroupManager->deleteAdminGroup($adminGroup);

        if($delState) {
            $message = sprintf($this->translator->translate('管理员组 %s 删除成功！'), $adminGroup->getAdminGroupName());
            $this->adminCommon()->addOperLog($message, $this->translator->translate('管理员组'));
            $this->flashMessenger()->addSuccessMessage($message);
        }
        else $this->flashMessenger()->addErrorMessage(sprintf($this->translator->translate('管理员组 %s 删除失败！该组不存在或该组下有管理员'), $adminGroup->getAdminGroupName()));

        return $this->adminCommon()->toReferer();
    }
}