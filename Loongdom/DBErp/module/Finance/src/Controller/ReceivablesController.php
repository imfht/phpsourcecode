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

namespace Finance\Controller;

use Admin\Data\Common;
use Doctrine\ORM\EntityManager;
use Finance\Entity\Receivable;
use Finance\Entity\ReceivableLog;
use Finance\Form\ReceivableLogForm;
use Finance\Form\SearchReceivableForm;
use Finance\Service\ReceivableLogManager;
use Finance\Service\ReceivableManager;
use Sales\Entity\SalesOrder;
use Sales\Entity\SalesOrderGoods;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

/**
 * 应收账款
 * Class ReceivablesController
 * @package Finance\Controller
 */
class ReceivablesController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $receivableManager;
    private $receivableLogManager;

    public function __construct(
        Translator  $translator,
        EntityManager $entityManager,
        ReceivableManager $receivableManager,
        ReceivableLogManager $receivableLogManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->receivableManager= $receivableManager;
        $this->receivableLogManager = $receivableLogManager;
    }

    /**
     * 应收账款列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new SearchReceivableForm();
        $searchForm->get('receivable_code')->setValueOptions(Common::receivable($this->translator));
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(Receivable::class)->findReceivablesList($search);
        $array['receivablesList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加收款记录
     * @return array|\Zend\Http\Response
     */
    public function addReceivableAction()
    {
        $array = [];

        $receivableId = (int) $this->params()->fromRoute('id', -1);
        $receivableInfo = $this->entityManager->getRepository(Receivable::class)->findOneByReceivableId($receivableId);
        if($receivableInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该收款信息不存在！'));
            return $this->redirect()->toRoute('accounts-receivable');
        }
        if($receivableInfo->getReceivableAmount() > 0 and $receivableInfo->getReceivableAmount() == $receivableInfo->getFinishAmount()) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('已经收款完成！'));
            return $this->redirect()->toRoute('accounts-receivable');
        }

        $form = new ReceivableLogForm(($receivableInfo->getReceivableAmount() - $receivableInfo->getFinishAmount()));
        if($this->getRequest()->isPost()) {
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();
                $this->entityManager->beginTransaction();
                try {
                    $this->receivableManager->updateReceivableFinishAmount($receivableInfo->getFinishAmount() + $data['receivableLogAmount'], $receivableInfo);
                    $this->receivableLogManager->addReceivableLog($data, $receivableInfo, $this->adminSession('admin_id'));

                    $message = $receivableInfo->getSalesOrderSn().$this->translator->translate('收款操作成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('应收账款'));
                    $this->flashMessenger()->addSuccessMessage($message);

                    $this->entityManager->commit();
                } catch (\Exception $e) {
                    $this->flashMessenger()->addWarningMessage($receivableInfo->getSalesOrderSn().$this->translator->translate('收款操作失败！'));
                    $this->entityManager->rollback();
                }
                return $this->redirect()->toRoute('accounts-receivable');
            }
        }

        $array['form'] = $form;

        $array['receivableInfo'] = $receivableInfo;

        return $array;
    }

    public function showAction()
    {
        $array = [];

        $receivableId = (int) $this->params()->fromRoute('id', -1);
        $receivableInfo = $this->entityManager->getRepository(Receivable::class)->findOneByReceivableId($receivableId);
        if($receivableInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该收款信息不存在！'));
            return $this->redirect()->toRoute('accounts-receivable');
        }

        $array['receivableInfo']    = $receivableInfo;
        $array['orderInfo']         = $this->entityManager->getRepository(SalesOrder::class)->findOneBySalesOrderId($receivableInfo->getSalesOrderId());
        $array['orderGoods']        = $this->entityManager->getRepository(SalesOrderGoods::class)->findBy(['salesOrderId' => $receivableInfo->getSalesOrderId()]);
        $array['receivableLogList'] = $this->entityManager->getRepository(ReceivableLog::class)->findBy(['receivableId' => $receivableId], ['receivableLogTime' => 'DESC', 'receivableAddTime' => 'DESC']);

        return $array;
    }

    public function receivableLogAction()
    {
        $array = [];

        $receivableId = (int) $this->params()->fromRoute('id', -1);
        $receivableInfo = $this->entityManager->getRepository(Receivable::class)->findOneByReceivableId($receivableId);
        if($receivableInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该收款信息不存在！'));
            return $this->redirect()->toRoute('accounts-receivable');
        }
        $array['receivableInfo'] = $receivableInfo;

        $array['receivableLogList'] = $this->entityManager->getRepository(ReceivableLog::class)->findBy(['receivableId' => $receivableId], ['receivableLogTime' => 'DESC', 'receivableAddTime' => 'DESC']);

        return $array;
    }
}