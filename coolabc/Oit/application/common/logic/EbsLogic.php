<?php
/**
 * Created by PhpStorm.
 * User: Yang
 * Date: 2017-11-21
 * Time: 15:35
 */

namespace app\common\logic;

use app\common\api\Common;
use app\common\api\Dict;
use app\common\api\Para;
use app\common\model\ebs\EbsV;
use app\common\model\ebs\EbsVAttr;
use app\common\model\ebs\EbsVr;
use app\common\model\ebs\EbsVrItem;
use app\common\model\ebs\EbsVrItemAttr;
use think\Db;
use think\Log;

/**
 * Class EbsLogic
 * @package app\common\logic
 */
class EbsLogic {

    /**
     * 物资单据ebs_v 与 ebs_vr视图
     * 再关联 ebs_v_attr
     * @param $where
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function ebs_v_ebs_vr($where = null) {
        if ($where == null) {
            return lang("缺少参数") . '$where';
        }
        $result = Db::view([
            'ebs_v a' => [
                true,
            ],
            'ebs_vr b' => [
                [
                    'ass_no',
                    'emf_v_no',
                    'eba_type',
                    'eba_id',
                    'emf_shop_id',
                    'emf_center_id',
                    'emf_process_id',
                    'edt_id',
                    'target_edt_id',
                    'mio_account',
                    'mio_method_id',
                    'discount_amount',
                    'draw_percent',
                    'draw_amount',
                    'mem_card_no',
                    'mem_card_pay_amount',
                    'bank_card_pay_amount',
                    'gift_ticket_pay_amount',
                    'io_amount',
                    'pre_amount',
                    'ebm_brand_id',
                    'money_type',
                    'money_factor',
                    'date_lmt_res',
                    'date_lmt_ebm',
                    'project_id',
                ],
                'a.voucher_id=b.voucher_id',
            ],
        ])->where($where)->select();

        return $result;
    }

    /**
     * 返回指定条件的明细数据
     * @param      $where // 查询条件
     * @param bool $need_dict_name // 是否需要加入字典名称列
     * @return array|mixed
     */
    public static function ebs_vr_item($where, $need_dict_name = true) {
        $ebs_vr_item = new EbsVrItem();
        $result = $ebs_vr_item->where($where)->order(['voucher_id', 'main_id', 'item_id'])->select()->toArray();
        if ($need_dict_name) {
            $result = Dict::data_add_dict_name($ebs_vr_item->field_dict_need, $ebs_vr_item->field_dict_def, $result);
        }
        return $result;
    }

    /**
     * 获取默认的单据的临时编号
     * oit系统的单据编号规则定义数据表
     * 如果有需要额外的单据编号规则，在单独的逻辑中编写
     * @param $voucher_type
     */
    public static function ebs_v_new_no($voucher_type) {
    }

