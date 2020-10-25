<?php

namespace app\admin\model;

use think\Model;
use think\Config;
/**
 * User模型
 * @author staitc7 <static7@qq.com>
 */

class User extends Model {
    /**
     * 用户列表
     * @author staitc7 <static7@qq.com>
     */

    public function userList(): array {
        $map = [
            'status' => ['neq', -1]
        ];
        $object = $this::where($map)->order('sort asc,id asc')->paginate(Config::get('list_rows') ?? 10);
        
        return $object ? array_merge($object->toArray(), ['page' => $object->render()]) : [];
    }

}
