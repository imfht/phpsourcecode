<?php
namespace app\common\model;
use think\Model;
use util\Tree;

//辅助栏目
abstract class Category extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table;// = '__FORM_MODULE__';
    
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = true;   // 自动写入时间戳
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
        $this->table = self::$table_pre.self::$model_key.'_category';
    }
    
    //获取所有栏目的名称及ID
    public static  function getTitleList($where=[])
    {
        self::InitKey();
        static $list = NULL;
        if($list==NULL){
            $list = self::where($where)->order('list','desc')->column('id,name');
        }
        return $list;
    }
    
    //通过ID得到相应的标题名称
    public static function getNameById($id)
    {
        self::InitKey();
        if (empty($id)) {
            return ;
        }
        $list = static::getTitleList();
        if($list){
            return $list[$id];
        }
    }
    
    //通过ID得到相应的栏目的相关资料
    public static function getInfoById($id)
    {
        self::InitKey();
        $data = self::get($id);
        if(!empty($data)){
            return $data->toArray();
        }
    }
    
    //获取一个值值给某些地方没有指定MID的地方默认使用
    public static function getId()
    {
        self::InitKey();
        $list = static::getTitleList();
        if($list){
            return current(array_flip($list));
        }
    }
    
    //第一项，指定ID及其子ID不要显示，比如创建栏目的时候容易造成死循环，第二项发布文章的时候，不能选择其它模型的栏目
    public static function getTreeTitle($id = 0, $mid = 0,$default_title = '请选择...')
    {
        self::InitKey();
        $where = [];
        $result = [];
        if ($default_title != '') {
            $result[0] = $default_title;
        }
        
        if ($id !== 0) {
            $hide_ids    = array_merge([$id], static::getSonsId($id));
            $where['id'] = ['notin', $hide_ids];
        }
        
        if ($mid !== 0) {
            $where['mid'] = $mid;
        }
        
        $data_list = Tree::config(['title' => 'name'])->toList(self::where($where)->order('list desc,id desc')->column('id,pid,name'));
        foreach ($data_list as $item) {
            $result[$item['id']] = $item['title_display'];
        }
        
        if ($default_title === false) {
            unset($result[0]);
        }
        
        return $result;
    }
    
    public static function getTreeList($id = 0, $mid = 0)
    {
        self::InitKey();
        $where = [];
        $result = [];
        
        if ($id !== 0) {
            $hide_ids    = array_merge([$id], static::getSonsId($id));
            $where['id'] = ['notin', $hide_ids];
        }
        if ($mid !== 0) {
            $where['mid'] = $mid;
        }
        
        $data_list = Tree::config(['title' => 'name'])->toList(self::where($where)->order('list desc,id desc')->column('id,pid,name'));
     
        return $data_list;
    }
    
    public static function getSonsId($id = 0)
    {
        self::InitKey();
        $array = $id_array = self::where('pid', $id)->column('id');
        foreach ($id_array AS $id_value) {
            $array = array_merge($array, static::getSonsId($id_value));
        }
        return $array;
    }
}