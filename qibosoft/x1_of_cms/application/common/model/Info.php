<?php
namespace app\common\model;
use think\Model;
use util\Tree;
use think\Db;

//辅栏目内容表
abstract class Info extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table;// = '__FORM_MODULE__';
    
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = false;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
    
    protected static $base_table;
    protected static $model_key;
    protected static $table_pre;
    
    //为了调用initialize初始化,生成数据表前缀$model_key
    protected static function scopeInitKey(){}
    protected function initialize()
    {
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$model_key = $array[0][1];
        self::$base_table = $array[0][1].'_content';
        self::$table_pre = config('database.prefix');
        //字段表，带数据表前缀如qb_form_field
        $this->table = self::$table_pre.self::$model_key.'_info';
    }
    
    public static function save_data($ids=[],$fid){
        $data = [];
        foreach($ids AS $id){
            if(Db::name(self::$model_key.'_info')->where('aid','=',$id)->where('cid','=',$fid)->select()){
                continue;
            }
            $data[] = ['aid'=>$id,'cid'=>$fid];
        }
        if ($data && Db::name(self::$model_key.'_info')->insertAll($data) ) {
            return count($data);
        }
        return false;
    }
    
    /**
     * 通过内容ID获取下一条内容数据
     * @param unknown $aid 内容ID
     * @param unknown $fid 分类ID
     * @return unknown
     */
    public static function getNextAidByAid($aid,$fid){
        self::InitKey();
        $ck = 0;
        $listdb = Db::name(self::$model_key.'_info')->where('cid','=',$fid)->order('list DESC,id DESC')->column('id,aid');
        foreach($listdb AS $key=>$value){
            if($value==$aid){
                $ck++;
            }elseif($ck){
                return $value;
            }
        }
    }
    
    /**
     * 根据栏目FID获取本栏目下的所有内容
     * @param number $fid
     * @return array
     */
    public static function getAllByfid($fid=0){
        self::InitKey();
        $listdb = Db::name(self::$model_key.'_info')->where('cid','=',$fid)->order('list DESC,id DESC')->column('id,aid');
        return $listdb;
    }

}