    /**
     * 保存单据数据
     * 相关数据表, ebs_v, ebs_vr, ebs_vr_item,
     * 1 没有单据编号，需要获取最新的单据流水号
     *      单据流水号根据 流水序列获取，根据单据的行数
     *      因为单据行可能会比较大，不如单独用一个流水序列节省统计行数的性能
     *   有单据编号，就是修改，修改也是删除单据，再添加单据
     *   需要获取到单据的配置参数，将库存、账户流水、业务量的影响，添加到单据数据中。然后再保存
     *   根据配置参数，是否写入相关数据表
     * 2 获取默认的单据创建人
     * 3 客户绑定的 公司业务员 与 部门
     * 4 把单据编号返回给客户端
     * @param $data
     * @return int
     */
    public static function ebs_v_save($data) {
        $edit_type = 'edit';
        $ebs_v = $data['ebs_v'];
        $ebs_v_attr = $data['ebs_v_attr'];
        $ebs_vr = $data['ebs_vr'];
        $ebs_vr_item = $data['ebs_vr_item'];
        $ebs_vr_item_attr = $data['ebs_vr_item_attr'];

        //Log::write(print_r($data), 'notice');

        // 需要根据单据配置参数，判断是否需要写入业务量、库存出入库流水账、资金流水账、虚拟库存出入
        // *********暂时就完成了网络订单，网络订单对上面的数据都不产生影响*********

        // 新的单据流水号
        if ($ebs_v['voucher_id'] == '') {
            // 检查单据编号已经被使用过
            $where = [];
            $where['voucher_id'] = ['<>', 0];
            $where['voucher_no'] = $ebs_v['voucher_no'];
            $check_no = Db::table('ebs_v')->where($where)->find();
            if (count($check_no) > 0) {
                return [
                    'result' => 'error',
                    'info' => lang('单据编号 已经被使用'),
                ];
            }

            Db::startTrans();
            try {
                $where = [];
                $where['seq_name'] = 'seq_voucher_id';
                Db::table('app_sequence')->where($where)->setInc('seq_val');
                $seq = Db::table('app_sequence')->where($where)->find();
                $ebs_v['voucher_id'] = $seq['seq_val'];
                $edit_type = 'new';
                Db::commit();
            } catch (\Exception    $e) {
                Db::rollback();
            }
        }
        Log::write($ebs_v, 'record');

        // 检查单据编号已存在
        $where = [];
        $where['voucher_id'] = ['<>', $ebs_v['voucher_id']];
        $where['voucher_no'] = $ebs_v['voucher_no'];
        $check_no = Db::table('ebs_v')->where($where)->find();
        if (count($check_no) > 0) {
            return [
                'result' => 'error',
                'info' => lang('单据编号 已经被使用'),
            ];
        }

        // 增加 voucher_id
        $ebs_vr['voucher_id'] = $ebs_v['voucher_id'];

        foreach ($ebs_vr_item as $k => $v) {
            $ebs_vr_item[$k]['voucher_id'] = $ebs_v['voucher_id'];
        }

        foreach ($ebs_vr_item_attr as $k => $v) {
            $ebs_vr_item_attr[$k]['voucher_id'] = $ebs_v['voucher_id'];
        }

        foreach ($ebs_v_attr as $k => $v) {
            $ebs_v_attr[$k]['voucher_id'] = $ebs_v['voucher_id'];
        }

        //Log::write(print_r($ebs_v, true), 'notice');
        //Log::write(print_r($ebs_v_attr, true), 'notice');
        //Log::write(print_r($ebs_vr, true), 'notice');
        //Log::write(print_r($ebs_vr_item, true), 'notice');
        //Log::write(print_r($ebs_vr_item_attr, true), 'notice');

        Db::startTrans();
        try {
            // 删除之前
            $where = [];
            $where['voucher_id'] = $ebs_v['voucher_id'];
            if ($edit_type == 'edit') {
                // 检查单据状态与打印次数
                // 防止A,B编辑同一个单据，A先审核了，而B后保存
                $check_state = Db::table('ebs_v')->where($where)->find();
                if (count($check_state)) {
                    // 只有待审核的单据才允许保存
                    if ($check_state['state'] != 'A') {
                        return [
                            'result' => 'error',
                            'info' => lang('单据非编辑状态，不允许保存(其他操作员已操作)'),
                        ];
                    }
                    $ebs_v['print_times'] = $check_state['print_times'];
                }

                // todo
                // 1. 页面提交中含所有数据
                // 2. 页面提交中缺少部分数据
                // 检查此单据是否有引用过其他单据
                // 如果有引用过其他单据,那么应该重写引用的数据
                // 如果从其他单据中引用之后,再手动修改数量,引用数量如何变化?
                // 引用数据,应该直接就查询显示在单据中,再保存时同时提交这些数据.
                /*
                $check_ref_other_vr = Db::table('ebs_vr_item')->distinct(true)->field(['ref_voucher_id', 'ref_voucher_type'])->where($where)->select();
                if (count($ebs_vr_item)) {

                }
                */

            }
            // 先删除再保存
            $m_ebs_v = new EbsV();
            $m_ebs_v_attr = new EbsVAttr();
            $m_ebs_vr = new EbsVr();
            $m_ebs_vr_item = new EbsVrItem();
            $m_ebs_vr_item_attr = new EbsVrItemAttr();

            $m_ebs_v->where($where)->delete();
            $m_ebs_v_attr->where($where)->delete();
            $m_ebs_vr->where($where)->delete();
            $m_ebs_vr_item->where($where)->delete();
            $m_ebs_vr_item_attr->where($where)->delete();

            // 保存单据 ebs_v
            $m_ebs_v->allowField(true)->save($ebs_v);
            // 保存单据扩展属性 ebs_v_attr
            if (count($ebs_v_attr)) {
                $m_ebs_v_attr->allowField(true)->saveAll($ebs_v_attr);
            }
            // 保存单据参数 ebs_vr
            $m_ebs_vr->allowField(true)->save($ebs_vr);
            // 保存单据明细数据 ebs_vr_item
            $m_ebs_vr_item->allowField(true)->saveAll($ebs_vr_item);
            // 保存单据明细数据 ebs_vr_item_attr
            if (count($ebs_vr_item_attr)) {
                $m_ebs_vr_item_attr->allowField(true)->saveAll($ebs_vr_item_attr);
            }

            Db::commit();
        } catch (\Exception    $e) {
            Log::record($e, 'record');
            Db::rollback();
            return [
                'result' => 'error',
                'info' => lang('保存失败'),
                'voucher_id' => $ebs_v['voucher_id'],
            ];
        }
        return [
            'result' => 'success',
            'info' => lang('保存成功'),
            'voucher_id' => $ebs_v['voucher_id'],
        ];
    }

