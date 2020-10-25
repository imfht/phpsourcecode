<?php
namespace app\common\api;
use app\common\model\mup\MupFrameNag;
use app\common\model\mup\MupNagGroup;
use app\common\model\mup\MupUserFrame;
use app\common\model\mup\MupUserNagGroup;
use app\common\model\mup\MupUserNagGroupItem;


/**
 * Class Frame
 * @package app\common\api
 */
class Frame {

    /**
     * 根据用户查找有没有定义界面风格
     * @param $user_id
     * @return mixed
     */
    public static function user_frame_id($user_id = null) {
        $mup_user_frame = new MupUserFrame();
        if ($user_id == null) {
            $user_id = session('user_id');
        }
        $where['user_id'] = $user_id;
        $frame_id = $mup_user_frame->where($where)->value('frame_id');
        //if ($frame_id == null) {
        //    return null;
        //}
        return $frame_id;
    }

    /**
     * 根据风格id,返回所属的导航分组
     * @param      $frame_id
     * @param null $field
     * @return mixed
     */
    public static function frame_nag($frame_id = null, $field = null) {
        $mup_frame_nag = new MupFrameNag();
        if ($frame_id == null) {
            $frame_id = input('frame_id');
        }
        if(empty($field)){
            $field = ['frame_id', 'nag_group_id', 'order_id'];
        }
        $where['frame_id'] = $frame_id;
        $nags = $mup_frame_nag->where($where)->field($field)->order('order_id')->select()->toArray();

        return $nags;
    }

    /**
     * 根据用户绑定的界面id，返回界面分组的信息
     * @param $frame_id
     * @return false|mixed|\PDOStatement|string|\think\Collection
     */
    public static function user_nag_group($frame_id = null) {
        if ($frame_id == null) {
            $frame_id = input('frame_id');
        }
        if ($frame_id == null) {
            return lang('需要' . '$frame_id');
        }

        $where['frame_id'] = $frame_id;
        $user_nag_group = new MupUserNagGroup();
        $nag_group = $user_nag_group->where($where)->field(['frame_id', 'nag_group_id', 'name', 'order_id'])->order('order_id')->select()->toArray();

        return $nag_group;
    }

    /**
     * @param      $frame_id
     * @param null $nag_group_id
     * @return mixed
     */
    public static function user_nag_group_item($frame_id, $nag_group_id = null) {
        if ($frame_id == null) {
            $frame_id = input('frame_id');
        }
        if ($frame_id == null) {
            return lang('需要' . '$frame_id');
        }
        $where['frame_id'] = $frame_id;
        $nag_group_item = new MupUserNagGroupItem();
        $nag_func = $nag_group_item->where($where)->field([
            'dll_id',
            'func_id',
            'item_type',
            'nag_group_id',
            'name',
            'order_id',
        ])->order('nag_group_id,order_id')->select()->toArray();

        return $nag_func;
    }

    /**
     * 返回系统的导航分组信息
     * @param null $nag_group_id
     * @param null $field
     * @return array
     */
    public static function nag_group($nag_group_id = null, $field = null) {
        $where = [];
        if ($nag_group_id) {
            $where['nag_group_id'] = $nag_group_id;
        }
        if(empty($field)){
            $field = ['nag_group_id', 'name', 'note_info'];
        }
        $mup_nag_group = new MupNagGroup();
        $nag_info = $mup_nag_group->where($where)->field($field)->select()->toArray();
        return $nag_info;
    }
}
