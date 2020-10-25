<?php
namespace app\common\model;
use think\Db;
use think\Model;

abstract class F extends Model
{
    // 设置当前模型对应的完整数据表名称
    public $table; // '__FORM_FIELD__';
    
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
        $this->table = self::$table_pre.self::$model_key.'_field';
    }
    
    /**
     * 根据mid获取内容表,如果mid为-1的话,是order订单表 -2是栏目表,-3是辅栏目表
     * @param number $mid
     * @return string
     */
    public function getTableByMid($mid=0){
        if ($mid>0) {
            $table = self::$base_table . $mid;
        }elseif($mid==-1){
            $table = self::$model_key . '_order' ;
        }elseif($mid==-2){
            $table = self::$model_key . '_sort' ;
        }elseif($mid==-3){
            $table = self::$model_key . '_category' ;
        }
        return $table;
    }
    
    public static function getFields($map=[])
    {
        return self::where($map)->order('list desc,id asc')->column(true);
    }
    
    /**
     * 创建字段
     */
    public function newField($mid,$data = [])
    {
        if ( empty($data) ) {
            return '参数不完整';
        }        
        $table = $this->getTableByMid($mid);        
        if (is_table($table)) {
            $sql = "
            ALTER TABLE `" . self::$table_pre  . $table . "`
            ADD COLUMN `{$data['name']}` {$data['field_type']} COMMENT '{$data['title']}';
            ";
        }else {
            return '数据表不存在.'.$table;
        }
        if (table_field($table,$data['name'])) {
            return '该字段已经存在,请更换一个';
        }
        
        try {
            Db::execute($sql);
        } catch(\Exception $e) {
            return '字段添加失败'.$sql;
        }
        return true;
    }
    
    /**
     * 更新字段
     * @param null $field 字段数据
     * @return bool
     */
    public function updateField($id,$array = [])
    {
        
        if (empty($array)) {
            return '参数不完整';
        }
        
        // 获取原字段名
        $field_array = self::get($id);  //;where('id', $id)->value('name');
        $table = $this->getTableByMid($field_array['mid']);
        
        if($array['field_type']==$field_array['field_type'] && $array['name']==$field_array['name'] ){
            return true;
        }
        if (is_table($table)) {
            
            if ($array['name']!=$field_array['name'] && table_field($table,$array['name'])) {
                return '该字段已经存在,请更换一个';
            }
            
            $sql = "
            ALTER TABLE `" . self::$table_pre . $table."`
            CHANGE COLUMN `{$field_array['name']}` `{$array['name']}` {$array['field_type']} COMMENT '{$array['title']}';
            ";
            try {
                Db::execute($sql);
            } catch(\Exception $e) {
                return '更新失败.'.$sql;
            }
            return true;
        } else {
            return '数据表不存在.'.$table;
        }
    }
    
    /**
     * 删除字段
     * @param null $field 字段数据
     * @return bool
     */
    public function deleteField($field = null)
    {
        
        if ($field === null) {
            return false;
        }
        if(empty($field['mid']) || empty($field['name'])){
            return false;
        }
        
        $table = $this->getTableByMid($field['mid']);
        
        if (is_table($table)) {
            $sql ="
            ALTER TABLE `" . self::$table_pre . $table ."`
            DROP COLUMN `{$field['name']}`;
            ";
            try {
                Db::execute($sql);
            } catch(\Exception $e) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
}