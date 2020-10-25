<?php

namespace Admin\Model;

use Think\Model;

/** 
 * @author Lain
 * 
 */
class ModelFieldModel extends Model {
    //不允许删除的字段，这些字段讲不会在字段添加处显示
    public $not_allow_fields = array('catid','typeid','title','keyword','posid','template','username');
    //允许添加但必须唯一的字段
    public $unique_fields = array('pages','readpoint','author','copyfrom','islink');
    //禁止被禁用的字段列表
    public $forbid_fields = array('catid','title','updatetime','inputtime','url','listorder','status','template','username');
    //禁止被删除的字段列表
    public $forbid_delete = array('catid','typeid','title','thumb','keywords','updatetime','inputtime','posids','url','listorder','status','template','username');
    
    //获取所有模型
    public function getFieldsByModelid($modelid){
        $map['modelid'] = $modelid;
        $map['disabled'] = 0;
        $list = $this->where($map)->index('field')->order('listorder,fieldid')->select();
        return $list;
    }

    public function drop_field($tablename, $field){
        $this->table_name = C('DB_PREFIX').$tablename;
        $fields =   $this->db->getFields($this->table_name);
        if(in_array($field, array_keys($fields))) {
            return $this->execute("ALTER TABLE `$this->table_name` DROP `$field`;");
        } else {
            return false;
        }
    }
}

?>