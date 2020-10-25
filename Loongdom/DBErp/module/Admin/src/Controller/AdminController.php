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

use Admin\Data\Common;
use Admin\Entity\AdminUser;
use Admin\Form\AdminUserForm;
use Admin\Form\AdminUserPasswordChangeForm;
use Admin\Form\SearchAdminForm;
use Admin\Service\AdminUserGroupManager;
use Admin\Service\AdminUserManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class AdminController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $adminUserManager;
    private $adminGroupManager;

    public function __construct(
        Translator              $translator,
        EntityManager           $entityManager,
        AdminUserManager        $adminUserManager,
        AdminUserGroupManager   $adminGroupManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->adminUserManager = $adminUserManager;
        $this->adminGroupManager= $adminGroupManager;
    }

    /**
     * 管理员列表
     * @return array
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchForm = new SearchAdminForm();
        $searchForm->get('admin_group_id')->setValueOptions($this->adminCommon()->adminGroupOptions());
        $searchForm->get('admin_state')->setValueOptions(Common::state($this->translator));
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }

        $query      = $this->entityManager->getRepository(AdminUser::class)->findAllAdmin($search);
        $paginator  = $this->adminCommon()->erpPaginator($query, $page);

        return ['adminUser' => $paginator, 'searchForm' => $searchForm];
    }

    /**
     * 管理员添加
     */
    public function addAction()
    {
        $array = [];

        $array['form'] = new AdminUserForm('add', [], $this->entityManager);
        $userForm = $array['form'];

        $userForm->get('adminGroupId')->setValueOptions($this->adminCommon()->adminGroupOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $userForm->setData($data);
            if($userForm->isValid()) {
                $data = $userForm->getData();

                $user = $this->adminUserManager->addAdminUser($data);

                $message = sprintf($this->translator->translate('管理员 %s 添加成功！'), $data['adminName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('管理员'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('admin');
            }
        }

        return $array;
    }

    /**
     * 编辑管理员
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $adminId = (int) $this->params()->fromRoute('id', -1);

        $adminUser  = $this->entityManager->getRepository(AdminUser::class)->findOneByAdminId($adminId);
        if($adminUser == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该管理员不存在！'));
            return $this->redirect()->toRoute('admin');
        }

        $form = new AdminUserForm('edit', [], $this->entityManager, $adminUser);

        $form->get('adminGroupId')->setValueOptions($this->adminCommon()->adminGroupOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->adminUserManager->updateAdminUser($adminUser, $data);

                $message = sprintf($this->translator->translate('管理员 %s 编辑成功！'), $adminUser->getAdminName());
                $this->adminCommon()->addOperLog($message, $this->translator->translate('管理员'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('admin');
            }
        } else $form->setData($adminUser->valuesArray());

        return ['user' => $adminUser, 'form' => $form];
    }

    /**
     * 管理员密码修改
     * @return array|\Zend\Http\Response
     */
    public function changePasswordAction()
    {
        $adminId = (int) $this->params()->fromRoute('id', -1);

        $adminUser = $this->entityManager->getRepository(AdminUser::class)->findOneByAdminId($adminId);
        if($adminUser == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该管理员不存在！'));
            return $this->redirect()->toRoute('admin');
        }

        $form = new AdminUserPasswordChangeForm();

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();
                $this->adminUserManager->changeAdminPassword($adminUser, $data);

                $message = sprintf($this->translator->translate('管理员 %s 密码修改成功！'), $adminUser->getAdminName());
                $this->adminCommon()->addOperLog($message, $this->translator->translate('管理员'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('admin');
            }
        }

        return ['user' => $adminUser, 'form' => $form];
    }

    /**
     * 删除账户
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->redirect()->toRoute('admin');

        $adminId = (int) $this->params()->fromRoute('id', -1);
        if($adminId <= 0 || $adminId == 1) {
            $this->flashMessenger()->addErrorMessage($this->translator->translate('不能删除创始人！'));
            return $this->redirect()->toRoute('admin');
        }

        $adminUser = $this->entityManager->getRepository(AdminUser::class)->findOneByAdminId($adminId);
        if($adminUser == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该管理员不存在！'));
            return $this->redirect()->toRoute('admin');
        }

        $this->adminUserManager->deleteUser($adminUser);

        $message = sprintf($this->translator->translate('管理员 %s 删除成功！'), $adminUser->getAdminName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('管理员'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }

}