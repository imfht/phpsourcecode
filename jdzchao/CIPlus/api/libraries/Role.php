<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Role {
    // 带权限的用户列表
    public function users(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_user_model");
        $num = $request->params('n') > 0 ? $request->params('n') : 10;
        $p = $request->params('p') > 0 ? $request->params('p') : 1;
        $offset = $num * ($p - 1);
        $total = $CI->role_user_model->users_total($request->params('key'), $request->params('value'));
        $data = array(
            'total' => $total,
            'list' => array()
        );
        if ($total > 0) {
            $res = $CI->role_user_model->users($num, $offset, $request->params('key'), $request->params('value'));
            $data['list'] = $res;
            $respond->setCode(20000);
        }
        $respond->setData($data);
        return $respond;
    }

    public function apis(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_api_model");
        $re = $CI->role_api_model->all($request->params('key'));
        $respond->setCode(20000)->setData($re);
        return $respond;
    }

    public function api_edit(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_api_model");
        $dict = $request->params('dict');
        $dict = json_decode($dict, true);
        if ($request->params('role') === 'admin') {
            $respond->setCode(40000);
        } else {
            $n = $CI->role_api_model->clean($request->params('role'));
            $re = $CI->role_api_model->edit($request->params('role'), $dict);
            if ($re > 0) {
                $respond->setCode(20000)->setData(array('n' => $re));
            }
        }
        return $respond;
    }

    // 全部角色
    public function all(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_model");
        $res = $CI->role_model->all();
        $respond->setCode(20000)->setData($res);
        return $respond;
    }

    // 更多角色
    public function more(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_model");
        $total = $CI->role_model->total();
        $data = array(
            'total' => $total,
            'roles' => null
        );
        if ($total > 0) {
            $res = $CI->role_model->more($request->params('p'), $request->params('n'));
            $data['roles'] = $res;
            $respond->setCode(20000)->setData($data);
        }
        return $respond;
    }

    // 添加角色
    public function add(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_model");
        $id = $CI->role_model->add(
            $request->params('key'),
            $request->params('name'),
            $request->params('description')
        );
        if ($id > 0) {
            $respond->setCode(20000)->setData(
                array('id' => $id)
            );
        }
        return $respond;
    }

    // 编辑角色
    public function edit(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_model");
        $n = $CI->role_model->edit(
            $request->params('id'),
            $request->params('name'),
            $request->params('description')
        );
        if ($n > 0) {
            $respond->setCode(20000)->setData(
                array('n' => $n)
            );
        }
        return $respond;
    }

    // 删除角色
    public function del(Request $request, Respond $respond) {
        $CI =& get_instance();
        $CI->load->model("role_model");
        $res = $CI->role_model->del($request->params('id'));
        if ($res > 0) {
            $respond->setCode(20000)->setData($res);
        }
        return $respond;
    }


}