    /**
     * 给ebs_v单据增加 扩展属性值
     * 1 只给一种单据类型添加属性扩展值
     * 2 同时给多种单据类型添加属性扩展值(这样极有可能造成列的数量不一致)
     * @param     $vr
     * @param int $type
     * @return int
     */
    public static function ebs_v_add_attr(&$vr, $type = 1) {
        $ebs_v_attr_def = [];
        if ($type == 1) {
            $where = [];
            $where['obj_id'] = 'voucher.' . $vr[0]['voucher_type'];
            $ebs_v_attr_def = Db::table('app_attr_def')->where($where)->select();
        }
        if (count($vr)) {
            foreach ($vr as $k => $v) {
                if ($type == 2) {
                    $where = [];
                    $where['obj_id'] = 'voucher.' . $vr[$k]['voucher_type'];
                    $ebs_v_attr_def = Db::table('app_attr_def')->where($where)->select();
                }
                $where = [];
                $where['voucher_id'] = $vr[$k]['voucher_id'];
                $ebs_v_attr = Db::table('ebs_v_attr')->where($where)->select();
                foreach ($ebs_v_attr_def as $ki => $kv) {
                    foreach ($ebs_v_attr as $kk => $vv) {
                        if ($vv['attr_id'] == $kv['attr_id']) {
                            $vr[$k][$vv['attr_id']] = $vv['attr_val'];
                        }
                    }
                }
            }
        }

        return 1;
    }

