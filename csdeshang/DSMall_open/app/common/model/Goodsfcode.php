<?php
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
class Goodsfcode extends BaseModel
{
    /**
     * 插入数据
     * @access public
     * @author csdeshang
     * @param array $data 参数内容
     * @return boolean
     */
    public function addGoodsfcodeAll($data) {
        return Db::name('goodsfcode')->insertAll($data);
    }

    /**
     * 取得F码列表
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @param type $order 排序
     * @return type
     */
    public function getGoodsfcodeList($condition, $order = 'goodsfcode_state asc,goodsfcode_id asc') {
        return Db::name('goodsfcode')->where($condition)->order($order)->select()->toArray();
    }

    /**
     * 删除F码
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @return bool
     */
    public function delGoodsfcode($condition) {
        return Db::name('goodsfcode')->where($condition)->delete();
    }

    /**
     * 取得F码
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @return bool
     */
    public function getGoodsfcode($condition) {
        return Db::name('goodsfcode')->where($condition)->find();
    }

    /**
     * 更新F码
     * @access public
     * @author csdeshang
     * @param array $data 更新数据
     * @param array $condition 条件
     * @return bool
     */
    public function editGoodsfcode($data, $condition) {
        return Db::name('goodsfcode')->where($condition)->update($data);
    }
}