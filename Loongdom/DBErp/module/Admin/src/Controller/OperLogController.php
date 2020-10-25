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
use Admin\Entity\OperLog;
use Admin\Form\SearchOperLogForm;
use Admin\Service\OperlogManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class OperLogController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $operLogManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        OperlogManager  $operlogManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->operLogManager   = $operlogManager;
    }

    /**
     * 操作日志列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchForm = new SearchOperLogForm();
        $groupList  = ['' => $this->translator->translate('操作者级别')];
        $group      = $this->entityManager->getRepository(AdminUserGroup::class)->findBy([], ['adminGroupId' => 'ASC']);
        if($group) {
            foreach ($group as $value) {
                $groupList[$value->getAdminGroupName()] = $value->getAdminGroupName();
            }
        }
        $searchForm->get('log_oper_user_group')->setValueOptions($groupList);

        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }

        $query      = $this->entityManager->getRepository(OperLog::class)->findOperLogAll($search);
        $paginator  = $this->adminCommon()->erpPaginator($query, $page);

        return ['operLogList' => $paginator, 'searchForm' => $searchForm];
    }

    /**
     * 清除日志
     * @return mixed
     */
    public function clearOperLogAction()
    {
        if ($this->getRequest()->isPost()) {
            $clearTime = (int) $this->params()->fromPost('clear_time');
            if($clearTime > 0) {
                $clearTime = time() - 3600 * 24 * $clearTime;
                $this->operLogManager->clearOperLog($clearTime);

                $message = $this->translator->translate('操作记录删除成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('操作日志'));
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }

        return $this->adminCommon()->toReferer();
    }
}