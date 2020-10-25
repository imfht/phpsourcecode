<?php

namespace app\admin\model;

use think\Model;
use think\Config;
use think\Request;
use think\Validate;

/**
 * Description of Action
 * 用户行为模型
 * @author static7
 */
class Action extends Model {

    protected $rule = [
        'title' => "require|max:30",
        'name' => "require|alphaDash|unique:deploy,name",
        'remark' => 'max:140',
    ];
    protected $msg = [
        'title.require' => '行为标题不能为空',
        'title.max' => '行为标题最多不能超过30个字符',
        'name.require' => '行为名称不能为空',
        'name.unique' => '行为名称已经存在',
        'name.alphaDash' => '行为标识为字母和数字，下划线_及破折号-',
        'remark.require' => '行为描述不能为空',
        'remark.max' => '行为描述不能超过140个字符'
    ];
    protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段
    protected $createTime = false;
    protected $auto = ['title'];
    protected $insert = [
        'status' => 1,
    ];

    /**
     * 行为列表
     * @param array $map_tmp 临时条件,后期会合并
     * @param string $field 查询的字段
     * @param string $order 排序 默认id asc
     * @author staitc7 <static7@qq.com>
     */

    public function actionList(array $map_tmp = [], $field = true, string $order = 'id ASC'): array {
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
     * 查询行为详情
     * @param int $id 行为详情
     * @author staitc7 <static7@qq.com>
     */

    public function edit(int $id = 0): array {
        $object = $this::get(function($query)use($id) {
                    $query->where('id', $id);
                });
        return $object ? $object->toArray() : [];
    }

    /**
     * 用户更新或者添加行为
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
     * 配置名称过滤
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */

    protected function setTitleAttr($value) {
        return htmlspecialchars($value);
    }

}
