<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting {
    // 全部接口
    public function api_all(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $all = $CI->api_model->all();
        $respond->setCode(20000)->setData($all);
        return $respond;
    }

    // 接口列表
    public function api_more(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $total = $CI->api_model->total($request->params('title'));
        $data = array(
            'total' => $total,
            'list' => array()
        );
        if ($total > 0) {
            $res = $CI->api_model->more($request->params('title'), $request->params('p'), $request->params('n'));
            $data['list'] = $res;
            $respond->setCode(20000)->setData($data);
        }
        return $respond;
    }

    // 添加接口
    public function api_add(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $id = $CI->api_model->add(
            $request->params('title'),
            $request->params('path'),
            $request->params('required'),
            $request->params('optional'),
            $request->params('method'),
            $request->params('module'),
            $request->params('validated')
        );
        if ($id > 0) {
            $respond->setCode(20000)->setData(
                array('id' => $id)
            );
        }
        return $respond;
    }

    // 编辑接口
    public function api_edit(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $id = $CI->api_model->edit(
            $request->params('id'),
            $request->params('title'),
            $request->params('path'),
            $request->params('required'),
            $request->params('optional'),
            $request->params('method'),
            $request->params('module'),
            $request->params('validated')
        );
        if ($id > 0) {
            $respond->setCode(20000)->setData(
                array('id' => $id)
            );
        }
        return $respond;
    }

    // 删除接口
    public function api_del(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $res = $CI->api_model->del($request->params('id'));
        if ($res > 0) {
            $respond->setCode(20000)->setData($res);
        }
        return $respond;
    }

    // 恢复接口
    public function api_revive(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("api_model");
        $res = $CI->api_model->revive($request->params('id'));
        if ($res > 0) {
            $respond->setCode(20000)->setData($res);
        }
        return $respond;
    }

    // 全部模块
    public function module_all(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("module_model");
        $res = $CI->module_model->all();
        $respond->setCode(20000)->setData($res);
        return $respond;
    }

    // 更多模块
    public function module_more(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("module_model");
        $total = $CI->module_model->total();
        $data = array(
            'total' => $total,
            'list' => array()
        );
        if ($total > 0) {
            $res = $CI->module_model->more($request->params('p'), $request->params('n'));
            $data['list'] = $res;
            $respond->setCode(20000)->setData($data);
        }
        return $respond;
    }

    // 添加模块
    public function module_add(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("module_model");
        $id = $CI->module_model->add($request->params('key'), $request->params('name'), $request->params('parent_id'));
        if ($id > 0) {
            $respond->setCode(20000)->setData(
                array('id' => $id)
            );
        }
        return $respond;
    }

    // 编辑模块
    public function module_edit(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("module_model");
        $res = $CI->module_model->edit($request->params('id'), $request->params('name'), $request->params('parent_id'));
        if ($res > 0) {
            $respond->setCode(20000)->setData(
                array('n' => $res)
            );
        }
        return $respond;
    }

    // 删除模块
    public function module_del(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("module_model");
        $res = $CI->module_model->get($request->params('id'));
        if ($res) {
            $CI->load->model('api_model');
            $CI->module_model->del($request->params('id'));
            $CI->api_model->remove_module($res['key']);
            $respond->setCode(20000)->setData($res);
        }
        return $respond;
    }
}