    /**
     * 给ebs_vr_item 单据增加 扩展属性值 ebs_vr_item_attr
     * 1 只给一种单据类型添加属性扩展值
     * 2 同时给多种单据类型明细添加属性扩展值(这样极有可能造成列的数量不一致)
     * @param     $vr_item
     * @param int $type
     * @return int
     */
    public static function ebs_vr_item_add_attr(&$vr_item, $type = 1) {
        $ebs_vr_item_attr_def = [];
        if ($type == 1) {
            $where = [];
            $where['voucher_id'] = $vr_item[0]['voucher_id'];
            $ebs_v = Db::table('ebs_v')->where($where)->select();

            $where = [];
            $where['obj_id'] = 'voucher.item.' . $ebs_v[0]['voucher_type'];
            $ebs_vr_item_attr_def = Db::table('app_attr_def')->where($where)->select();
            //Log::write(print_r($ebs_vr_item_attr_def, true), 'notice');
        }
        if (count($vr_item)) {
            $voucher_type = '';
            foreach ($vr_item as $k => $v) {
                if ($type == 2) {
                    $where = [];
                    $where['voucher_id'] = $vr_item[$k]['voucher_id'];
                    $ebs_v = Db::table('ebs_v')->where($where)->select();

                    if (empty($ebs_vr_item_attr_def) || $voucher_type != $ebs_v[0]['voucher_type']) {
                        $where = [];
                        $where['obj_id'] = 'voucher.item.' . $ebs_v[0]['voucher_type'];
                        $ebs_vr_item_attr_def = Db::table('app_attr_def')->where($where)->select();
                        $voucher_type = $ebs_v[0]['voucher_type'];
                    }
                }
                $where = [];
                $where['voucher_id'] = $vr_item[$k]['voucher_id'];
                $where['item_id'] = $vr_item[$k]['item_id'];
                $ebs_vr_item_attr = Db::table('ebs_vr_item_attr')->where($where)->select();
                foreach ($ebs_vr_item_attr_def as $ki => $kv) {
                    foreach ($ebs_vr_item_attr as $kk => $vv) {
                        if ($kv['attr_id'] == $vv['attr_id'] && $vv['item_id'] == $v['item_id']) {
                            $vr_item[$k][$vv['attr_id']] = $vv['attr_val'];
                        }
                    }
                }
            }
        }

        return 1;
    }

    /**
     * 返回单据的静态配置参数
     * 如果类似的系统内置的查找,只有一个参数
     * 可在逻辑中形成常用方法
     * @param $voucher_type
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function ebs_vr_ctl_data($voucher_type) {
        $where = [];
        $where['voucher_type'] = $voucher_type;
        $ebs_vr_ctl_config = Db::table('ebs_vr_ctl')->where($where)->find();

        // 业务量的影响
        $ebs_vr['eba_res_oper_flag'] = $ebs_vr_ctl_config['eba_res_oper_flag'];
        // 预收的影响
        $ebs_vr['ebm_pre_oper_flag'] = $ebs_vr_ctl_config['ebm_pre_oper_flag'];
        // 应收的影响
        $ebs_vr['ebm_should_oper_flag'] = $ebs_vr_ctl_config['ebm_should_oper_flag'];
        // 产品出借的影响
        $ebs_vr['item_res_edt_eba_oper_flag'] = $ebs_vr_ctl_config['item_res_edt_eba_oper_flag'];
        // 明细对哪个库存影响
        $ebs_vr['item_res_edt_flag'] = $ebs_vr_ctl_config['item_res_edt_flag'];
        // 明细对库存量影响
        $ebs_vr['item_res_edt_oper_flag'] = $ebs_vr_ctl_config['item_res_edt_oper_flag'];
        // 主物资对哪个库存影响
        $ebs_vr['main_res_edt_flag'] = $ebs_vr_ctl_config['main_res_edt_flag'];
        // 主物资对库存量的影响
        $ebs_vr['main_res_edt_oper_flag'] = $ebs_vr_ctl_config['main_res_edt_oper_flag'];
        // 单据对收支明细账的影响
        $ebs_vr['mio_oper_flag'] = $ebs_vr_ctl_config['mio_oper_flag'];
        return $ebs_vr;
    }

    /**
     * 根据 voucher_no 找 voucher_id
     * @param $voucher_no
     * @return string
     */
    public static function get_voucher_id($voucher_no) {
        $m_ebs_v = new EbsV();
        $voucher_id = $m_ebs_v->where(['voucher_no' => $voucher_no])->value('voucher_id');

        return $voucher_id;
    }
}