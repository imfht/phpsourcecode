<?php

/**
 * 筛选管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteFilterModel extends SystemModel {

    private $urlParam = [];
    private $filterParam = [];
    private $attrList = [];
    private $ids = '';
    private $contentData = [];

    protected $infoModel = [
        'pri' => 'filter_id',
        'validate' => [
            'name' => [
                'len' => ['1, 20', '名称输入不正确!', 'must', 'all'],
            ],
        ],
        'format' => [
            'name' => [
                'function' => ['html_in', 'all'],
            ],
        ],
        'into' => '',
        'out' => '',
    ];

    public function _saveBefore($data) {
        $this->beginTransaction();
        return $data;
    }

    public function _saveAfter($type, $data) {
        $list = $_POST['attr'];
        $attrData = [];
        foreach ($list['name'] as $key => $vo) {
            $curData = [
                'filter_id' => $data['filter_id'],
                'attr_id' => $list['attr_id'][$key],
                'name' => $vo,
                'type' => $list['type'][$key],
                'value' => $list['value'][$key],
                'filter' => $list['filter'][$key]
            ];
            if($curData['type'] == 2) {
                $curData['value'] = '';
                $curData['filter'] = 0;
            }
            $attrData[] = $curData;
        }
        if (empty($attrData)) {
            $this->error = '请添加属性!';
            $this->rollBack();

            return false;
        }
        $attrIds = [];
        foreach ($attrData as $v) {
            if (empty($v['name'])) {
                $this->error = '请完善属性信息!';
                return false;
            }
            if ($v['attr_id']) {
                $attrIds[] = $v['attr_id'];
                $status = target('site/SiteFilterAttr')->edit($v);
            } else {
                $status = target('site/SiteFilterAttr')->add($v);
                $attrIds[] = $status;
            }
            if (!$status) {
                $this->error = '处理失败,请重试!';
                $this->rollBack();

                return false;
            }
        }

        $list = target('site/SiteFilterAttr')->loadList(['filter_id' => $data['filter_id'], '_sql' => 'attr_id NOT IN (' . implode(',', $attrIds) . ')']);
        if ($list) {
            foreach ($list as $vo) {
                if (!target('site/SiteContentAttr')->where(['attr_id' => $vo['attr_id']])->delete()) {
                    $this->error = '处理失败,请重试!';
                    $this->rollBack();

                    return false;
                }
                if (!target('site/SiteFilterAttr')->del($vo['attr_id'])) {
                    $this->error = '处理失败,请重试!';
                    $this->rollBack();

                    return false;
                }
            }
        }

        $this->commit();

        return true;
    }

    public function delData($id) {
        $attrIds = [];
        $attrList = target('site/SiteFilterAttr')->loadList(['filter_id' => $id]);
        if ($attrList) {
            foreach ($attrList as $vo) {
                $attrIds[] = $vo['attr_id'];
            }
        }
        $this->beginTransaction();
        $where = [];
        $where['filter_id'] = $id;
        if (!$this->where($where)->delete()) {
            $this->rollBack();

            return false;
        }
        if (!target('site/SiteFilterAttr')->where(['filter_id' => $id])->delete()) {
            $this->rollBack();

            return false;
        }
        if ($attrIds) {
            if (!target('site/SiteContentAttr')->where(['_sql' => 'attr_id IN (' . implode(',', $attrIds) . ')'])->delete()) {
                $this->rollBack();

                return false;
            }
        }
        $this->commit();

        return true;
    }

    public function getHtml($id, $contentId = 0, $type = 0) {
        $list = target('site/SiteFilterAttr')->loadList(['filter_id' => $id]);
        if (empty($list)) {
            return '';
        }
        $contentInfo = [];
        if (!empty($contentId)) {
            $contentAttr = target('site/siteContentAttr')->loadList(['content_id' => $contentId]);
            $attrInfo = [];
            if (!empty($contentAttr)) {
                foreach ($contentAttr as $vo) {
                    if($vo['type'] == 2) {
                        $attrInfo[$vo['attr_id']] = $vo['value'];
                    }else {
                        $attrInfo[$vo['attr_id']] = explode(',', $vo['value']);
                    }
                }
            }
            $contentInfo = $attrInfo;
        }
        $html = '';
        foreach ($list as $attr) {
            $value = $contentInfo[$attr['attr_id']];
            if ($attr['type'] == 1) {
                $valueList = explode(',', $attr['value']);
                $cHtml = target('site/SiteFormHtml')->checkbox('attr_data[' . $attr['attr_id'] . ']', 0, '', $value ? implode(",", $value) : '', implode("\n", $valueList));
            }
            if ($attr['type'] == 0){
                $valueList = explode(',', $attr['value']);
                $value = is_array($value) ? reset($value) : $value;
                $cHtml = target('site/SiteFormHtml')->radio('attr_data[' . $attr['attr_id'] . ']', 0, '', $value, implode("\n", $valueList));
            }

            if ($attr['type'] == 2) {
                $cHtml = target('site/SiteFormHtml')->text('attr_data[' . $attr['attr_id'] . ']', 0, '', $value, '');
            }

            if ($type) {
                $html .= target('site/SiteFormHtml')->layer($attr['name'], $cHtml);
            } else {
                $html .= target('site/SiteFormHtml')->layerWrap($attr['name'], $cHtml);

            }
        }

        return $html;
    }

    public function getFilterContent($filterId, $contentId) {
        $attrData = target('site/siteFilterAttr')->loadList(['filter_id' => $filterId, 'filter' => 1], 0, 'attr_id asc');
        $contentData = target('site/SiteContentAttr')->loadList(['content_id' => $contentId]);
        if (empty($attrData) || empty($contentData)) {
            return [];
        }

        $attrList = [];
        foreach ($attrData as $key => $vo) {
            $attrList[$vo['attr_id']] = $vo;
        }

        $attrData = [];
        foreach ($contentData as $key => $vo) {
            if($vo['type'] == 2) {
                $val = $vo['value'];
            }else {
                $val = explode(',', $vo['value']);
            }
            $attr = $attrList[$vo['attr_id']];
            if (empty($attr)) {
                continue;
            }
            $attrData[] = [
                'name' => $attr['name'],
                'val' => $val
            ];
        }

        return $attrData;

    }

    public function getFilter($contentData, $urlParam = []) {

        $this->contentData = $contentData;

        $this->getFilterParam($urlParam);
        $this->getContentData();
        $this->getFilterData();

        return [
            'attrList' => $this->attrList,
            'ids' => $this->ids,
            'filterParam' => $this->filterParam,
            'urlParam' => $this->urlParam
        ];
    }

    private function getFilterParam($urlParam = []) {
        $getParam = request('get');
        $attrArray = [];
        foreach ($getParam as $key => $vo) {
            if (stristr($key, 'attr_', 0) !== false) {
                $vo = urldecode($vo);
                $attrArray[substr($key, 5)] = $vo;
                $urlParam[$key] = $vo;
            }
        }
        $this->urlParam = $urlParam;
        $this->filterParam = $attrArray;
    }

    private function getContentData() {
        $filterParam = $this->filterParam;
        $contentData = $this->contentData;

        if (empty($contentData)) {
            return false;
        }
        $contentIds = [];
        foreach ($contentData as $vo) {
            $contentIds[] = $vo['content_id'];
        }

        $attrCond = [];
        if(!empty($filterParam)) {
            foreach ($filterParam as $key => $val) {
                if ($key && $val) {
                    $attrCond[] = ' attr_id = ' . intval($key) . ' and FIND_IN_SET("' . $val . '",value)';
                }
            }
        }
        if(!empty($attrCond)) {
            $tempArray = [];
            foreach ($attrCond as $key => $cond) {
                $tempArray[] = '(' . $cond . ')';
            }
            $tempSql = ' AND (' . implode(' or ', $tempArray) . ')';

            $childSql = 'content_id in(' . implode(',', $contentIds) . ')' . $tempSql;
            $contentArray = target('site/SiteContentAttr')->query('SELECT content_id,attr_id FROM {pre}site_content_attr WHERE ' . $childSql . '  GROUP BY content_id HAVING count(content_id) >= ' . count($attrCond));
            $contentIds = [];
            $attrArray = [];
            foreach ($contentArray as $vo) {
                $contentIds[] = $vo['content_id'];
                $attrArray[] = $vo['value'];
            }
            $contentIds = array_unique($contentIds);
        }

        $this->ids = implode(',', $contentIds);
    }

    private function getFilterData() {
        if(empty($this->ids)) {
            return false;
        }

        $childSql = 'content_id in(' . $this->ids . ')';
        $attrData = target('site/SiteContentAttr')->query('SELECT attr_id,value FROM {pre}site_content_attr WHERE ' . $childSql);

        $attrTemp = [];
        foreach ($attrData as $key => $val) {
            if ($val['attr_id']) {
                if (!isset($attrTemp[$val['attr_id']])) {
                    $attrTemp[$val['attr_id']] = array();
                }
                $checkSelectedArray = explode(",", $val['value']);
                foreach ($checkSelectedArray as $k => $v) {
                    if (!in_array($v, $attrTemp[$val['attr_id']])) {
                        $attrTemp[$val['attr_id']][] = $v;
                    }
                }
            }
        }

        if(empty($attrTemp)) {
            return false;
        }

        $attrData = target('site/siteFilterAttr')->loadList(['_sql' => 'attr_id in (' . implode(',', array_keys($attrTemp)) . ')'], 0, 'attr_id asc');
        $getData = request('get');
        $attrList = [];

        foreach ($attrData as $key => $val) {
            $attrArray = $attrTemp[$val['attr_id']];
            $getVal = urldecode($getData['attr_' . $val['attr_id']]);
            $resList = [];
            foreach ($attrArray as $k => $v) {
                $resList[] = [
                    'name' => $v,
                    'url' => $this->filterUrl(['attr_' . $val['attr_id'] => $v]),
                    'cur' => ($v == $getVal) ? true : false,
                ];
            }
            $attrList[] = [
                'attr_id' => $val['attr_id'],
                'name' => $val['name'],
                'value' => array_merge([[
                    'name' => '不限',
                    'url' => $this->filterUrl(['attr_' . $val['attr_id'] => '']),
                    'cur' => !request('get', 'attr_' . $val['attr_id']) ? true : false,
                ]], $resList),
            ];
        }
        $this->attrList = $attrList;
    }

    private function filterUrl($param) {
        $urlParam = array_filter(array_merge($this->urlParam, $param));
        return url('index', $urlParam);
    }


}