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
use Finance\Entity\Payable;
use Finance\Entity\PayableLog;
use Finance\Form\PayableLogForm;
use Finance\Form\SearchPayableForm;
use Finance\Service\PayableLogManager;
use Finance\Service\PayableManager;
use Purchase\Entity\Order;
use Purchase\Entity\WarehouseOrder;
use Purchase\Entity\WarehouseOrderGoods;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

/**
 * 支付账款
 * Class PayableController
 * @package Finance\Controller
 */
class PayableController extends AbstractActionController
{
    private $translator;
    private $entityManager;

    private $payableLogManager;
    private $payableManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        PayableLogManager $payableLogManager,
        PayableManager  $payableManager
    )
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;

        $this->payableLogManager    = $payableLogManager;
        $this->payableManager       = $payableManager;
    }

    /**
     * 应付账款列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];
        $searchForm = new SearchPayableForm();
        $searchForm->get('payment_code')->setValueOptions(Common::payment($this->translator));
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }
        $array['searchForm'] = $searchForm;

        $query = $this->entityManager->getRepository(Payable::class)->findPayableList($search);
        $array['PayableList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 付款操作
     * @return array|\Zend\Http\Response
     */
    public function addPayableAction()
    {
        $array = [];
        $payableId = (int) $this->params()->fromRoute('id', -1);
        $payableInfo = $this->entityManager->getRepository(Payable::class)->findOneByPayableId($payableId);
        if($payableInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该付款信息不存在！'));
            return $this->redirect()->toRoute('finance-payable');
        }
        if($payableInfo->getPaymentAmount() > 0 and $payableInfo->getPaymentAmount() == $payableInfo->getFinishAmount()) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('已经付款完成！'));
            return $this->redirect()->toRoute('finance-payable');
        }

        $form = new PayableLogForm(($payableInfo->getPaymentAmount() - $payableInfo->getFinishAmount()));
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
                    $this->payableManager->updatePayableFinishAmount($payableInfo->getFinishAmount() + $data['payLogAmount'], $payableInfo);
                    $this->payableLogManager->addPayableLog($data, $payableInfo, $this->adminSession('admin_id'));

                    $this->entityManager->commit();

                    $message = $payableInfo->getPOrderSn().$this->translator->translate('付款操作成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('应付账款'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->flashMessenger()->addWarningMessage($payableInfo->getPOrderSn().$this->translator->translate('付款操作失败！'));
                    $this->entityManager->rollback();
                }

                return $this->redirect()->toRoute('finance-payable');
            }
        }

        $array['form'] = $form;

        $array['payableInfo'] = $payableInfo;

        return $array;
    }

    public function payableLogAction()
    {
        $array = [];

        $payableId = (int) $this->params()->fromRoute('id', -1);
        $payableInfo = $this->entityManager->getRepository(Payable::class)->findOneByPayableId($payableId);
        if($payableInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该付款信息不存在！'));
            return $this->redirect()->toRoute('finance-payable');
        }
        $array['payableInfo'] = $payableInfo;

        $array['payableLogList'] = $this->entityManager->getRepository(PayableLog::class)->findBy(['payableId' => $payableId], ['payLogPaytime' => 'DESC', 'payLogAddtime' => 'DESC']);

        return $array;
    }

    /**
     * 应付账款详情显示
     * @return array|\Zend\Http\Response
     */
    public function showAction()
    {
        $array = [];
        $payableId = (int) $this->params()->fromRoute('id', -1);
        $payableInfo = $this->entityManager->getRepository(Payable::class)->findOneByPayableId($payableId);
        if($payableInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该付款信息不存在！'));
            return $this->redirect()->toRoute('finance-payable');
        }
        $array['payableInfo']       = $payableInfo;
        $array['warehouseOrder']    = $this->entityManager->getRepository(WarehouseOrder::class)->findOneByWarehouseOrderId($payableInfo->getWarehouseOrderId());
        $array['orderInfo']         = $this->entityManager->getRepository(Order::class)->findOneByPOrderId($payableInfo->getPOrderId());
        $array['orderGoods']        = $this->entityManager->getRepository(WarehouseOrderGoods::class)->findBy(['warehouseOrderId' => $payableInfo->getWarehouseOrderId()]);
        $array['payableLogList']    = $this->entityManager->getRepository(PayableLog::class)->findBy(['payableId' => $payableId], ['payLogPaytime' => 'DESC', 'payLogAddtime' => 'DESC']);

        return $array;
    }
}