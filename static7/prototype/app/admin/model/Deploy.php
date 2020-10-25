<?php

namespace app\admin\model;

use think\Model;
use think\Request;
use think\Validate;
use think\Config;

/**
 * Description of Config
 * 系统配置
 * @author static7
 */
class Deploy extends Model {

    protected $rule = [
        'title' => "require|max:30",
        'name' => "require|unique:deploy,name",
    ];
    protected $msg = [
        'title.require' => '标题不能为空',
        'title.max' => '标题最多不能超过30个字符',
        'name.require' => '配置名称不能为空',
        'name.unique' => '配置名称已经存在'
    ];
    protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段
    protected $auto = ['title', 'name'];
    protected $insert = [
        'status' => 1,
    ];

    /**
     * 配置列表
     * @param array $map_tmp 临时条件,后期会合并
     * @param string $field 查询的字段
     * @param string $order 排序 默认id asc
     * @author staitc7 <static7@qq.com>
     */

    public function configList(array $map_tmp = [], $field = true, string $order = 'id ASC'): array {
        $map = array_merge(['status' => ['neq', '-1']], $map_tmp);
        $object = $this::where($map)->order($order)->field($field)->paginate(Config::get('list_rows') ?? 10);
        return $object ? array_merge($object->toArray(), ['page' => $object->render()]) : [];
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
     * 用户更新或者添加菜单
     * @author staitc7 <static7@qq.com>
     */

    public function renew() {
        $data = Request::instance()->post();
        $validate = Validate::make($this->rule, $this->msg);
        if (!$validate->check($data)) {
            // 验证失败 输出错误信息
            return $validate->getError(); 
        }
        $object = (int) $data['id'] ? $this::update($data) : $this::create($data);
        return $object ? $object->toArray() : null;
    }

    /**
     * 批量保存配置
     * @param array $data 配置数据
     * @author staitc7 <static7@qq.com>
     */

    public function batchSave(array $data = []) {
        foreach ($data as $name => $value) {
            $this::where(['name' => $name])->setField('value', $value);
        }
        return $this->getError() ?
                ['status' => false, 'info' => $this->getError()] :
                ['status' => true, 'info' => '操作成功'];
    }

    /**
     * 配置标识转为小写
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */

    protected function setNameAttr($value) {
        return strtoupper($value);
    }

    /**
     * 配置名称过滤
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */

    protected function setTitleAttr($value) {
        return htmlspecialchars($value);
    }

}
