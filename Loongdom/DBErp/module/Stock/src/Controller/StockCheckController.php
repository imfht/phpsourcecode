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

namespace Stock\Controller;

use Doctrine\ORM\EntityManager;
use Stock\Entity\StockCheck;
use Stock\Entity\StockCheckGoods;
use Stock\Form\StockCheckForm;
use Stock\Form\StockCheckGoodsForm;
use Stock\Service\StockCheckGoodsManager;
use Stock\Service\StockCheckManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;

class StockCheckController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $stockCheckManager;
    private $stockCheckGoodsManager;

    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        StockCheckManager $stockCheckManager,
        StockCheckGoodsManager $stockCheckGoodsManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->stockCheckManager= $stockCheckManager;
        $this->stockCheckGoodsManager = $stockCheckGoodsManager;
    }

    /**
     * 库存盘点列表
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $array= [];

        $search = [];

        $query = $this->entityManager->getRepository(StockCheck::class)->findStockCheckList($search);
        $array['stockCheckList'] = $this->adminCommon()->erpPaginator($query, $page);

        return $array;
    }

    /**
     * 添加库存盘点
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form           = new StockCheckForm($this->entityManager);
        $stockGoodsForm = new StockCheckGoodsForm($this->entityManager);

        $form->get('warehouseId')->setValueOptions($this->storeCommon()->warehouseListOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $stockGoodsForm->setData($data);
            if($form->isValid() && $stockGoodsForm->isValid()) {
                $data = $form->getData();
                $stockCheckGoodsData = $stockGoodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $stockCheckInfo = $this->stockCheckManager->addStockCheck($data, $stockCheckGoodsData, $this->adminSession('admin_id'));
                    $this->stockCheckGoodsManager->addStockCheckGoods($stockCheckGoodsData, $data['warehouseId'], $stockCheckInfo->getStockCheckId());

                    $this->entityManager->commit();

                    $message = $stockCheckInfo->getStockCheckSn() . $this->translator->translate('库存盘点添加成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('库存盘点'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('仓库盘点失败！'));
                }
                return $this->redirect()->toRoute('stock-check');
            }
        } else $form->get('stockCheckSn')->setValue($this->stockCommon()->createStockCheckOrderSn());

        return ['form' => $form, 'stockGoodsForm' => $stockGoodsForm];
    }

    /**
     * 编辑库存盘点
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $stockCheckId = (int) $this->params()->fromRoute('id', -1);
        $stockCheckInfo = $this->entityManager->getRepository(StockCheck::class)->findOneBy(['stockCheckId' => $stockCheckId]);
        if($stockCheckInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库存盘点单不存在！'));
            return $this->redirect()->toRoute('stock-check');
        }
        if($stockCheckInfo->getStockCheckState() == 1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库存盘点已经完成！'));
            return $this->redirect()->toRoute('stock-check');
        }

        $form           = new StockCheckForm($this->entityManager, $stockCheckInfo);
        $stockGoodsForm = new StockCheckGoodsForm($this->entityManager);

        $form->get('warehouseId')->setValueOptions($this->storeCommon()->warehouseListOptions());
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            $stockGoodsForm->setData($data);
            if($form->isValid() && $stockGoodsForm->isValid()) {
                $data = $form->getData();
                $stockCheckGoodsData = $stockGoodsForm->getData();

                $this->entityManager->beginTransaction();
                try {
                    $this->stockCheckManager->updateStockCheck($data, $stockCheckGoodsData, $stockCheckInfo);
                    $this->stockCheckGoodsManager->editStockCheckGoods($stockCheckGoodsData, $stockCheckId, $stockCheckInfo->getWarehouseId());

                    $this->entityManager->commit();

                    $message = $stockCheckInfo->getStockCheckSn() . $this->translator->translate('库存盘点编辑成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('库存盘点'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('库存盘点编辑失败！'));
                }
                return $this->redirect()->toRoute('stock-check');
            }
        } else {
            $form->setData($stockCheckInfo->valuesArray());
            $form->setData(['stockCheckTime' => date("Y-m-d", $stockCheckInfo->getStockCheckTime())]);
        }

        $stockCheckGoods = $this->entityManager->getRepository(StockCheckGoods::class)->findBy(['stockCheckId' => $stockCheckId], ['stockCheckGoodsId' => 'ASC']);

        return ['form' => $form, 'stockGoodsForm' => $stockGoodsForm, 'stockCheckInfo' => $stockCheckInfo, 'stockCheckGoods' => $stockCheckGoods];
    }

    /**
     * 查看库存盘点
     * @return array|\Zend\Http\Response
     */
    public function viewAction()
    {
        $stockCheckId = (int) $this->params()->fromRoute('id', -1);
        $stockCheckInfo = $this->entityManager->getRepository(StockCheck::class)->findOneBy(['stockCheckId' => $stockCheckId]);
        if($stockCheckInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库存盘点单不存在！'));
            return $this->redirect()->toRoute('stock-check');
        }

        $stockCheckGoods = $this->entityManager->getRepository(StockCheckGoods::class)->findBy(['stockCheckId' => $stockCheckId], ['stockCheckGoodsId' => 'ASC']);

        return ['stockCheckInfo' => $stockCheckInfo, 'stockCheckGoods' => $stockCheckGoods];
    }

    /**
     * 库存盘点确认
     * @return \Zend\Http\Response
     */
    public function confirmAction()
    {
        $stockCheckId = (int) $this->params()->fromRoute('id', -1);
        $stockCheckInfo = $this->entityManager->getRepository(StockCheck::class)->findOneBy(['stockCheckId' => $stockCheckId]);
        if($stockCheckInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库存盘点单不存在！'));
            return $this->redirect()->toRoute('stock-check');
        }
        if($stockCheckInfo->getStockCheckState() == 1) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库存盘点已经完成，无需确认！'));
            return $this->redirect()->toRoute('stock-check');
        }

        $stockCheckGoods = $this->entityManager->getRepository(StockCheckGoods::class)->findBy(['stockCheckId' => $stockCheckId]);
        if($stockCheckGoods == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该库存盘点单无商品！'));
            return $this->redirect()->toRoute('stock-check');
        }

        $this->entityManager->beginTransaction();
        try {
            $this->stockCheckManager->updateStockCheckState(1, $stockCheckInfo);
            $this->getEventManager()->trigger('stock-check.update.post', $this, $stockCheckInfo);

            $this->entityManager->commit();

            $message = $stockCheckInfo->getStockCheckSn() . $this->translator->translate('库存盘点确认完成！');
            $this->adminCommon()->addOperLog($message, $this->translator->translate('库存盘点'));
            $this->flashMessenger()->addSuccessMessage($message);
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->flashMessenger()->addWarningMessage($this->translator->translate('库存盘点确认失败！'));
        }

        return $this->adminCommon()->toReferer();
    }

    /**
     * 删除盘点单
     * @return mixed
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $stockCheckId = (int) $this->params()->fromRoute('id', -1);
        $stockCheckInfo = $this->entityManager->getRepository(StockCheck::class)->findOneBy(['stockCheckId' => $stockCheckId]);

        if($stockCheckInfo) {
            if($stockCheckInfo->getStockCheckState() == 1) $this->flashMessenger()->addWarningMessage($this->translator->translate('该库存盘点已经完成，无法删除！'));
            else {
                $this->entityManager->beginTransaction();
                try {
                    $this->stockCheckManager->deleteStockCheck($stockCheckInfo);
                    $this->stockCheckGoodsManager->deleteStockCheckIdGoods($stockCheckId);

                    $this->entityManager->commit();

                    $message = $stockCheckInfo->getStockCheckSn() . $this->translator->translate('库存盘点单删除成功！');
                    $this->adminCommon()->addOperLog($message, $this->translator->translate('库存盘点'));
                    $this->flashMessenger()->addSuccessMessage($message);
                } catch (\Exception $e) {
                    $this->entityManager->rollback();
                    $this->flashMessenger()->addWarningMessage($this->translator->translate('库存盘点单删除失败！'));
                }
            }
        } else $this->flashMessenger()->addWarningMessage($this->translator->translate('该库存盘点不存在！'));

        return $this->adminCommon()->toReferer();
    }

    /**
     * 删除盘点商品
     * @return JsonModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delStockCheckGoodsAction()
    {
        $array = ['state' => 'false'];

        $goodsId        = (int) $this->request->getPost('goodsId', 0);
        $stockCheckId   = (int) $this->request->getPost('stockCheckId', 0);
        if($goodsId > 0 && $stockCheckId > 0) {
            $stockCheckInfo = $this->entityManager->getRepository(StockCheck::class)->findOneBy(['stockCheckId' => $stockCheckId, 'stockCheckState' => 2]);
            if($stockCheckInfo) {
                $stockGoodsInfo = $this->entityManager->getRepository(StockCheckGoods::class)->findOneBy(['stockCheckId' => $stockCheckId, 'goodsId' => $goodsId]);
                if($stockGoodsInfo) {
                    $stockCheckSn   = $stockCheckInfo->getStockCheckSn();
                    $goodsName      = $stockGoodsInfo->getGoodsName();

                    $this->stockCheckGoodsManager->deleteStockCheckGoods($stockGoodsInfo);
                    $this->adminCommon()->addOperLog($stockCheckSn . $this->translator->translate('删除商品') . '：' . $goodsName, $this->translator->translate('库存盘点'));
                    $array['state'] = 'ok';
                }
            }
        }

        return new JsonModel($array);
    }
}