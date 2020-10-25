<?php
/**
 * Created by PhpStorm.
 * User: Yang
 * Date: 2017-11-21
 * Time: 15:35
 */

namespace app\common\logic;

use app\common\api\Para;
use app\common\model\eba\Eba;
use app\common\model\eba\EbaGroupMember;
use app\common\model\eba\EbaGroupShare;
use app\common\model\eba\EbaService;
use app\common\model\ebs\EbsVr;
use app\common\model\mio\MioAccountIo;
use app\common\model\mup\MupUserBo;
use think\Db;
use think\Log;

/**
 * Class Eba
 * @package app\common\logic
 */
class EbaLogic {

    /**
     * 返回用户权限内的服务区域
     * @param string $type
     * @param null   $user_id
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public static function user_eba_service($type = 'user', $user_id = null) {
        $eba_service = new EbaService();
        $field = ['service_id', 'service_name', 'parent_service_id'];
        switch ($type) {
            case 'user':
                if ($user_id == null) {
                    $user_id = session('user_id');
                }
                $where['user_id'] = $user_id;

                $bo_val = Para::user_bo_val_now($user_id, 'eba_service');
                if (empty($bo_val)) {
                    $where['service_id'] = ['in', $bo_val];
                }
                // 不使用sql查询，直接从缓存中获取，再编辑逻辑处理数组
                $service_list = $eba_service->where(['service_id' => ['in', $bo_val]])->field($field)->select()->toArray();
                break;
            default:
                $service_list = $eba_service->field($field)->select()->toArray();

        }
        return $service_list;
    }

    /**
     * 获得客户列表
     * @param $type
     * $type : 'all', 'emp', 'eba_service', 'share'
     * @param $field
     * @return array
     */
    public static function get_eba_list($type, $field) {
        $eba = new Eba();
        $emp_id = session('emp_id');
        $user_id = session('user_id');
        $field = empty($field) || [
                'eba_id', 'eba_name', 'should_in', 'linkman', 'office_no',
                'mobile_no', 'e_mail', 'last_touch_date', 'last_sell_date',
                'address', 'other_im_no', 'eba_grade',
                'gender', 'state', 'service_id',
            ];

        $result = [];
        switch ($type) {
            case 'all':
                $result = $eba->field($field)->select()->toArray();
                break;
            case 'emp':
                $result = $eba->where(['emp_id' => $emp_id])->field($field)->select()->toArray();
                break;
            case 'eba_service':
                $user_eba_service = MupUserBo::all(['user_id' => $user_id, 'bo_id' => 'eba_service'])->toArray();
                if (!empty($user_eba_service)) {
                    $result = $eba->where(['service_id' => ['in', array_column($user_eba_service, 'val')]])->field($field)->select()->toArray();
                }
                break;
            case 'share':
                $share_eba_group = EbaGroupShare::all(['user_id' => $user_id])->toArray();
                if (!empty($share_eba_group)) {
                    $eba_groups = array_column($share_eba_group, 'group_id');
                    $eba_ids = EbaGroupMember::all(['group_id' => ['in', $eba_groups]])->toArray();
                    if (!empty($eba_ids)) {
                        $eba_id_val = array_column($eba_ids, 'eba_id');
                        $result = $eba->where(['eba_id' => ['in', $eba_id_val]])->field($field)->select()->toArray();
                    }
                }
                break;
        }
        return $result;
    }


    /**
     * 检查是否能删除某条客户信息
     * 1 有业务单据使用就不能删除
     * 2 有收支单据使用就不能删除
     * @param $eba_id
     * @return mixed
     */
    public static function remove_check($eba_id = null) {
        if($eba_id == null){
            return [
                'result' => false,
                'info' => lang('请传递参数') . ' $eba_id'
            ];
        }
        $ebs_vr = new EbsVr();
        $where['eba_id'] = $eba_id;
        $result = $ebs_vr->field('voucher_id')->where($where)->select()->toArray();
        if(!empty($result)){
            return [
                'result' => false,
                'info' => lang('已有业务单据使用了该客户')
            ];
        }
        $mio_account_io = new MioAccountIo();
        $result = $mio_account_io->field('voucher_id')->where($where)->select()->toArray();
        if(!empty($result)){
            return [
                'result' => false,
                'info' => lang('已有收支单据使用了该客户')
            ];
        }
        return [
            'result' => true,
            'info' => lang('没有业务单据与收支单据使用该客户')
        ];
    }

    /**
     * 检测是否有客户
     * @param        $search
     * @param string $type 是否精确查找
     * @return array
     */
    public static function is_exist($search, $type = 'N') {
        $eba = new Eba();
        $search_field = 'eba_id|eba_name|office_no|mobile_no';
        if($type == 'Y') {
            $search_field = 'eba_id|eba_name';
        }
        $result = $eba->where($search_field, $search)->field('eba_id')->select()->toArray();
        if(count($result) != 0) {
            if($type == 'Y') {
                return $result[0]['eba_id'];
            }
            return true;
        }
        return false;
    }

    /**
     * 添加客户资料
     * @param       $data
     * @param array $other_field
     * @param int   $eba_bit_num
     * @return bool|\think\response\Json
     */
    public static function add($data, $other_field = [], $eba_bit_num = 4) {
        $base_field = [
            'eba_id', 'eba_name', 'linkman', 'office_no', 'state',
            'mobile_no', 'e_mail', 'address', 'other_im_no', 'order_id',
            'emp_id', 'dept_id', 'service_id'
        ];

        Db::startTrans();
        try {
            $eba = new Eba();
            $base_field = array_merge($base_field, $other_field);
            if (empty($data['service_id'])) {
                return json(lang('请定义默认新增客户所在的区域'));
            }
            if (empty($data['eba_id'])) {
                $data['eba_id'] = $eba->get_new_id(null, 'pad', '', $eba_bit_num);
            }
            if (empty($data['order_id'])) {
                $data['order_id'] = $eba->get_new_order_id('order_id');
            }

            $eba->allowField($base_field)->save($data);
            Db::commit();
            return $data['eba_id'];
        } catch (\Exception $e) {
            Log::record(print_r($e, true), 'error');
            Log::record(lang('新增记录') . ' eba ' . lang('失败'), 'error');
            Db::rollback();
            return false;
        }
    }


}