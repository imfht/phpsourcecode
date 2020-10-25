<?php
namespace app\site\service;

/**
 * 标签接口
 */
class LabelService {

    /**
     * 导航列表
     * @param $data
     * @return mixed
     */
    public function nav($data) {
        $where = array();
        if (!empty($data['group_id'])) {
            $where['group_id'] = $data['group_id'];
        } else {
            $where['group_id'] = 1;
        }
        //上级栏目
        $parentId = 0;
        if (isset($data['parent_id'])) {
            $parentId = $data['parent_id'];
        }
        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'] = $data['where'];
        }
        $limit = 10;
        if (!empty($data['limit'])) {
            $limit = $data['limit'];
        }
        $list = target('site/SiteNav')->loadList($where, $limit);
        if ($list) {
            $list = target('site/SiteNav')->getTree($list, $parentId, ['parent_id', 'nav_id']);
        }
        return $list;
    }

    /**
     * 碎片内容
     * @param $data
     * @return mixed
     */
    public function fragment($data) {
        $where = array();
        $where['fragment_id'] = $data['id'];

        if (!empty($data['where'])) {
            $where['_sql'] = $data['where'];
        }

        $info = target('site/SiteFragment')->getInfo($data['id']);
        return html_out($info['content']);
    }

    /**
     * 表单内容
     * @param $data
     * @return mixed
     */
    public function form($data) {
        $formId = intval($data['form_id']);
        if (empty($formId)) {
            return [];
        }
        $formInfo = target('site/SiteForm')->getInfo($formId);
        if (empty($formInfo)) {
            return [];
        }
        $where = [];
        if (!empty($data['where'])) {
            $where['_sql'] = $data['where'];
        }
        $limit = 10;
        if (!empty($data['limit'])) {
            $limit = $data['limit'];
        }
        if (empty($data['order'])) {
            $data['order'] = 'data_id desc';
        }
        return target('site/SiteFormData')->table('form_' . $formInfo['label'])->loadList($where, $limit, $data['order']);

    }
	
	/**
     * 广告位置列表
     * @param $data
     * @return mixed
     */
    public function advPosition($data) {
        $where = array();
        if (empty($data['region'])) {
            return [];
        }
        $regionInfo = target('site/SiteAdvRegion')->getWhereInfo([
            'label' => $data['region']
        ]);
        if(empty($regionInfo)) {
            return [];
        }
        if(!$regionInfo['status']) {
            return [];
        }
        $where['region_id'] = $regionInfo['region_id'];
        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        $limit = 10;
        if (!empty($data['limit'])) {
            $limit = $data['limit'];
        }
        $where['status'] = 1;
        return target('site/SiteAdvPosition')->loadList($where, $limit, 'sort asc, pos_id asc');
    }

    /**
     * 广告列表
     * @param $data
     * @return mixed
     */
    public function adv($data) {
        $where = array();
        if (empty($data['pos'])) {
            return [];
        }
        $posInfo = target('site/SiteAdvPosition')->getWhereInfo([
            'label' => $data['pos']
        ]);
        if(empty($posInfo)) {
            return [];
        }
        if(!$posInfo['status']) {
            return [];
        }
        $where['pos_id'] = $posInfo['pos_id'];
        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        $limit = 10;
        if (!empty($data['limit'])) {
            $limit = $data['limit'];
        }
        $where['_sql'][] = '(start_time = 0 OR start_time >= ' . time() . ')';
        $where['_sql'][] = '(stop_time = 0 OR stop_time <= ' . time() . ')';
        $where['status'] = 1;
        return target('site/SiteAdv')->loadList($where, $limit, 'sort asc, adv_id asc');
    }

    public function search($data) {
        $where = array();
        if ($data['type']) {
            $where['app'] = $data['type'];
        }
        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        $limit = 10;
        if (!empty($data['limit'])) {
            $limit = $data['limit'];
        }
        if (empty($data['order'])) {
            $data['order'] = 'num desc, create_time desc';
        }
        return target('site/SiteSearch')->loadList($where, $limit, $data['order']);
    }

    public function tags($data) {
        $where = array();
        if ($data['type']) {
            $where['app'] = $data['type'];
        }
        //其他条件
        if (!empty($data['where'])) {
            $where['_sql'][] = $data['where'];
        }
        $limit = 10;
        if (!empty($data['limit'])) {
            $limit = $data['limit'];
        }
        if (empty($data['order'])) {
            $data['order'] = 'quote desc, tag_id desc';
        }
        return target('site/SiteTags')->loadList($where, $limit, $data['order']);
    }

}
