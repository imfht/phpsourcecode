<?php
namespace app\common\api;


class Tree {

    /**
     * @param $company_id
     */
    public static function emp_dept($company_id){
        // 1 检查当前用户是否有绑定公司
        // 没有绑定公司就返回所有公司
        $user_bo_company = Para::user_bo_val('emp_company');
        // 2 检查当前用户是否有绑定部门
        // 没有绑定部门就返回所有部门
        $user_bo_dept = Para::user_bo_val('emp_dept');

        // 3 查询出所有的部门,
        // 有限制公司，则只留限制公司的记录
        // 有限制部门，则只留下限制的部门
        //$dept_list = Emp::emp_dept();

        return ;
    }

    /**
     * 目录层级结构
     * @param $data
     * @return array|mixed
     */
    function get_tree($data) {
        $list = model($data['table'])->select();
        foreach ($list as $key => $val) {
            $list[$key]['state'] = $data['state'] ? $data['state'] : 'closed';
            $list[$key]['text'] = $val[$data['text']];
            $list[$key]['url'] = $val[$data['url']];
        }
        $list = unlimited_layer($list, $data['parent_id'], $data['children_id']);

        return $list;
    }

}
