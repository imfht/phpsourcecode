<?php

class user_roleModel extends RelationModel
{
	
    protected $_link = array(
   
        'role_priv' => array(
            'mapping_type'  => MANY_TO_MANY,
            'class_name'    => 'menu',
            'foreign_key'   => 'role_id',
            'relation_foreign_key'=>'menu_id',
            'relation_table'=> 'admin_auth',
            'autoprefix' => true
        )
    );
	
	protected $_validate = array(
        array('name', 'require', '{%role_name_empty}'), //不能为空
        array('name', '3,16', '{%username_length_error}', 0, 'length', 1), //用户名长度
        array('name', '', '{%role_name_exists}', 1, 'unique', 1), //检测重复
        array('score', 'number', '{%isnotnum}', 1, 'unique', 1), //检测重复
    );
    
    protected $_auto = array(
       
        
    );

    /**
     * 修改名称
     */
    public function rename($map, $newname) {
        if ($this->where(array('name'=>$newname))->count('id')) {
            return false;
        }
        $this->where($map)->save(array('name'=>$newname));
        $id = $this->where(array('name'=>$newname))->getField('id');
        return true;
    }
public function name_exists($name, $id = 0) {
        $where = "name='" . $name . "' AND id<>'" . $id . "'";
        $result = $this->where($where)->count('id');
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
}