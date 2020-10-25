<?php

namespace app\admin\model;

use think\Model;
use think\Request;
use think\Validate;
use think\Config;

/**
 * Description of AuthManager
 * 权限管理类
 * @author static7
 */
class AuthGroup extends Model {

    protected $rule = [
        'title' => "require|max:20",
    ];
    protected $msg = [
        'title.require' => '标题不能为空',
        'title.max' => '标题最多不能超过20个字符',
    ];
    protected $auto = ['description', 'title', 'type'];
    protected $insert = [
        'status' => 1,
        'module' => 'admin'
    ];
    protected $update = [];

    /**
     * 用户组菜单
     * @author staitc7 <static7@qq.com>
     */

    public function authManagerList(): array {
        $map = [
            'module' => 'admin',
            'status' => ['neq', -1]
        ];
        $object = $this::where($map)->order('id ASC')->paginate(Config::get('list_rows') ?? 10);
        return $object ? array_merge($object->toArray(), ['page' => $object->render()]) : [];
    }

    /**
     * 修改状态
     * @param int|array $map 数据的ID或者ID组
     * @param array $data 要修改的数据
     * @author staitc7 <static7@qq.com>
     * @return bool|int|string
     */

    public function setStatus($map = null, $data = null) {
        if (empty($map) || empty($data)) {
            return false;
        }
        return $this::where($map)->update($data);
    }

    /**
     * 用户组详情
     * @param int $id 用户组详情
     * @param bool $field
     * @return array
     * @author staitc7 <static7@qq.com>
     */

    public function editGroup(int $id, $field = true): array {
        $object = $this::get(function($query)use($id, $field) {
                    $query->where('id', $id)->field($field);
                });
        return $object ? $object->toArray() : [];
    }

    /**
     * 条件查询用户组
     * @param array $map 查询条件
     * @param boole|bool|string $field 查询的字段
     * @return array|null|object
     * @author staitc7 <static7@qq.com>
     */

    public function mapList($map = [], $field = true) {
        $object = $this::all(function($query)use($map, $field) {
                    $query->where($map)->field($field);
                });
        return $object ? object_to_array($object) : null;
    }

    /**
     * 用户更新或者添加用户组
     * @author staitc7 <static7@qq.com>
     */

    public function renew() {
        $data = Request::instance()->post();
        $validate = Validate::make($this->rule, $this->msg);
        if (!$validate->check($data)) {
            return $validate->getError();// 验证失败 输出错误信息
        }
        $object = (int) $data['id'] ? $this::update($data) : $this::create($data);
        return $object ? $object->toArray() : null;
    }

    /**
     * 检查用户组是否存在
     * @author staitc7 <static7@qq.com>
     * @param int $group_id 组id
     * @return bool|mixed
     */

    public function checkGroupId(int $group_id) {
        $object = $this::get($group_id);
        return $object ? $object->id : false;
    }

    /**
     * 给用户授权
     * @param int $id 用户ID
     * @author staitc7 <static7@qq.com>
     */

    public function userAuthorize(int $id = 0) {
        (int) $id || $this->error='用户ID丢失';
        
    }

    /**
     * 过滤非法字符description
     * @author staitc7 <static7@qq.com>
     */

    protected function setDescriptionAttr($value) {
        return htmlspecialchars($value);
    }

    /**
     * 过滤非法字符description
     * @author staitc7 <static7@qq.com>
     */

    protected function setTitleAttr($value) {
        return htmlspecialchars($value);
    }

    /**
     * 组类型 type
     * @author staitc7 <static7@qq.com>
     */

    protected function setTypeAttr() {
        return Config::get('auth_config.type_admin');
    }

}
