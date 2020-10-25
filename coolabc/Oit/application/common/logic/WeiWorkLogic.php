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
use app\common\model\eba\Eba;
use app\common\model\eba\EbaGroupMember;
use app\common\model\eba\EbaGroupShare;
use app\common\model\eba\EbaService;
use think\Config;
use think\Db;

/**
 * Class WeiWorkLogic
 * @package app\common\logic
 */
class WeiWorkLogic {

    /**
     * 根据用户获取此用户可查看的客户列表
     * 企业微信, 客户管理 Eba
     * @param $user_info
     * @return array
     */
    public static function wk_get_eba_list($user_info = null) {
        if ($user_info == null) {
            return lang("缺少参数") . ' $user_info';
        }
        $eba = new Eba();
        $field = [
            'eba_id', 'eba_name', 'should_in', 'linkman', 'office_no',
            'mobile_no', 'e_mail', 'last_touch_date', 'last_sell_date',
            'address', 'other_im_no', 'eba_grade',
            'gender', 'state', 'service_id',
        ];
        $format_list = [];

        // 所需要字典数据
        $dict_data = [];
        foreach ($eba->field_dict_need as $v) {
            // 这个字典不能使用 字典默认的查询方法
            if ($v != 'eba_service') {
                $dict_data[$v] = Dict::get_dict($v);
            }
        }
        $dict_data['eba_service'] = EbaLogic::user_eba_service('all');


        if ('Y' == $user_info['is_admin']) {
            // 1 如果是超级管理员，可看所有客户资料
            $eba_list = EbaLogic::get_eba_list('all', $field);
            $eba_list = Dict::data_add_dict_name($dict_data, $eba->field_dict_def, $eba_list);
            if (!empty($eba_list)) {
                $format_list = [[
                    'service_name' => '所有客户',
                    'ebas' => $eba_list,
                ]];
            }
        } else {
            // 2 普通操作员
            //   获得同名操作员id 与 操作员绑定的部门业务员id
            //   获得业务员绑定的客户列表
            $eba_list = EbaLogic::get_eba_list('emp', $field);
            if (!empty($eba_list)) {
                $eba_list = Dict::data_add_dict_name($dict_data, $eba->field_dict_def, $eba_list);
                $format_list = [[
                    'service_name' => '本人的',
                    'ebas' => $eba_list,
                ]];
            }

            // 如果用户有绑定区域就获得区域中所有的客户
            $eba_service_eba_list = EbaLogic::get_eba_list('eba_service', $field);
            if (!empty($eba_service_eba_list)) {
                // 去重
                $eba_service_eba_list = array_diff_ext($eba_service_eba_list, $eba_list, 'eba_id');
                if (!empty($eba_service_eba_list)) {
                    $eba_service_eba_list = Dict::data_add_dict_name($dict_data, $eba->field_dict_def, $eba_service_eba_list);
                    $format_list[] = [
                        'service_name' => '区域的',
                        'ebas' => $eba_service_eba_list,
                    ];
                    $eba_list = array_merge($eba_list, $eba_service_eba_list);
                }
            }

            //   获得其他操作员共享给这个用户的客户列表
            $share_eba_group_list = EbaLogic::get_eba_list('share', $field);
            if (!empty($share_eba_group_list)) {
                // 去重
                $eba_group_member_list = array_diff_ext($share_eba_group_list, $eba_list, 'eba_id');
                if (!empty($eba_group_member_list)) {
                    $eba_group_member_list = Dict::data_add_dict_name($dict_data, $eba->field_dict_def, $eba_group_member_list);
                    $format_list[] = [
                        'service_name' => '收到共享的',
                        'ebas' => $eba_group_member_list,
                    ];
                    $eba_list = array_merge($eba_list, $eba_group_member_list);
                }
            }
        }

        return $format_list;
    }

    /**
     * 获取临时的单据类型的编号
     * @param        $voucher_type
     * @param        $find_id
     * @param string $type
     * @return string
     */
    public static function ebs_v_new_no($voucher_type, $find_id, $type = 'eba') {
        // 临时网络订单编号规则,前缀-日期-客户编号-当日第多少个单据
        $voucher_no = $voucher_type . date('ymd') . '' . $find_id . '-';
        $map['a.voucher_date'] = date('Ymd');
        switch($type) {
            case 'eba':
                $map['b.eba_id'] = $find_id;
                break;
            case 'emp':
                $map['a.emp_id'] = $find_id;
                break;
        }
        $map['a.voucher_type'] = $voucher_type;
        $voucher_data = EbsLogic::ebs_v_ebs_vr($map);
        $number = count($voucher_data) + 1;
        $voucher_no .= str_pad($number, 2, "0", STR_PAD_LEFT);

        return $voucher_no;
    }

    /**
     * 返回企业微信所用的 成品目录中的产品
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function res() {
        $config = Config::get('work_vr_net');
        $where['key_id'] = $config['res_cat_id'];
        $where['obj_id'] = 'res_catalog';
        $code_map = Db::table('app_tree_code_map')->where($where)->find();

        $sub_sql = "select key_id from app_tree_code_map where tree_id like '" . $code_map['tree_id'] . "%'";
        $sql = "select res_id, res_name from res where res_cat_id in(" . $sub_sql . ")";
        $result = Db::query($sql);

        return $result;
    }


}