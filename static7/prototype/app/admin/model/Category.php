<?php

namespace app\admin\model;

use think\Model;
use think\Request;
use think\Validate;
use think\Config;
use think\Cache;

/**
 * Description of Category
 * 分类管理表
 * @author static7
 */
class Category extends Model {

    protected $rule = [
        'title' => "require|max:30",
        'name' => "require|alpha|unique:category",
        'meta_title' => 'max:50',
        'keywords' => 'max:200',
        'description' => 'max:200',
    ];
    protected $message = [
        'title.require' => '分类名称不能为空',
        'title.max' => '分类名称最多不能超过30个字符',
        'name.require' => '分类标识不能为空',
        'name.unique' => '分类标识已经存在',
        'name.alpha' => '行为标识只能为字母',
        'meta_title.max' => '网页标题不能超过50个字符',
        'keywords.max' => '网页关键字不能超过200个字符',
        'description.max' => '网页描述不能超过200个字符'
    ];
    protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段
    protected $auto = ['meta_title'];
    protected $insert = [
        'status' => 1,
    ];
    protected $update = ['title'];

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function getTree($id = 0, $field = true) {
        /* 获取当前分类信息 */
        if ($id) {
            $info = $this->info($id);
            $id = $info['id'];
        }
        $object = $this::all(function($query) use($field) {
                    $query->where(['status' => ['neq', -1]])->field($field)->order(['sort' => 'ASC']);
                });
        if ($object) {
            object_to_array($object);
            $list = list_to_tree($object, 'id', 'pid', '_', $id);
            if (isset($info)) { //指定分类则返回当前分类极其子分类
                $info['_'] = $list;
            } else { //否则返回所有分类
                $info = $list;
            }
        }
        return $info;
    }

    /**
     * 获取分类详细信息
     * @param  milit   $id 分类ID或标识
     * @param  boolean $field 查询字段
     * @return array     分类信息
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function info(int $id, $field = true) {
        is_numeric($id) ? $map['id'] = $id : $map['name'] = $id;
        $object = $this::get(function($query)use($map, $field) {
                    $query->field($field)->where($map);
                });
        return $object ? object_to_array($object) : null;
    }

    /**
     * 编辑分类信息
     * @param int $id 分类id
     * @author staitc7 <static7@qq.com>
     */
    public function edit(int $id = 0) {
        $info = $this->info($id);
        $category = $info ? $this->info($info->data['pid'], 'id,name,title,level') : null;
        return $data = [
            'info' => $info->data,
            'category' => $category->data ?? null
        ];
    }

    /**
     * 更新分类信息
     * @return boolean 更新状态
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function renew() {
        $data = Request::instance()->post();
        $validate = Validate::make($this->rule, $this->message);
        if (!$validate->check($data)) {
            return $validate->getError(); // 验证失败 输出错误信息
        }
        $object = (int) $data['id'] ? $this::update($data) : $this::create($data);
        Cache::clear('sys_category_list', null); //更新分类缓存
//        action_log('update_category', 'category', $data['id'] ? $data['id'] : $res, UID);//记录行为
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

    /* =================自动完成===================== */

    protected function setTypeAttr($value) {
        return implode(',', $value);
    }
    
    protected function setModelAttr($value) {
        return implode(',', $value);
    }
    
    protected function setNameAttr($value) {
        return strtolower($value);
    }

    protected function setTitleAttr($value) {
        return htmlspecialchars($value);
    }

    protected function setDescriptionAttr($value) {
        return htmlspecialchars($value);
    }

    protected function setMetaTitleAttr($value) {
        return htmlspecialchars($value);
    }

    /* =================修改器==================== */
}
