<?php

/**
 * 店铺入住模型
 */

namespace app\common\model;
use think\facade\Db;


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
 * 数据层模型
 */
class Storejoinin extends BaseModel {
    public $page_info;


    /**
     * 读取列表
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @param int $pagesize 分页
     * @param string $order 排序
     * @param string $field 字段
     * @return array
     */
    public function getStorejoininList($condition, $pagesize = '', $order = '', $field = '*') {
        if($pagesize){
            $result = Db::name('storejoinin')->field($field)->where($condition)->order($order)->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $result;
            return $result->items();
        }else{
            $result = Db::name('storejoinin')->field($field)->where($condition)->order($order)->select()->toArray();
            return $result;
        }
    }

    /**
     * 店铺入住数量
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return int
     */
    public function getStorejoininCount($condition) {
        return Db::name('storejoinin')->where($condition)->count();
    }

    /**
     * 读取单条记录
     * @access public
     * @author csdeshang
     * @param array $condition 条件
     * @return array
     */
    public function getOneStorejoinin($condition) {
        $result = Db::name('storejoinin')->where($condition)->find();
        return $result;
    }

    /**
     * 判断是否存在
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @return boolean
     */
    public function isStorejoininExist($condition) {
        $result = $this->getOneStorejoinin($condition);
        if (empty($result)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * 增加
     * @access public
     * @author csdeshang
     * @param type $data 数据
     * @return type
     */
    public function addStorejoinin($data) {
        return Db::name('storejoinin')->insert($data);
    }


    /**
     * 更新
     * @access public
     * @author csdeshang
     * @param type $update 数据
     * @param type $condition 条件
     * @return type
     */
    public function editStorejoinin($update, $condition) {
        return Db::name('storejoinin')->where($condition)->update($update);
    }

    /**
     * 删除
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @return type
     */
    public function delStorejoinin($condition) {
        return Db::name('storejoinin')->where($condition)->delete();
    }



}
