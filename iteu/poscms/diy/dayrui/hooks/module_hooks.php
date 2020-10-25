<?php
/**
 * Created by PhpStorm.
 * User: chunjie
 * Date: 14-6-23
 * Time: 17:11
 */

class module_hooks {

    public $ci;

    /**
     * 构造函数
     */
    function __construct() {
        $this->ci = &get_instance();
    }

    // 发布时同步到其他站点
    function syn_content_add($param) {

        if (!isset($param['syn']['use']) || !$param['syn']['use']) {
            return;
        }

        $name = $param['syn']['name'];
        unset(
            $param['data'][0]['id'],
            $param['data'][1]['id'],
            $param['data'][1]['tableid'],
            $param['data'][1]['url'],
            $param['data'][0][$name],
            $param['data'][1][$name],
            $param['syn']['name'],
            $param['syn']['use']
        );

        foreach ($param['syn'] as $site => $catid) {
            $catid = (int)$catid;
            if ($this->ci->SITE[$site] && $catid) {
                $param['data'][0]['catid'] = $param['data'][1]['catid'] = $catid;
                $this->ci->content_model->link = $this->ci->site[$site];
                $this->ci->content_model->prefix = $this->ci->db->dbprefix($site.'_'.APP_DIR);
                $this->ci->content_model->is_category = FALSE;
                if ($param['data'][1]['id'] = $this->ci->content_model->add($param['data'])) {
                    // 更新地址
                    $this->ci
                         ->site[$site]
                         ->where('id', $param['data'][1]['id'])
                         ->update($this->ci->content_model->prefix, array(
                            'url' => dr_show_url($this->ci->get_cache('module-'.$site.'-'.APP_DIR), $param['data'][1])
                        ));
                }

            }
        }

        unset($param);
    }
}
