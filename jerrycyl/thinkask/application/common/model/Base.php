<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\common\model;
use think\Model;
use think\Db;
class Base extends Model
{
  public function getdb($table){
    return Db::table(config('database.prefix').$table);
  }
	/**
	 * [getall 查出所有]
	 * @return [type] [description]
	 */
     public function getall($table,$param=[])
    {
    	if(empty($table)) die('必须指定表名');
    	$param['where']=$param['where']?$param['where']:[];
    	$param['field']=$param['field']?$param['field']:"";
      $param['alias']=$param['alias']?$param['alias']:"";
    	$param['limit']=$param['limit']?$param['limit']:"";
    	$param['order']=$param['order']?$param['order']:[];
      $param['join'] = $param['join']?$param['join']:[];
      $param['page'] = $param['page']?$param['page']:"";
      $param['unionALL'] = $param['unionALL']?$param['unionALL']:[];
    	$param['union'] = $param['union']?$param['union']:[];
    	 $param['cache'] =$param['cache']?true:false;
      return $re =Db::name($table)->alias($param['alias'])->join($param['join'])->where($param['where'])->field(trim($param['field'],","))->cache($param['cache'])->order($param['order'])->limit($param['limit'])->select();
    }
     /**
      * [getall count 所有]
      * @param  [type] $table [description]
      * @param  array  $param [description]
      * @return [type]        [description]
      */
      public function getcount($table,$param=[])
    {
    	if(empty($table)) die('必须指定表名');
      $param['where']=$param['where']?$param['where']:[];
      $param['field']=$param['field']?$param['field']:"";
      $param['alias']=$param['alias']?$param['alias']:"";
      $param['order']=$param['order']?$param['order']:[];
      $param['join'] = $param['join']?$param['join']:[];
    	 $param['cache'] =$param['cache']?true:false;
    return $re = Db::name($table)->alias($param['alias'])->join($param['join'])->where($param['where'])->field(trim($param['field'],","))->cache($param['cache'])->order($param['order'])->count();
    }
    /**
     * [getquery 原生查询]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function getquery($sql){
      return Db::query($sql);
    }
    /**
     * [getadd 添加数据]
     * @param  [type] $table [description]
     * @param  [type] $data  [description]
     * @return [type]        [description]
     */
    public function getadd($table,$data){
      if(empty($table)){
        die('表不能为空');
      }
      return  Db::name($table)->insertGetId($data);
    }
    /**
     * [getedit description 修改]
     * @return [type] [description]
     */
    public function getedit($table,$param,$data){
      if(empty($table)) die('表不能为空');
      if(empty($param['where'])) die('修改文件必须传入WHERE条件');
      return  Db::name($table)->where($param['where'])->update($data);
    }
    /**
     * [getdel description]
     * @return [type] [description]
     */
    public function getdel($table,$param){
       if(empty($table)) die('表不能为空');
       if(empty($param['where'])) die('删除必须传入WHERE条件');
       return  Db::name($table)->where($param['where'])->delete();
    }
    /**
     * [getone 获得一条数据]
     * @return [type] [description]
     */
    public function getone($table,$param=[]){
       if(empty($table)) die('必须指定表名');
          $param['where']=$param['where']?$param['where']:[];
          $param['field']=$param['field']?$param['field']:"";
          $param['alias']=$param['alias']?$param['alias']:"";
          $param['order']=$param['order']?$param['order']:[];
          $param['join'] = $param['join']?$param['join']:[];
           $param['cache'] =$param['cache']?true:false;
        return $re = Db::name($table)->join($param['join'])->where($param['where'])->field(trim($param['field'],","))->cache($param['cache'])->order($param['order'])->find();
        }
      /**
       * [getpages description]
       * @return [type] [description]
       * <div>
        *<ul>
       * {volist name='list' id='user'}
         *   <li> {$user.nickname}</li>
       * {/volist}
        *</ul>
       * </div>
        *{$list->render()}
       */
  public function getpages($table,$param=[]){
    if(empty($table)) die('必须指定表名');
      $param['where']=$param['where']?$param['where']:[];
      $param['field']=$param['field']?$param['field']:"";
      $param['alias']=$param['alias']?$param['alias']:"";
      // $param['limit']=$param['limit']?$param['limit']:"";
      $param['order']=$param['order']?$param['order']:[];
      $param['join'] = $param['join']?$param['join']:[];
      $param['unionALL'] = $param['unionALL']?$param['unionALL']:[];
      $param['union'] = $param['union']?$param['union']:[];
      $param['cache'] =$param['cache']?true:false;
      $param['list_rows'] = $param['list_rows']?$param['list_rows']:30;
      return $re = Db::name($table)->alias($param['alias'])->join($param['join'])->where($param['where'])->field(trim($param['field'],","))->cache($param['cache'])->order($param['order'])->paginate($param['list_rows'],false,['query' =>request()->param(),]);

  }
  
  // 自增  字段
  /**
   * [getinc description]
   * @param  [type] $table [表名]
   * @param  [type] $param [条件]
   * @param  [type] $field [自增的字段，如SCORE 那么 SCORE++]
   * @return [type]        [description]
   */
  public function getinc($table,$param,$field){
    return db($table)->where($param['where'])->setInc($field);
  }
  //// 自减  字段
  public function getdec($table,$param,$field){
    return db($table)->where($param['where'])->setDec($field);
  }
  /**
   * 数据修改
   * @return [bool] [是否成功]
   */
  public function change(){
    $data = \think\Request::instance()->post();
    if (isset($data['id']) && $data['id']) {
      return $this->save($data, array('id'=>$data['id']));
    }else{
      return $this->save($data);
    }
  }
    
}