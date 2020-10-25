<?php
/**
 * Created by PhpStorm.
 * User: Yang
 * Date: 2017-11-21
 * Time: 15:35
 */

namespace app\common\logic;

use app\common\api\Dict;
use app\common\api\Para;
use app\common\model\emp\Emp;
use app\common\model\emp\EmpCompany;
use app\common\model\emp\EmpDept;


/**
 * Class Emp
 * @package app\common\logic
 */
class EmpLogic {
    /**
     * 用户可见的公司
     * 1 超级管理员
     * 2 普通用户
     * @return array
     */
    public static function get_emp_company() {
        $user_id = session('user_id');
        $is_admin = session('is_admin');
        $emp_company = new EmpCompany();
        //if('Y' == $is_admin){
        //    $list = $emp_company->order('order_id')->select()->toArray();
        //} else {
        // 判断用户有没有绑定公司
        // 1 有，只能看绑定的公司信息
        // 2 没有，可以看所有公司信息
        if (Para::user_bo_has('emp_company')) {
            $user_bo_val = Para::user_bo_val('emp_company');
            $list = $emp_company->where(["company_id" => ["in", $user_bo_val]])->order('order_id')->select()->toArray();
        } else {
            $list = $emp_company->order('order_id')->select()->toArray();
        }
        //}

        return $list;
    }

    /**
     * 返回用户可见的部门
     * @return array|mixed
     */
    public static function get_emp_dept() {
        $user_id = session('user_id');
        $is_admin = session('is_admin');
        $emp_dept = new EmpDept();
        //if('Y' == $is_admin){
        //    $list = $emp_company->order('order_id')->select()->toArray();
        //} else {

        // 判断用户有没有绑定公司
        // 1 有，只能看绑定的公司信息
        // 2 没有，可以看所有公司信息
        $where = [];
        if(Para::user_bo_has('emp_company')){
            $where['company_id'] = ["in", Para::user_bo_val('emp_company')];
        }

        // 判断用户有没有绑定部门
        // 1 有，只能看绑定的公司信息
        // 2 没有，可以看公司中所有部门的信息
        if (Para::user_bo_has('emp_dept')) {
            $where['dept_id'] = ["in", Para::user_bo_val('emp_dept')];
        }

        $list = $emp_dept->where($where)->order('order_id')->select()->toArray();
        //}

        return $list;
    }

    /**
     * 获取员工信息
     */
    public static function get_emp_data() {
        $emp = new Emp();
        $list = $emp->select()->toArray();
        // 所需要字典数据
        $dict_data = [];
        foreach ($emp->field_dict_need as $v) {
            $dict_data[$v] = Dict::get_dict($v);
        }
        $list = Dict::data_add_dict_name($dict_data, $emp->field_dict_def, $list);
        return $list;
    }

}