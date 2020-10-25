<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/7/15 0015
 * Time: 9:07
 */
//model的存储目录在data，为了公共方法的使用方便
class Model {
 protected   $table='';
 protected  $field='';
 protected   $privateKey='';//主键
//返回基本方法实例,查找一个实例的方法,根据id查找是否存在,如果是数组就是数组条件
public  function  find($id){

  if(is_array($id)){
   return Model3($this->table)->where($id)->find();
  }
  else{
   return   Model3($this->table)->where(array($this->privateKey=>$id))->find();
  }
}
//查询列表
public  function  select($condition,$field='',$order=''){
    return Model3($this->table)->field($field)->where($condition)->order($order)->select();
}
//更改
public  function  update($condition,$update){
    return Model3($this->table)->where($condition)->update($update);
}
 //根据条件查找是否存在
public  function  count($condition){
    return   Model3($this->table)->where($condition)->count();
}
//插入
public  function  insert($insert=array()){
    return   Model3($this->table)->insert($insert);
}
}

//获取一个model的实例
function Model($model){
   $file_name = ROOT_PATH . '/data/model/' .$model.'.php';
    static $_cache = array();
    if (isset($_cache[$model])) {
        return $_cache[$model];
    }
    if (!$model) //如果为空或null直接返回model对象
    {
        return new  Model();
    }
    $class_name = $model . 'Model';

    if (class_exists(@$class_name, false)) { //由于测试是indexControl已经加载过但不是这个方法加载过的，所以没在静态缓存中
        return $_cache[$model] = new $class_name();
    }
    include($file_name); //动态引入文件
    if (!class_exists($class_name)) {
        $error = 'Model Error:  Class ' . $class_name . ' is not exists!';
        throw new Exception($error);
    } else {
        return $_cache[$model] = new $class_name();
    }
}
//model初始化
function create_model($model='all',$prefix=''){
    if(function_exists('c')){
        $table_prefix=c('db');
        $table_prefix= $table_prefix['master']['dbprefix'];
    }
    else{
        if(!$prefix){
         Throw  new Exception('请添写表前缀参数,或在trigger init之后调用和实现');
        }
        $table_prefix=$prefix;
    }

    $target_path=ROOT_PATH.'/data/model/';
    $tpl=CORE_PATH.'/tpl/model.tpl';
    $tpl=file_get_contents($tpl);
    //创建全部模型
    if($model=='all'){
        $table_list= Model3::get_table_list();
    }
    else{
        $table_list=array($table_prefix.$model);
    }
  //从数据库查询表，字段，并根据模板生成相应的model模型，存放在data/model目录中
  foreach($table_list as $key=>$value){
     $fields=Model3::get_field($value,1);
     $key=$fields[0];//第1个键值作为主键
     $fields=implode(',',$fields);
      if($table_prefix){
      $table=explode($table_prefix,$value);
       $table=$table[1];
      }
      else{
     $table=$value;
      }
      $path=$target_path.$table.'.php';
    if(!file_exists($path)){
      mk_dir($target_path);
    $content=str_replace(array('{{model}}','{{field}}','{{key}}'),array($table,$fields,$key),$tpl);
    file_put_contents($path,$content); //将内容写入文件
    }
      else{
          echo $path.' already exist!';
      }
  }
    echo 'init model '.$model.'success!!';

}