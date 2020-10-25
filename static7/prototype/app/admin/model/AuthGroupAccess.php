<?php

namespace app\admin\model;

use think\Model;

/**
 * Description of AuthGroupAccess
 * 权限用户组
 * @author static7
 */
class AuthGroupAccess extends Model {
    /**
     * 查询用户组
     * @param int $uid 用户ID
     * @param int $group_id 用户组ID
     * @author staitc7 <static7@qq.com>
     */

    public function removeFromGroup(int $uid, int $group_id) {
        $map = [
            'uid' => $uid,
            'group_id' => $group_id
        ];
        return $this::where($map)->delete();
    }

    /**
     * 把用户添加到用户组
     * @param array $user_ids 用户或者多个用户
     * @param int $group_id 用户组
     * @author staitc7 <static7@qq.com>
     */

    public function addToGroup(array $user_ids, int $group_id) {
        $map ['group_id'] = $group_id;
        $repeat = [];
        foreach ($user_ids as $v) {
            $map['uid'] = $v;
            //检查用户是否已经所在该用户组
            $object = $this::get(function($query)use($map) {
                        $query->where($map)->field('uid');
                    });
            if ($object) {
                $repeat[] = $object->uid;
                continue;
            }
            $this::create($map);
        }
        !empty($repeat) && $info = '用户编号' . implode(',', $repeat) . '已经加入该组，不再重复添加';
        return $this->getError() ? ['status' => false, 'info' => $this->getError()] : ['status' => true, 'info' => $info ?? '操作成功'];
    }

    /**
     * 方法名称或者用途
     * @param int $user_id 用户
     * @param int $group_id 用户组或者多个用户组
     * @author staitc7 <static7@qq.com>
     */

    public function userToGroup(int $user_id, array $group_id = []) {
        $map ['uid'] = $user_id;
        //删除原来的用户组
        $this::destroy(function($query)use($map) {
            $query->where($map);
        });
        if (!empty($group_id)) {
            //添加新的用户组
            foreach ($group_id as $v) {
                $map['group_id'] = $v;
                $this::create($map);
            }
        }
        return $this->getError() ? ['status' => false, 'info' => $this->getError()] : ['status' => true, 'info' => '操作成功'];
    }

}
