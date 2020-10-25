<?php

namespace Admin\Model;

use Think\Model;

/** 
 * @author Lain
 * 
 */
class ModelModel extends Model {

    const PRE_FORM = 'form_';
    const TYPE_CONTENT = 0;
    const TYPE_FORM = 3;

    /**
     * 添加内容模型 type默认为0
     * @param [type] $info [description]
     */
    public function addModel($info){
        $modelid = $this->add($info);
        //添加默认表结构
        $model_sql = file_get_contents(APP_PATH.'Admin/Conf/model.sql');
        $tablepre  = C('DB_PREFIX');
        $tablename = $info['tablename'];
        $model_sql = str_replace('$basic_table', $tablepre.$tablename, $model_sql);
        $model_sql = str_replace('$table_data',$tablepre.$tablename.'_data', $model_sql);
        $model_sql = str_replace('$table_model_field',$tablepre.'model_field', $model_sql);
        $model_sql = str_replace('$modelid',$modelid,$model_sql);
        $model_sql = str_replace('$siteid', 0,$model_sql);
        $model_sql = str_replace("\r", "\n", $model_sql);
        $model_sql = explode(";\n", $model_sql);
        foreach ($model_sql as $key => $value) {
            $value = trim($value);
            if (empty($value)) continue;
            $this->execute($value);
        }
        //添加缓存
        return true;
    }


    //表单模型
    public function addFormModel($info){
        $info['type'] = self::TYPE_FORM;
        $modelid = $this->add($info);
        //添加默认表结构
        $model_sql = file_get_contents(APP_PATH.'Admin/Conf/form_model.sql');
        $tablepre  = C('DB_PREFIX');
        //表名添加标识
        $tablename = self::PRE_FORM . $info['tablename'];
        $model_sql = str_replace('$basic_table', $tablepre.$tablename, $model_sql);
        $model_sql = explode(";\n", $model_sql);
        foreach ($model_sql as $key => $value) {
            $value = trim($value);
            if (empty($value)) continue;
            $this->execute($value);
        }
        //添加缓存
        return true;
    }

    /**
     * 删除内容模型
     * @param  [type] $modelid 模型ID
     * @return [type]          [description]
     */
    public function deleteModel($modelid){
        $map['modelid'] = $modelid;
        $model = $this->where($map)->find();
        //删除model_field模型字段表信息
        M('ModelField')->where($map)->delete();
        //删除主表
        $this->drop_table($model['tablename']);
        //删除附表
        $this->drop_table($model['tablename'].'_data');
        //删除model模型信息
        $this->where($map)->delete();
        return true;
    }

    public function deleteFormModel($modelid){
        $map['modelid'] = $modelid;
        $model = $this->where($map)->find();
        //删除主表
        $this->drop_table(self::PRE_FORM . $model['tablename']);
        //删除model模型信息
        $this->where($map)->delete();
        return true;
    }

    //获取所有模型
    public function getAllModels(){
        $list = $this->index('modelid')->select();
        return $list;
    }

    //取出该模型表名
    public function getModelTablename($modelid){
        $model_table = $this->where(array('modelid' => $modelid))->getField('tablename');
        return $model_table;
    }

    //查看表是否存在
    public function tableExists($tablename){
        $tables = $this->db()->getTables();
        return in_array(C('DB_PREFIX').$tablename, $tables);
    }

    public function transModel(){
        $tables = $this->db()->getTables();
        foreach ($tables as $key => $tablename) {
            if(strpos($tablename, 'v9_') !== false){
                $table_new_name = str_replace('v9_', '', $tablename);
                //先删除旧表， 再改名。
                $this->drop_table($table_new_name);
                $table_new_name = C('DB_PREFIX').$table_new_name;
                $sql = "alter table $tablename rename $table_new_name;";
                $this->execute($sql);
            }
        }
        return true;
    }

    public function drop_table($tablename) {
        $tablename = C('DB_PREFIX').$tablename;
        return $this->execute("DROP TABLE IF EXISTS $tablename");
        // $tablearr = $this->db->list_tables();
        // if(in_array($tablename, $tablearr)) {
        //     return $this->db->query("DROP TABLE $tablename");
        // } else {
        //     return false;
        // }
    }
}

?>