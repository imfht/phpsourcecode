<?php

namespace app\home\controller;
use think\facade\View;
use think\facade\Lang;

/**
 * ============================================================================
 * DSMall多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 控制器
 */
class SellerEditablePage extends BaseSeller {

    var $type = 'pc';
    var $model_dir = 'home@default/base/editable_page_model/';

    public function initialize() {
        parent::initialize();
        Lang::load(base_path() . 'home/lang/' . config('lang.default_lang') . '/seller_editable_page.lang.php');
    }

    public function page_list($type = 'pc') {
        $this->type = $type;
        $keyword = input('param.editable_page_name');

        $condition = array();
        if ($keyword) {
            $condition[]=array('editable_page_name','like', '%' . $keyword . '%');
        }
        View::assign('filtered', empty($condition) ? 0 : 1);
        if (!in_array($type, array('pc', 'h5'))) {
            $type = 'pc';
        }


        $editable_page_model = model('editable_page');
        $condition = array_merge(array(array('store_id' ,'=', $this->store_info['store_id']), array('editable_page_client' ,'=', $type)), $condition);
        $editable_page_list = $editable_page_model->getEditablePageList($condition, 10);
        foreach ($editable_page_list as $key => $val) {
            if ($val['editable_page_client'] == 'pc') {
                $editable_page_list[$key]['edit_url'] = (string)url('StoreSpecial/index', ['store_id' => $this->store_info['store_id'], 'editable_page_id' => $val['editable_page_id']]);
                $editable_page_list[$key]['view_url'] = (string)url('StoreSpecial/index', ['store_id' => $this->store_info['store_id'], 'special_id' => $val['editable_page_id']]);
            } else {
                $editable_page_list[$key]['edit_url'] = (string)url('SellerEditablePage/mobile_page_setting', array('store_id' => $this->store_info['store_id'], 'editable_page_id' => $val['editable_page_id']));
                $editable_page_list[$key]['view_url'] = config('ds_config.h5_site_url') . '/' . 'home/store_special' . '?' . http_build_query(['id' => $this->store_info['store_id'], 'special_id' => $val['editable_page_id']]);
            }
        }

        View::assign('show_page', $editable_page_model->page_info->render());
        View::assign('editable_page_list', $editable_page_list);
        View::assign('type', $type);
        /* 设置卖家当前菜单 */
        $this->setSellerCurMenu($type == 'h5' ? 'seller_editable_page_h5' : 'seller_editable_page_pc');
        /* 设置卖家当前栏目 */
        $this->setSellerCurItem('page_list');
        return View::fetch($this->template_dir . 'page_list');
    }

    public function h5_page_list() {
        return $this->page_list('h5');
    }

