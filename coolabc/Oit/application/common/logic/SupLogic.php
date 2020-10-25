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
use app\common\model\sup\Sup;
use app\common\model\sup\SupService;
use app\common\model\ebs\EbsVr;
use app\common\model\mio\MioAccountIo;
use app\common\model\mup\MupUserBo;
use think\Db;
use think\Log;

/**
 * Class Sup
 * @package app\common\logic
 */
class SupLogic {

    /**
     * 返回用户权限内的服务区域
     * @param string $type
     * @param null   $user_id
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    public static function user_sup_service($type = 'user', $user_id = null) {
        $eba_service = new SupService();
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
     * 获得供应商列表
     * @param $type
     * $type : 'all', 'emp', 'sup_service',
     * 供应商没有共享功能 'share'
     * @param $field
     * @return array
     */
    public static function get_sup_list($type, $field) {
        $sup = new Sup();
        $emp_id = session('emp_id');
        $user_id = session('user_id');
        $field = empty($field) || [
                'sup_id', 'sup_name',
            ];

        $result = [];
        switch ($type) {
            case 'all':
                $result = $sup->field($field)->select()->toArray();
                break;
            case 'emp':
                $result = $sup->where(['emp_id' => $emp_id])->field($field)->select()->toArray();
                break;
            case 'sup_service':
                $user_sup_service = MupUserBo::all(['user_id' => $user_id, 'bo_id' => 'sup_service'])->toArray();
                if (!empty($user_sup_service)) {
                    $result = $sup->where(['service_id' => ['in', array_column($user_sup_service, 'val')]])->field($field)->select()->toArray();
                }
                break;
            /*
            case 'share':
                $share_eba_group = EbaGroupShare::all(['user_id' => $user_id])->toArray();
                if (!empty($share_eba_group)) {
                    $eba_groups = array_column($share_eba_group, 'group_id');
                    $eba_ids = EbaGroupMember::all(['group_id' => ['in', $eba_groups]])->toArray();
                    if (!empty($eba_ids)) {
                        $eba_id_val = array_column($eba_ids, 'eba_id');
                        $result = $sup->where(['eba_id' => ['in', $eba_id_val]])->field($field)->select()->toArray();
                    }
                }
                break;
            */
        }
        return $result;
    }


    /**
     * 检查是否能删除某条客户信息
     * 1 有业务单据使用就不能删除
     * 2 有收支单据使用就不能删除
     * @param $sup_id
     * @return mixed
     */
    public static function remove_check($sup_id = null) {
        if($sup_id == null){
            return [
                'result' => false,
                'info' => lang('请传递参数') . ' $sup_id'
            ];
        }
        $ebs_vr = new EbsVr();
        $where['eba_id'] = $sup_id;
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
     * 检测是否有供应商
     * @param        $search
     * @param string $type 是否精确查找
     * @return array
     */
    public static function is_exist($search, $type = 'N') {
        $sup = new Sup();
        $search_field = 'sup_id|sup_name|office_no|mobile_no';
        if($type == 'Y') {
            $search_field = 'sup_id|sup_name';
        }
        $result = $sup->where($search_field, $search)->field('sup_id')->select()->toArray();
        if(count($result) != 0) {
            if($type == 'Y') {
                return $result[0]['sup_id'];
            }
            return true;
        }
        return false;
    }


    /**
     * 添加供应商资料
     * @param        $data
     * @param array  $other_field
     * @param int    $bit_num
     * @param string $front
     * @param null   $where
     * @return bool|\think\response\Json
     */
    public static function add($data, $other_field = [], $bit_num = 4, $front = 'B', $where = null) {
        $base_field = [
            'sup_id', 'sup_name', 'linkman', 'office_no', 'state',
            'mobile_no', 'e_mail', 'address', 'other_im_no', 'order_id',
            'emp_id', 'dept_id', 'service_id'
        ];

        Db::startTrans();
        try {
            $sup = new Sup();
            $base_field = array_merge($base_field, $other_field);
            if (empty($data['service_id'])) {
                return json(lang('请定义默认供应商所在的区域'));
            }
            if (empty($data['sup_id'])) {
                $data['sup_id'] = $sup->get_new_id($where, 'pad', $front, $bit_num);
            }
            if (empty($data['order_id'])) {
                $data['order_id'] = $sup->get_new_order_id('order_id');
            }

            $sup->allowField($base_field)->save($data);
            Db::commit();
            return $data['sup_id'];
        } catch (\Exception $e) {
            Log::record(print_r($e, true), 'error');
            Log::record(lang('新增记录') . ' sup ' . lang('失败'), 'error');
            Db::rollback();
            return false;
        }
    }

}