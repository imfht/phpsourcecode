<?php

/**
 * 商品详情
 */

namespace app\mall\middle;

class InfoMiddle extends \app\base\middle\BaseMiddle {

    private $crumb = [];
    private $info = [];
    private $classInfo = [];
    private $tpl = '';

    private function getInfo() {
        if ($this->info) {
            return $this->info;
        }
        $id = $this->params['mall_id'];
        if (empty($id)) {
            return [];
        }
        $this->info = target('mall/Mall')->getInfo($id);

        return $this->info;
    }

    private function getCrumb() {
        if ($this->crumb) {
            return $this->crumb;
        }
        $classId = $this->info['class_id'];
        if (empty($classId)) {
            return [];
        }
        $this->crumb = target('mall/MallClass')->loadCrumbList($classId);

        return $this->crumb;
    }

    private function getClass() {
        if ($this->classInfo) {
            return $this->classInfo;
        }
        $this->info = $this->getInfo();
        $classId = $this->info['class_id'];
        if (empty($classId)) {
            return [];
        }
        $this->classInfo = target('mall/MallClass')->getInfo($classId);

        return $this->classInfo;
    }

    protected function meta() {
        $this->info = $this->getInfo();
        $this->classInfo = $this->getClass();
        $this->crumb = $this->getCrumb();
        $this->setMeta($this->info['title'] . ' - ' . $this->classInfo['name']);
        $this->setName('商品详情');
        $this->setCrumb($this->crumb);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function classInfo() {
        $this->classInfo = $this->getClass();
        if (empty($this->classInfo)) {
            return $this->stop('分类不存在！', 404);
        }
        $this->crumb = $this->getCrumb();
        $parentClassInfo = array_slice($this->crumb, -2, 1);
        if (empty($parentClassInfo)) {
            $parentClassInfo = $this->crumb[0];
        } else {
            $parentClassInfo = $parentClassInfo[0];
        }
        $topClassInfo = $this->crumb[0];

        if ($this->classInfo['tpl_content']) {
            $this->tpl = APP_NAME . '_' . $this->classInfo['tpl_content'];
        }

        return $this->run([
            'classInfo' => $this->classInfo,
            'parentClassInfo' => $parentClassInfo,
            'topClassInfo' => $topClassInfo,
        ]);
    }

    protected function data() {
        $id = $this->params['mall_id'];
        $this->info = $this->getInfo();
        $userId = $this->params['user_id'];

        if (empty($this->info)) {
            return $this->stop('商品不存在！', 404);
        }

        if ($this->info['tpl']) {
            $this->tpl = 'mall_' . $this->info['tpl'];
        }
        target('site/SiteContent')->where(['content_id' => $this->info['content_id']])->setInc('view');

        $proId = $this->params['pro_id'];
        $mallData = $this->mallData($id, $proId, $this->info);

        $proInfo = $mallData['proInfo'];
        $specList = $mallData['specList'];
        $skuList = $mallData['skuList'];
        if (empty($proInfo)) {
            return $this->stop('货品不存在！', 404);
        }


        $filter = [];
        if ($this->info['filter_id']) {
            $filter = target('site/SiteFilter')->getFilterContent($this->info['filter_id'], $this->info['content_id']);
        }

        $brandInfo = target('shop/ShopBrand')->getInfo($this->info['brand_id']);

        $faqList = target('shop/ShopFaq')->loadList(['has_id' => $id, 'app' => APP_NAME], 10);
        $commentList = target('order/OrderComment')->loadList([
            'A.app' => APP_NAME,
            'A.has_id' => $id
        ], 10);
        $commentStatis = target('shop/Shop', 'service')->getCommentStatis(APP_NAME, $id);

        if ($userId) {
            $followInfo = target('shop/ShopFollow')->getWhereInfo([
                'app' => APP_NAME,
                'has_id' => $id,
                'user_id' => $userId
            ]);
        }
        if (!empty($followInfo)) {
            $this->info['follow'] = 1;
        } else {
            $this->info['follow'] = 0;
        }
        $this->info['images'] = unserialize($this->info['images']);
        $html = '<style>.edit-content img { height: auto !important; width: auto !important; max-width: 100% !important; vertical-align: middle;} .edit-content p { margin: 0 !important;}</style>';
        $this->info['content'] = $html . "<div class='edit-content'>" . html_out($this->info['content']) . "</div>";


        return $this->run([
            'info' => $this->info,
            'proInfo' => $proInfo,
            'skuList' => $skuList,
            'specList' => $specList,
            'brandInfo' => $brandInfo,
            'faqList' => $faqList,
            'commentList' => $commentList,
            'commentRate' => $commentStatis['commentRate'],
            'commentCount' => $commentStatis['commentCount'],
            'filterList' => $filter

        ]);
    }

    private function mallData($mallId, $proId = 0, $info) {
        $proList = target('mall/mallProducts')->loadList(['A.mall_id' => $mallId], 0);
        $proInfo = [];
        if (!empty($proId)) {
            foreach ($proList as $vo) {
                if ($vo['products_id'] == $proId) {
                    $proInfo = $vo;
                }
            }
        } else {
            $proInfo = $proList[0];
        }
        if(empty($proInfo)) {
            return $this->stop('暂无可选规格!');
        }
        if ($proInfo) {
            $proInfo['spec_data'] = unserialize($proInfo['spec_data']);
        }
        $skuList = [];
        if (!empty($proList)) {
            foreach ($proList as $key => $vo) {
                $specData = unserialize($vo['spec_data']);
                $k = [];
                if (!empty($specData)) {
                    foreach ($specData as $v) {
                        $k[] = $v['id'] . ':' . $v['value'];
                    }
                }
                $k = implode(',', $k);
                $skuList[$k] = $vo;
                $skuList[$k]['spec_data'] = unserialize($vo['spec_data']);
                if ($vo['products_id'] == $proList[0]['products_id']) {
                    $skuList[$k]['url'] = url('index', ['id' => $mallId, 'pro_id' => 0]);
                } else {
                    $skuList[$k]['url'] = url('index', ['id' => $mallId, 'pro_id' => $vo['products_id']]);
                }
            }
        }
        $specData = [];
        if (!empty($proInfo['spec_data'])) {
            foreach ($proInfo['spec_data'] as $vo) {
                $specData[$vo['id']] = $vo;
            }
            $proInfo['spec_data'] = $specData;
        }

        $specList = unserialize($info['spec_data']);
        if (!empty($specList)) {
            foreach ($specList as $key => $vo) {
                $spec = explode(',', $vo['value']);
                $specList[$key]['value'] = $spec;
                if (in_array($specData[$key]['value'], $spec)) {
                    $specList[$key]['cur'] = $specData[$key]['value'];
                }
            }
        }

        return [
            'proInfo' => $proInfo ? $proInfo : [],
            'skuList' => $skuList ? $skuList : [],
            'specList' => $specList ? $specList : []
        ];
    }

    protected function spec() {
        $proId = $this->params['pro_id'];
        $this->info = $this->getInfo();
        $info = $this->info;
        $specData = $this->mallData($info['mall_id'], $proId, $info);
        return $this->run(array_merge(['info' => $info], $specData));
    }
	
    protected function share() {
        $userId = $this->params['user_id'];
        $layer = $this->params['layer'];
        $this->info = $this->getInfo();

        $saleCode = '';
        if ($userId) {
            $saleUser = target('sale/SaleUser')->getWhereInfo([
                'A.user_id' => $userId,
                'agent' => 1
            ]);
            if ($saleUser) {
                $saleCode = $saleUser['code'];
            }
        }

        return $this->run([
            'share' => [
                'title' => $this->info['title'],
                'desc' => $this->info['description'] ? $this->info['description'] : $this->info['title'],
                'image' => str_replace('http:', 'https:', $this->info['image']),
                'url' => url($layer . '/mall/Info/index', ['id' => $this->info['mall_id'], 'sale_code' => $saleCode], true, false)
            ]
        ]);
    }


}