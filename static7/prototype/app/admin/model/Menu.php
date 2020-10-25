<?php

namespace app\admin\model;

use think\Model;
use think\Request;
use think\Validate;
use think\Config;
/**
 * Description of Menu
 * 菜单管理
 * @author static7
 */
class Menu extends Model {

    protected $rule = [
        'title' => "require|max:30",
        'url' => "require",
    ];
    protected $msg = [
        'title.require' => '标题不能为空',
        'title.max' => '标题最多不能超过30个字符',
        'url.require' => '链接不能为空',
    ];
    protected $auto = ['title', 'url'];
    protected $insert = [
        'status' => 1,
    ];
    protected $update = [];

    /**
     * 菜单列表
     * @param int $pid 父级ID
     * @author staitc7 <static7@qq.com>
     */

    public function menuList(int $pid = 0): array {
        $map = $pid === 0 ? ['pid' => 0] : ['pid' => $pid];
        $map['status'] = ['neq', -1];
        $object = $this::where($map)->order('sort asc,id asc')->paginate(Config::get('list_rows') ?? 10);
        return $object ? array_merge($object->toArray(), ['page' => $object->render()]) : [];
    }

    /**
     * 查询父级菜单
     * @param int $pid 父级ID
     * @author staitc7 <static7@qq.com>
     */

    public function father(int $pid = 0): array {
        $object = $this::get(function($query)use($pid) {
                    $query->where('id', $pid)->field('pid,title');
                });
        return $object ? $object->toArray() : [];
    }

    /**
     * 查询菜单详情
     * @param int $id 菜单详情
     * @author staitc7 <static7@qq.com>
     */

    public function edit(int $id = 0): array {
        $object = $this::get(function($query)use($id) {
                    $query->where('id', $id);
                });
        return $object ? $object->toArray() : [];
    }

    /**
     * 根据字段查询菜单
     * @param string $field 查询的字段
     * @author staitc7 <static7@qq.com>
     */

    public function menuField($field) {
        $object = $this::all(function($query)use($field) {
                    $query->where(['status'=>['neq',-1]])->field($field)->order('sort asc');
                });
        return $object ? object_to_array($object) : null;
    }

    /**
     * 菜单列表(所有)
     * @author staitc7 <static7@qq.com>
     */

    public function menuListAll(): array {
        $object = $this::all(function($query) {
                    $query->field('id,title,pid');
                });
        return $object ? object_to_array($object) : [];
    }

    /**
     * 用户更新或者添加菜单
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
     * 修改状态
     * @param int|array $map 数据的ID或者ID组
     * @param array $data 要修改的数据
     * @author staitc7 <static7@qq.com>
     */

    public function setStatus($map = null, $data = null) {
        if (empty($map) || empty($data)) {
            return false;
        }
        return $this::where($map)->update($data);
    }

    /**
     * 菜单导入
     * @param array $param 可变参数
     * @author staitc7 <static7@qq.com>
     */

    public function menuImport(...$param) {
        $data = true;
        foreach ($param[0] as $key => $value) {
            $record = explode('|', $value);
            if (count($record) !== 2) {
                return false;
            }
            $object[$key] = $this::create([
                        'title' => $record[0],
                        'url' => $record[1],
                        'pid' => $param[1],
                        'sort' => 0,
                        'hide' => 0,
                        'tip' => '',
                        'is_dev' => 0,
                        'group' => '',
            ]);
            $info[$key] = $object[$key] ? $object[$key]->toArray() : null;
            if ((int) $info[$key]['id'] < 1) {
                $data = false;
                break;
            }
        }
        return $data;
    }

    /**
     * 获取器isDev
     * @param int $value 要修改的值
     * @author staitc7 <static7@qq.com>
     */

//    public function getIsDevAttr($value) {
//        $isDev = [1 => '是', 0 => '否'];
//        return $isDev[$value];
//    }

    /**
     * 获取器hide
     * @param 类型 参数 参数说明 
     * @author staitc7 <static7@qq.com>
     */

//    public function getHideAttr($value) {
//        $hide = [1 => '是', 0 => '否'];
//        return $hide[$value];
//    }

    /**
     * 获取器status
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */

//    public function getStatusAttr($value) {
//        $status = [1 => '禁用', 0 => '启用'];
//        return $status[$value];
//    }
}
