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
use Store\Entity\Goods;
use Store\Entity\GoodsCategory;
use Store\Form\GoodsCategoryForm;
use Store\Service\GoodsCategoryManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;

class GoodsCategoryController extends AbstractActionController
{
    private $translator;
    private $entityManager;
    private $goodsCategoryManager;

    public function __construct(
        Translator      $translator,
        EntityManager   $entityManager,
        GoodsCategoryManager $goodsCategoryManager
    )
    {
        $this->translator       = $translator;
        $this->entityManager    = $entityManager;
        $this->goodsCategoryManager = $goodsCategoryManager;
    }

    /**
     * 商品分类列表
     * @return array
     */
    public function indexAction()
    {
        $goodsCategory  = $this->entityManager->getRepository(GoodsCategory::class)->findBy([], ['goodsCategoryTopId' => 'ASC', 'goodsCategorySort' => 'ASC']);
        $categoryList   = $this->storeCommon()->categoryOptions($goodsCategory);

        return ['category' => $categoryList];
    }

    /**
     * 添加商品分类
     * @return array|\Zend\Http\Response
     */
    public function addAction()
    {
        $form = new GoodsCategoryForm($this->entityManager);

        $form->get('goodsCategoryTopId')->setValueOptions($this->storeCommon()->categoryListOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->goodsCategoryManager->addGoodsCategory($data, $this->adminSession('admin_id'));

                $message = sprintf($this->translator->translate('商品分类 %s 添加成功！'), $data['goodsCategoryName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商品分类'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('goods-category');
            }
        }

        return ['form' => $form];
    }

    /**
     * 编辑商品分类
     * @return array|\Zend\Http\Response
     */
    public function editAction()
    {
        $categoryId = (int) $this->params()->fromRoute('id', -1);

        $categoryInfo = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryId($categoryId);
        if($categoryInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该商品分类不存在！'));
            return $this->redirect()->toRoute('goods-category');
        }

        $form = new GoodsCategoryForm($this->entityManager, $categoryInfo);

        $form->get('goodsCategoryTopId')->setValueOptions($this->storeCommon()->categoryListOptions());

        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $this->goodsCategoryManager->updateGoodsCategory($data, $categoryInfo);

                $message = sprintf($this->translator->translate('商品分类 %s 编辑成功！'), $data['goodsCategoryName']);
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商品分类'));
                $this->flashMessenger()->addSuccessMessage($message);

                return $this->redirect()->toRoute('goods-category');
            }
        } else $form->setData($categoryInfo->valuesArray());

        return ['form' => $form];
    }

    /**
     * 删除分类
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if(!$this->adminCommon()->validatorCsrf()) return $this->adminCommon()->toReferer();

        $categoryId = (int) $this->params()->fromRoute('id', -1);

        $categoryInfo   = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryId($categoryId);
        if($categoryInfo == null) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该分类不存在！'));
            return $this->adminCommon()->toReferer();
        }

        $subCategoryInfo = $this->entityManager->getRepository(GoodsCategory::class)->findOneByGoodsCategoryTopId($categoryId);
        if($subCategoryInfo) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该分类下还存在分类，不能删除！'));
            return $this->adminCommon()->toReferer();
        }

        $oneGoods = $this->entityManager->getRepository(Goods::class)->findOneByGoodsCategoryId($categoryId);
        if($oneGoods) {
            $this->flashMessenger()->addWarningMessage($this->translator->translate('该分类有商品存在，不能删除！'));
            return $this->adminCommon()->toReferer();
        }

        $this->goodsCategoryManager->deleteGoodsCategory($categoryInfo);

        $message = sprintf($this->translator->translate('分类 %s 删除成功！'), $categoryInfo->getGoodsCategoryName());
        $this->adminCommon()->addOperLog($message, $this->translator->translate('商品分类'));
        $this->flashMessenger()->addSuccessMessage($message);

        return $this->adminCommon()->toReferer();
    }
    /**
     * 仓库批量处理
     * @return \Zend\Http\Response
     */
    public function updateAllAction()
    {
        if($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            if(!empty($data['select_id']) and !empty($data['editAllState'])) {
                $this->goodsCategoryManager->updateAllGoodsCategory($data);

                $message = $this->translator->translate('批量处理成功！');
                $this->adminCommon()->addOperLog($message, $this->translator->translate('商品分类'));
                $this->flashMessenger()->addSuccessMessage($message);
            }
        }
        return $this->redirect()->toRoute('goods-category');
    }
}