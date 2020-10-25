<?php
namespace app\shop\service;

/**
 * 标签接口
 */
class LabelService {

    /**
     * 帮助内容
     * @param $data
     * @return mixed
     */
    public function helpTrueList($data) {
        $where = [];
        if (empty($data['helpLimit'])) {
            $data['helpLimit'] = 0;
        }
        if (empty($data['classLimit'])) {
            $data['classLimit'] = 0;
        }
        if (empty($data['limit'])) {
            $data['limit'] = 0;
        }

        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        //调用数量
        if (empty($data['limit'])) {
            $data['limit'] = 0;
        }
        //内容排序
        if (empty($data['order'])) {
            $data['order'] = 'A.sort asc, A.help_id asc';
        }

        //帮助分类
        $classList = target('shop/ShopHelpClass')->loadList([], $data['classLimit'], 'sort asc, class_id asc');

        //其他属性
        $where['A.status'] = 1;
        $list = target('shop/ShopHelp')->loadList($where, $data['limit'], $data['order']);

        $dataList = [];
        foreach ($list as $vo) {
            $dataList[$vo['class_id']][] = $vo;
        }

        if($data['helpLimit']) {
            foreach ($dataList as $key => $vo) {
                $dataList[$key] = array_slice($vo, 0, $data['helpLimit']);
            }
        }

        foreach ($classList as $key => $vo) {
            $classList[$key]['list'] = $dataList[$vo['class_id']];
        }
        return $classList;
    }


    /**
     * 品牌列表
     * @param $data
     * @return mixed
     */
    public function brandList($data) {

        $where = [];
        if (!empty($classWhere)) {
            $where['_sql'][] = $classWhere;
        }
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        //调用数量
        if (empty($data['limit'])) {
            $data['limit'] = 10;
        }
        //内容排序
        if (empty($data['order'])) {
            $data['order'] = 'brand_id asc';
        }

        return target('shop/ShopBrand')->loadList($where, $data['limit'], $data['order']);

    }

}
