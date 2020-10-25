<?php

namespace app\article\validate;
use think\Validate;
/**
 *  模型属性验证
 */
class CategoryArticle extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|max:25', '栏目名称不能为空|栏目名称不能超过25个字符'],
        ['parent_id', 'require|parentCheck', '字段名不能为空|上级栏目关系选择错误'],
    ];

    protected $scene = array(
        'add'     => 'name',//新增数据时验证
        'edit'     => 'name,parent_id',//修改数据时验证
    );
    /**
     * 验证字段是否重复
     * @param int $field 字段名
     * @return bool 状态
     */
    protected function parentCheck($field){
        //获取变量
        $classId = input('post.class_id');
        $parentId = input('post.parent_id');
        //判断空上级
        if(!$parentId){
            return true;
        }
        // 分类检测
        if ($classId == $parentId){
            $this->error = '不可以将当前栏目设置为上一级栏目';
            return false;
        }

        $cat = model('kbcms/Category')->loadList(array(),$classId);
        if(empty($cat)){
            return true;
        }
        foreach ($cat as $vo) {
            if ($parentId == $vo['class_id']) {
                $this->error = '不可以将上一级栏目移动到子栏目';
                return false;
            }
        }
        return true;
    }
}