    /**
     * 新增页面
     */
    public function page_add() {
        $editable_page_path = input('param.editable_page_path');
        $editable_page_item_id = intval(input('param.editable_page_item_id'));
        $editable_page_model = model('editable_page');
        if (!request()->isPost()) {
            return View::fetch($this->template_dir . 'page_form');
        } else {
            $data = array(
                'store_id' => $this->store_info['store_id'],
                'editable_page_name' => input('post.editable_page_name'),
                'editable_page_path' => $editable_page_path,
                'editable_page_item_id' => $editable_page_item_id,
                'editable_page_client' => input('param.type', 'pc'),
                'editable_page_theme' => 'style_1',
                'editable_page_edit_time' => TIMESTAMP,
                'editable_page_theme_config' => json_encode(array(
                    'back_color' => input('param.back_color')
                ))
            );
            $result = $editable_page_model->addEditablePage($data);
            
            $condition = array();
            $condition[] = array('store_id','=',$data['store_id']);
            $condition[] = array('editable_page_id','<>',$result);
            $condition[] = array('editable_page_path','=',$data['editable_page_path']);
            $condition[] = array('editable_page_client','=',$data['editable_page_client']);
            
            if (!in_array($data['editable_page_path'], array('store/index'))) {
                $condition[] = array('editable_page_item_id','=',$data['editable_page_item_id']);
            }
            $editable_page_model->editEditablePage($condition, array('editable_page_path' => '', 'editable_page_item_id' => 0));
            if ($result) {
                $this->recordSellerlog(lang('ds_add') . ($data['editable_page_client'] == 'h5' ? lang('editable_page_h5') : lang('editable_page_pc')) . '[flex_' . $result . ':' . input('post.editable_page_name') . ']');
                ds_json_encode(10000, lang('ds_common_op_succ'));
            } else {
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
        }
    }
    /**
     * 设置手机端页面
     */
    public function mobile_page_setting() {
        return View::fetch($this->model_dir . 'mobile_page_setting');
    }

    public function mobile_page_view() {
        //获取配置列表
        $editable_page_id = intval(input('param.editable_page_id'));
        $editable_page_model = model('editable_page');
        $editable_page = $editable_page_model->getOneEditablePage(array('store_id'=>$this->store_info['store_id'],'editable_page_id' => $editable_page_id));
        if (!$editable_page) {
            $this->error(lang('param_error'));
        }
        $editable_page['if_edit'] = 1;
        $editable_page['editable_page_theme_config'] = json_decode($editable_page['editable_page_theme_config'], true);
        View::assign('editable_page', $editable_page);
        $data = $editable_page_model->getEditablePageConfigByPageId($editable_page_id,$this->store_info['store_id']);
        View::assign('editable_page_config_list', $data['editable_page_config_list']);
        return View::fetch($this->model_dir . 'mobile_page_view');
    }
    /**
     * 编辑页面
     */
    public function page_edit() {
        $editable_page_id = intval(input('param.editable_page_id'));

        $editable_page_model = model('editable_page');
        $editable_page_info = $editable_page_model->getOneEditablePage(array('store_id' => $this->store_info['store_id'], 'editable_page_id' => $editable_page_id));
        if (!$editable_page_info) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editable_page_info['editable_page_theme_config'] = json_decode($editable_page_info['editable_page_theme_config'], true);
        if (!request()->isPost()) {
            View::assign('editable_page', $editable_page_info);
            return View::fetch($this->template_dir . 'page_form');
        } else {
            $data = array(
                'editable_page_path' => input('post.editable_page_path'),
                'editable_page_item_id' => input('post.editable_page_item_id'),
                'editable_page_name' => input('post.editable_page_name'),
                'editable_page_theme_config' => json_encode(array(
                    'back_color' => input('param.back_color')
                ))
            );
            $result = $editable_page_model->editEditablePage(array('editable_page_id' => $editable_page_id), $data);
            
            $condition = array();
            $condition[] = array('store_id','=',$this->store_info['store_id']);
            $condition[] = array('editable_page_id','<>',$editable_page_id);
            $condition[] = array('editable_page_path','=',$data['editable_page_path']);
            $condition[] = array('editable_page_client','=',$editable_page_info['editable_page_client']);
            
            if (!in_array($data['editable_page_path'], array('store/index'))) {
                $condition[] = array('editable_page_item_id','=',$data['editable_page_item_id']);
            }
            $editable_page_model->editEditablePage($condition, array('editable_page_path' => '', 'editable_page_item_id' => 0));
            if ($result) {
                $this->recordSellerlog(lang('ds_edit') . ($editable_page_info['editable_page_client'] == 'h5' ? lang('editable_page_h5') : lang('editable_page_pc')) . '[' . $editable_page_info['editable_page_name'] . ']');
                ds_json_encode(10000, lang('ds_common_op_succ'));
            } else {
                ds_json_encode(10000, lang('ds_common_op_fail'));
            }
        }
    }

    /**
     * 删除页面
     */
    public function page_del() {
        $editable_page_id = intval(input('param.editable_page_id'));

        $editable_page_model = model('editable_page');
        $editable_page_info = $editable_page_model->getOneEditablePage(array('store_id' => $this->store_info['store_id'], 'editable_page_id' => $editable_page_id));
        if (!$editable_page_info) {
            ds_json_encode(10001, lang('param_error'));
        }
        if (!$editable_page_model->delEditablePage($editable_page_id)) {
            ds_json_encode(10001, lang('ds_common_op_fail'));
        }
        $this->recordSellerlog(lang('ds_del') . ($editable_page_info['editable_page_client'] == 'h5' ? lang('editable_page_h5') : lang('editable_page_pc')) . '[ID:' . $editable_page_info['editable_page_id'] . ':' . $editable_page_info['editable_page_name'] . ']');
        ds_json_encode(10000, lang('ds_common_del_succ'));
    }

    /**
     * 新增模块
     */
    public function model_add() {
        $page_id = intval(input('param.editable_page_id'));
        if (!$page_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editable_page_model = model('editable_page');
        $editable_page = $editable_page_model->getOneEditablePage(array('store_id' => $this->store_info['store_id'], 'editable_page_id' => $page_id));
        if (!$editable_page) {
            ds_json_encode(10001, lang('param_error'));
        }
        $model_id = intval(input('param.model_id'));
        $type = input('param.type', 'pc');
        if (!$model_id) {
            $editable_page_model_list = model('editable_page_model')->getEditablePageModelList(array(array('editable_page_model_type','in', array('', 'store')), array('editable_page_model_client','in', array('', $type)), array('editable_page_theme','in', array('', '|' . $editable_page['editable_page_theme'] . '|'))));
            View::assign('editable_page_model_list', $editable_page_model_list);
            echo View::fetch($this->model_dir . 'model_add');
            exit;
        } else {
            $config_id = intval(input('param.config_id'));
            $res = model('editable_page_model', 'logic')->modelAdd($page_id, $type, $model_id, $config_id, $this->store_info['store_id']);
            if (!$res['code']) {
                ds_json_encode(10001, $res['msg']);
            }
            $data = $res['data'];
            //日志
            $this->recordSellerlog(lang('ds_update') . lang('editable_page_model') . '[' . $data['editable_page_config_id'] . ']');
            View::assign('page_config', $data);
            ds_json_encode(10000, '', array('config_id' => $data['editable_page_config_id'], 'model_html' => View::fetch($this->model_dir . ($type == 'h5' ? 'h5_' : '') . $model_id)));
        }
    }

    public function model_del() {
        if (!model('editable_page_config')->delEditablePageConfig(array('editable_page_id' => intval(input('param.editable_page_id')), 'editable_page_config_id' => intval(input('param.config_id'))))) {
            ds_json_encode(10001, lang('ds_common_op_fail'));
        } else {
            //日志
            $this->recordSellerlog(lang('ds_update') . lang('editable_page_model') . '[' . input('param.config_id') . ']');
            ds_json_encode(10000);
        }
    }


    public function model_sort() {
        $config_id = intval(input('param.config_id'));
        $o_config_id = intval(input('param.o_config_id'));
        $direction = intval(input('param.direction'));
        if (!$config_id || !$o_config_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $res = model('editable_page_model', 'logic')->modelSort($direction, $config_id, $o_config_id);
        if (!$res['code']) {
            ds_json_encode(10001, $res['msg']);
        }

        ds_json_encode(10000);
    }

    /**
     * 编辑模块
     */
    public function model_edit() {
        $config_id = intval(input('param.config_id'));
        if (!$config_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editable_page_config_model = model('editable_page_config');
        $editable_page_config_info = $editable_page_config_model->getOneEditablePageConfig(array('editable_page_config_id' => $config_id));
        if (!$editable_page_config_info) {
            ds_json_encode(10001, lang('editable_page_config_not_exist'));
        }
        $config_info = json_decode($editable_page_config_info['editable_page_config_content'], true);
        if (!request()->isPost()) {
            View::assign('base_config', $config_info);
            View::assign('model_type', $editable_page_config_info['editable_page_model_id']);
            echo View::fetch($this->model_dir . 'model_edit');
            exit;
        } else {
            $res = model('editable_page_model', 'logic')->modelEdit($editable_page_config_info, input('post.'), $this->store_info['store_id']);
            if (!$res['code']) {
                ds_json_encode(10001, $res['msg']);
            }
            $editable_page_config_info = $res['data'];
            //日志
            $this->recordSellerlog(lang('ds_update') . lang('editable_page_model') . '[' . $editable_page_config_info['editable_page_config_id'] . ']');
            $type = input('param.type', 'pc');
            View::assign('page_config', $editable_page_config_info);
            ds_json_encode(10000, '', array('config_id' => $editable_page_config_info['editable_page_config_id'], 'model_html' => View::fetch($this->model_dir . ($type == 'h5' ? 'h5_' : '') . $editable_page_config_info['editable_page_model_id'])));
        }
    }

    /**
     * 编辑器模块
     */
    public function model_editor() {
        $config_id = intval(input('param.config_id'));
        $item_id = intval(input('param.item_id'));
        if (!$config_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editable_page_config_model = model('editable_page_config');
        $editable_page_config_info = $editable_page_config_model->getOneEditablePageConfig(array('editable_page_config_id' => $config_id));
        if (!$editable_page_config_info) {
            ds_json_encode(10001, lang('editable_page_config_not_exist'));
        }
        $config_info = json_decode($editable_page_config_info['editable_page_config_content'], true);
        if (!isset($config_info['editor']) || !isset($config_info['editor'][$item_id])) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editor_content = $config_info['editor'][$item_id];
        if (!request()->isPost()) {
            View::assign('editor_content', $editor_content);
            View::assign('file_upload', model('upload')->getUploadList(array('upload_type' => 7, 'item_id' => $config_id)));
            echo View::fetch($this->model_dir . 'model_editor');
            exit;
        } else {
            $config_info['editor'][$item_id] = input('post.editor');
            if (!$editable_page_config_model->editEditablePageConfig(array('editable_page_config_id' => $config_id), array('editable_page_config_content' => json_encode($config_info)))) {
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
            $config_info['editor'][$item_id] = htmlspecialchars_decode($config_info['editor'][$item_id]);
            $editable_page_config_info['editable_page_config_content'] = $config_info;
            $editable_page_config_info = model('editable_page_model', 'logic')->updatePage($editable_page_config_info, $this->store_info['store_id']);
            //日志
            $this->recordSellerlog(lang('ds_update') . lang('editable_page_model') . '[' . $editable_page_config_info['editable_page_config_id'] . ']');
            $type = input('param.type', 'pc');
            View::assign('page_config', $editable_page_config_info);
            ds_json_encode(10000, '', array('config_id' => $editable_page_config_info['editable_page_config_id'], 'model_html' => View::fetch($this->model_dir . ($type == 'h5' ? 'h5_' : '') . $editable_page_config_info['editable_page_model_id'])));
        }
    }

    /**
     * 文字模块
     */
    public function model_text() {
        $config_id = intval(input('param.config_id'));
        $item_id = intval(input('param.item_id'));
        if (!$config_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editable_page_config_model = model('editable_page_config');
        $editable_page_config_info = $editable_page_config_model->getOneEditablePageConfig(array('editable_page_config_id' => $config_id));
        if (!$editable_page_config_info) {
            ds_json_encode(10001, lang('editable_page_config_not_exist'));
        }
        $config_info = json_decode($editable_page_config_info['editable_page_config_content'], true);
        if (!isset($config_info['text']) || !isset($config_info['text'][$item_id]) || !isset($config_info['text'][$item_id]['count']) || !isset($config_info['text'][$item_id]['list'])) {
            ds_json_encode(10001, lang('param_error'));
        }
        $text_info = $config_info['text'][$item_id];
        if (!request()->isPost()) {
            View::assign('text_info', $text_info);
            View::assign('editable_type', 'text');
            echo View::fetch($this->model_dir . 'model_text');
            exit;
        } else {
            $text_list = input('post.text/a');
            if (!is_array($text_list) || empty($text_list)) {
                ds_json_encode(10001, lang('param_error'));
            }
            $config_info['text'][$item_id]['list'] = $this->arraySort($text_list, 'sort' , 'asc');
            if (!$editable_page_config_model->editEditablePageConfig(array('editable_page_config_id' => $config_id), array('editable_page_config_content' => json_encode($config_info)))) {
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
            $editable_page_config_info['editable_page_config_content'] = $config_info;
            $editable_page_config_info = model('editable_page_model', 'logic')->updatePage($editable_page_config_info, $this->store_info['store_id']);
            //日志
            $this->recordSellerlog(lang('ds_update') . lang('editable_page_model') . '[' . $editable_page_config_info['editable_page_config_id'] . ']');
            $type = input('param.type', 'pc');
            View::assign('page_config', $editable_page_config_info);
            ds_json_encode(10000, '', array('config_id' => $editable_page_config_info['editable_page_config_id'], 'model_html' => View::fetch($this->model_dir . ($type == 'h5' ? 'h5_' : '') . $editable_page_config_info['editable_page_model_id'])));
        }
    }

    /**
     * 链接模块
     */
    public function model_link() {
        $config_id = intval(input('param.config_id'));
        $item_id = intval(input('param.item_id'));
        if (!$config_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editable_page_config_model = model('editable_page_config');
        $editable_page_config_info = $editable_page_config_model->getOneEditablePageConfig(array('editable_page_config_id' => $config_id));
        if (!$editable_page_config_info) {
            ds_json_encode(10001, lang('editable_page_config_not_exist'));
        }
        $config_info = json_decode($editable_page_config_info['editable_page_config_content'], true);
        if (!isset($config_info['link']) || !isset($config_info['link'][$item_id]) || !isset($config_info['link'][$item_id]['count']) || !isset($config_info['link'][$item_id]['list'])) {
            ds_json_encode(10001, lang('param_error'));
        }
        $link_info = $config_info['link'][$item_id];
        if (!request()->isPost()) {
            View::assign('text_info', $link_info);
            View::assign('editable_type', 'link');
            echo View::fetch($this->model_dir . 'model_text');
            exit;
        } else {
            $link_list = input('post.text/a');
            if (!is_array($link_list) || empty($link_list)) {
                ds_json_encode(10001, lang('param_error'));
            }
            $config_info['link'][$item_id]['list'] = $this->arraySort($link_list, 'sort' , 'asc');
            if (!$editable_page_config_model->editEditablePageConfig(array('editable_page_config_id' => $config_id), array('editable_page_config_content' => json_encode($config_info)))) {
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
            $editable_page_config_info['editable_page_config_content'] = $config_info;
            $editable_page_config_info = model('editable_page_model', 'logic')->updatePage($editable_page_config_info, $this->store_info['store_id']);
            //日志
            $this->recordSellerlog(lang('ds_update') . lang('editable_page_model') . '[' . $editable_page_config_info['editable_page_config_id'] . ']');
            $type = input('param.type', 'pc');
            View::assign('page_config', $editable_page_config_info);
            ds_json_encode(10000, '', array('config_id' => $editable_page_config_info['editable_page_config_id'], 'model_html' => View::fetch($this->model_dir . ($type == 'h5' ? 'h5_' : '') . $editable_page_config_info['editable_page_model_id'])));
        }
    }

    /**
     * 图片模块
     */
    public function model_image() {
        $config_id = intval(input('param.config_id'));
        $item_id = intval(input('param.item_id'));
        if (!$config_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editable_page_config_model = model('editable_page_config');
        $editable_page_config_info = $editable_page_config_model->getOneEditablePageConfig(array('editable_page_config_id' => $config_id));
        if (!$editable_page_config_info) {
            ds_json_encode(10001, lang('editable_page_config_not_exist'));
        }
        $config_info = json_decode($editable_page_config_info['editable_page_config_content'], true);
        if (!isset($config_info['image']) || !isset($config_info['image'][$item_id]) || !isset($config_info['image'][$item_id]['count']) || !isset($config_info['image'][$item_id]['list'])) {
            ds_json_encode(10001, lang('param_error'));
        }
        $image_info = $config_info['image'][$item_id];
        if (!request()->isPost()) {
            View::assign('image_info', $image_info);
            echo View::fetch($this->model_dir . 'model_image');
            exit;
        } else {
            $image_list = input('post.img/a');
            if (!is_array($image_list) || empty($image_list)) {
                ds_json_encode(10001, lang('param_error'));
            }
            $config_info['image'][$item_id]['list'] = $this->arraySort($image_list, 'sort' , 'asc');
            if (!$editable_page_config_model->editEditablePageConfig(array('editable_page_config_id' => $config_id), array('editable_page_config_content' => json_encode($config_info)))) {
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
            $editable_page_config_info['editable_page_config_content'] = $config_info;
            $editable_page_config_info = model('editable_page_model', 'logic')->updatePage($editable_page_config_info, $this->store_info['store_id']);
            //日志
            $this->recordSellerlog(lang('ds_update') . lang('editable_page_model') . '[' . $editable_page_config_info['editable_page_config_id'] . ']');
            $type = input('param.type', 'pc');
            View::assign('page_config', $editable_page_config_info);
            ds_json_encode(10000, '', array('config_id' => $editable_page_config_info['editable_page_config_id'], 'model_html' => View::fetch($this->model_dir . ($type == 'h5' ? 'h5_' : '') . $editable_page_config_info['editable_page_model_id'])));
        }
    }

    public function image_del() {
        $file_id = intval(input('param.upload_id'));
        $res = model('editable_page_model', 'logic')->imageDel($file_id);
        if (!$res['code']) {
            ds_json_encode(10001, $res['msg']);
        }

        ds_json_encode(10000);
    }

    /**
     * 图片上传
     */
    public function image_upload() {
        $res = model('editable_page_model', 'logic')->imageUpload(input('param.name'), input('param.config_id'));
        if (!$res['code']) {
            ds_json_encode(10001, $res['msg']);
        }
        $data = $res['data'];
        ds_json_encode(10000, '', $data);
    }

    /**
     * 商品模块
     */
    public function model_goods() {
        $config_id = intval(input('param.config_id'));
        $item_id = intval(input('param.item_id'));
        if (!$config_id) {
            ds_json_encode(10001, lang('param_error'));
        }
        $editable_page_config_model = model('editable_page_config');
        $editable_page_config_info = $editable_page_config_model->getOneEditablePageConfig(array('editable_page_config_id' => $config_id));
        if (!$editable_page_config_info) {
            ds_json_encode(10001, lang('editable_page_config_not_exist'));
        }
        $config_info = json_decode($editable_page_config_info['editable_page_config_content'], true);
        if (!isset($config_info['goods']) || !isset($config_info['goods'][$item_id])) {
            ds_json_encode(10001, lang('param_error'));
        }
        $goods_info = $config_info['goods'][$item_id];
        if (!isset($goods_info['gc_id']) || !isset($goods_info['sort']) || !isset($goods_info['if_fix']) || !isset($goods_info['goods_id']) || !is_array($goods_info['goods_id'])) {
            ds_json_encode(10001, lang('param_error'));
        }
        if (!request()->isPost()) {
            View::assign('goods_info', $goods_info);
            $goods_list = array();
            if ($goods_info['if_fix'] && !empty($goods_info['goods_id'])) {
                $goods_model = model('goods');
                $goods_list = $goods_model->getGoodsOnlineList(array(array('goods_id','in', array_keys($goods_info['goods_id']))));
            }
            View::assign('goods_list', $goods_list);
            // 商品分类
            $store_goods_class = model('storegoodsclass')->getClassTree(array('store_id' => $this->store_info['store_id'], 'storegc_state' => '1'));
            View::assign('store_goods_class', $store_goods_class);

            echo View::fetch($this->model_dir . 'model_goods');
            exit;
        } else {
            $sort = input('param.sort');
            if (!in_array($sort, array('new', 'hot', 'good'))) {
                ds_json_encode(10001, lang('param_error'));
            }
            $if_fix = intval(input('param.if_fix'));
            if (!in_array($if_fix, array(0, 1))) {
                ds_json_encode(10001, lang('param_error'));
            }
            $goods_id = input('param.goods_id/a');
            if (!is_array($goods_id)) {
                $goods_id = array();
            }
            asort($goods_id);
            $temp = array(
                'gc_id' => intval(input('param.choose_gcid')),
                'sort' => $sort,
                'if_fix' => $if_fix,
                'goods_id' => $goods_id,
            );
            $config_info['goods'][$item_id] = array_merge($config_info['goods'][$item_id], $temp);
            if (!$editable_page_config_model->editEditablePageConfig(array('editable_page_config_id' => $config_id), array('editable_page_config_content' => json_encode($config_info)))) {
                ds_json_encode(10001, lang('ds_common_op_fail'));
            }
            $editable_page_config_info['editable_page_config_content'] = $config_info;
            $editable_page_config_info = model('editable_page_model', 'logic')->updatePage($editable_page_config_info, $this->store_info['store_id']);
            //日志
            $this->recordSellerlog(lang('ds_update') . lang('editable_page_model') . '[' . $editable_page_config_info['editable_page_config_id'] . ']');
            $type = input('param.type', 'pc');
            View::assign('page_config', $editable_page_config_info);
            ds_json_encode(10000, '', array('config_id' => $editable_page_config_info['editable_page_config_id'], 'model_html' => View::fetch($this->model_dir . ($type == 'h5' ? 'h5_' : '') . $editable_page_config_info['editable_page_model_id'])));
        }
    }

    /**
     * 搜索商品
     */
    public function search_goods() {
        $goods_model = model('goods');

        /**
         * 查询条件
         */
        $where = array(array('store_id','=',$this->store_info['store_id']));
        $search_goods_name = trim(input('param.keyword'));
        if ($search_goods_name != '') {
            $where[]=array('goods_name|store_name','like', '%' . $search_goods_name . '%');
        }

        $goods_list = $goods_model->getGoodsOnlineList($where, '*', 12);
        View::assign('goods_list', $goods_list);
        View::assign('show_page', $goods_model->page_info->render());
        echo View::fetch($this->model_dir . 'search_goods');
        exit;
    }

    /**
     * 多维数组排序（多用于文件数组数据）
     *
     * @param array $array
     * @param array $cols
     * @return array
     *
     */
    private function arraySort($array, $keys, $sort = 'asc') {
        $newArr = $valArr = array();
        foreach ($array as $key => $value) {
            $valArr[$key] = $value[$keys];
        }
        ($sort == 'asc') ? asort($valArr) : arsort($valArr);
        reset($valArr);
        foreach ($valArr as $key => $value) {
            $newArr[$key] = $array[$key];
        }
        return $newArr;
    }

    /**
     *    栏目菜单
     */
    function getSellerItemList() {
        $menu_array[] = array(
            'name' => 'page_list',
            'text' => lang('page_list'),
            'url' => 'javascript:void(0)',
        );
        return $menu_array;
    }

}
