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

namespace Store\Controller;

use Doctrine\ORM\EntityManager;
use Purchase\Entity\OrderGoods;
use Purchase\Entity\PurchaseGoodsPriceLog;
use Sales\Entity\SalesOrderGoods;
use Store\Entity\Goods;
use Store\Entity\WarehouseGoods;
use Store\Form\GoodsForm;
use Store\Form\SearchGoodsForm;
use Store\Service\GoodsCategoryManager;
use Store\Service\GoodsManager;
use Zend\Filter\StaticFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class GoodsController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $goodsCategoryManager;
    private $goodsManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        GoodsCategoryManager $goodsCategoryManager,
        GoodsManager    $goodsManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->goodsCategoryManager = $goodsCategoryManager;
        $this->goodsManager     = $goodsManager;
    }

    /**
     * 商品列表
     * @return mixed
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchForm = new SearchGoodsForm();
        $searchForm->get('goods_category_id')->setValueOptions($this->storeCommon()->categoryListOptions($this->translator->translate('商品分类')));
        $searchForm->get('brand_id')->setValueOptions($this->storeCommon()->brandListOptions($this->translator->translate('商品品牌')));
        if($this->getRequest()->isGet()) {
            $data = $this->params()->fromQuery();
            $searchForm->setData($data);
            if($searchForm->isValid()) $search = $searchForm->getData();
        }

        $query = $this->entityManager->getRepository(Goods::class)->findAllGoods($search);
        $goodsList = $this->adminCommon()->erpPaginator($query, $page);

        return ['goodsList' => $goodsList, 'searchForm' => $searchForm];
    }

    /**
     * 添加商品
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new GoodsForm($this->entityManager);

        $form->get('goodsCategoryId')->setValueOptions($this->storeCommon()->categoryListOptions($this->translator->translate('选择商品分类')));
        $form->get('brandId')->setValueOptions($this->storeCommon()->brandListOptions());
        $form->get('unitId')->setValueOptions($this->storeCommon()->unitOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $goods = $this->goodsManager->addGoods($data, $this->adminSession('admin_id'));
                $this->getEventManager()->trigger('goods.add.post', $this, $goods);

                $message = sprintf($this->translator->translate('商品 %s 添加成功！'), $data['goodsName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商品'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('goods');

            }
        }

        return ['form' => $form];
    }

    /**
     * 编辑商品
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $goodsId = (int) $this->params()->fromRoute('id', -1);

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
        if($goodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品不存在！'));
            return $this->redirect()->toRoute('goods');
        }

        $form = new GoodsForm($this->entityManager, $goodsInfo);

        $form->get('goodsCategoryId')->setValueOptions($this->storeCommon()->categoryListOptions($this->translator->translate('选择商品分类')));
        $form->get('brandId')->setValueOptions($this->storeCommon()->brandListOptions());
        $form->get('unitId')->setValueOptions($this->storeCommon()->unitOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if($form->isValid()) {
                $data = $form->getData();

                $this->goodsManager->editGoods($data, $goodsInfo);
                $this->getEventManager()->trigger('goods.edit.post', $this, $goodsInfo);

                $message = sprintf($this->translator->translate('商品 %s 编辑成功！'), $data['goodsName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商品'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('goods');
            }
        } else $form->setData($goodsInfo->valuesArray());

        return ['goods' => $goodsInfo, 'form' => $form];
    }

    /**
     * 商品名称检索，ajax输出
     * @return JsonModel
     */
    public function autoCompleteGoodsSearchAction()
    {
        $array = [];

        $query = StaticFilter::execute($this->request->getQuery('query', ''), 'StripTags');
        $query = StaticFilter::execute($query, 'HtmlEntities');

        $goodsSearch = $this->entityManager->getRepository(Goods::class)->findGoodsNameSearch($query);
        if($goodsSearch) {
            foreach ($goodsSearch as $item) {
                $array[] = ['id'=>$item['goodsId'], 'label'=>$item['goodsName'] . (!empty($item['goodsSpec']) ? ' - '.$item['goodsSpec'] : '')];
            }
        }
        return new JsonModel($array);
    }

    /**
     * 商品id检索，ajax输出
     * @return JsonModel
     */
    public function goodsIdSearchAction()
    {
        $array = ['state' => 'false'];

        $goodsId = (int) $this->request->getPost('goodsId', 0);
        $warehouseId = (int) $this->request->getPost('warehouseId', 0);

        if($goodsId > 0) {
            $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
            if($goodsInfo) {
                $array['state'] = 'ok';
                $array['result'] = $goodsInfo->goodsValuesArray();

                $warehouseGoodsNum  = 0;
                if($warehouseId > 0) {
                    $goodsWarehouseInfo = $this->entityManager->getRepository(WarehouseGoods::class)->findOneBy(['warehouseId' => $warehouseId, 'goodsId' => $goodsId]);
                    if($goodsWarehouseInfo) {
                        $warehouseGoodsNum = $goodsWarehouseInfo->getWarehouseGoodsStock();
                    }
                }
                $array['result']['warehouseGoodsNum'] = $warehouseGoodsNum;
            }
        }

        return new JsonModel($array);
    }

    /**
     * 删除商品
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $goodsId = (int) $this->params()->fromRoute('id', -1);

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
        if($goodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $oneOrderGoods = $this->entityManager->getRepository(OrderGoods::class)->findOneByGoodsId($goodsId);
        $oneSalesGoods = $this->entityManager->getRepository(SalesOrderGoods::class)->findOneByGoodsId($goodsId);
        if($oneOrderGoods || $oneSalesGoods) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('订单中存在该商品，不能删除！'));
            return $this->adminCommon()->toReferer();
        }

        $this->goodsManager->deleteGoods($goodsInfo);
        $this->getEventManager()->trigger('goods.del.post', $this, $goodsInfo);

        $message = sprintf($this->translator->translate('商品 %s 删除成功！'), $goodsInfo->getGoodsName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('商品'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }

    /**
     * 采购价格趋势
     * @return array|\Zend\Http\Response
     */
    public function priceTrendAction()
    {
        $goodsId = (int) $this->params()->fromRoute('id', -1);

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
        if($goodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品不存在！'));
            return $this->redirect()->toRoute('goods');
        }

        $priceTrend = $this->entityManager->getRepository(PurchaseGoodsPriceLog::class)->findBy(['goodsId' => $goodsId], ['priceLogId' => 'DESC']);
        $priceArray = [];
        if(!empty($priceTrend)) {
            foreach ($priceTrend as $priceValue) {
                $priceArray['price'][]  = number_format($priceValue->getGoodsPrice(), 2, '.', '');
                $priceArray['date'][]   = "'" . date("Y-m-d H:i", $priceValue->getLogTime()) ."'";
            }
        }

        return ['goodsInfo' => $goodsInfo, 'priceArray' => $priceArray];
    }

    /**
     * 单个商品在仓库中的分布
     * @return array|\Zend\Http\Response
     */
    public function goodsWarehouseAction()
    {
        $goodsId = (int) $this->params()->fromRoute('id', -1);

        $goodsInfo = $this->entityManager->getRepository(Goods::class)->findOneByGoodsId($goodsId);
        if($goodsInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品不存在！'));
            return $this->redirect()->toRoute('goods');
        }

        //$warehouseGoodsList = $this->entityManager->getRepository(WarehouseGoods::class)->findBy(['goodsId' => $goodsId]);
        $warehouseGoodsList = $this->entityManager->getRepository(WarehouseGoods::class)->findWarehouseGoods($goodsId);
        $warehouseArray = [];
        if(!empty($warehouseGoodsList)) {
            foreach ($warehouseGoodsList as $value) {
                $warehouseArray['title'][] = "'" . $value->getOneWarehouse()->getWarehouseName() . "'";
                $warehouseArray['value'][] = "{value:".$value->getWarehouseGoodsStock().", name:'".$value->getOneWarehouse()->getWarehouseName()."'}";
            }
        }

        return ['goodsInfo' => $goodsInfo, 'warehouseGoods'=>$warehouseGoodsList, 'warehouseArray' => $warehouseArray];
    }

    /**
     * ajax获取商品列表
     * @return ViewModel
     */
    public function ajaxGoodsSearchAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true);

        $page = (int) $this->params()->fromQuery('page', 1);

        $search = [];
        $searchGoodsName = trim($this->params()->fromQuery('searchGoodsName'));
        if(!empty($searchGoodsName)) {
            $searchGoodsName = StaticFilter::execute($searchGoodsName, 'StripTags');
            $searchGoodsName = StaticFilter::execute($searchGoodsName, 'HtmlEntities');
            $search['goods_name'] = $searchGoodsName;
        }
        $query = $this->entityManager->getRepository(Goods::class)->findAllGoods($search);
        $goodsList = $this->adminCommon()->erpPaginator($query, $page);

        return $view->setVariables(['goodsList' => $goodsList, 'searchGoodsName' => $searchGoodsName]);
    }
}