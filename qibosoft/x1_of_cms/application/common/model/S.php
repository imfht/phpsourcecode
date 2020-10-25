<?php
namespace app\common\model;
use think\Model;
use util\Tree;

abstract class S extends Model
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
        $this->table = self::$table_pre.self::$model_key.'_sort';
    }
    
    /**
     * 获取所有栏目的名称及ID
     * @param array $where
     * @return unknown
     */
    public static function getTitleList($where=[])
    {
        return self::where($where)->order('list','desc')->column('id,name');
    }
    
    public static function getList($where=[])
    {
        return self::where($where)->order('list','desc')->column(true);
    }
    
    /**
     * 通过ID得到相应的栏目的相关资料
     * @param unknown $id
     * @return void|array|NULL[]|unknown
     */
    public static function getInfoById($id)
    {
        if (empty($id)) {
            return ;
        }
        return getArray(self::get($id));
    }
    
    /**
     * 通过ID得到相应的栏目名称
     * @param unknown $id
     * @return void|\app\common\model\unknown
     */
    public static function getNameById($id)
    {
        if (empty($id)) {
            return ;
        }
        $list = static::getTitleList();
        if($list){
            return $list[$id];
        }
    }
    
    /**
     * 获取一个值值给某些地方没有指定MID的地方默认使用
     * @return mixed
     */
    public static function getId()
    {
        $list = static::getTitleList();
        if($list){
            return current(array_flip($list));
        }
    }
    
    /**
     * 获取树状的栏目,只含栏目标题及ID
     * 第一项，指定ID及其子ID不要显示，比如创建栏目的时候容易造成死循环，第二项发布文章的时候，不能选择其它模型的栏目
     * @param number $id
     * @param number $mid
     * @param string $default_title
     * @return string[]|unknown[]
     */
    public static function getTreeTitle($id = 0, $mid = 0,$default_title = '请选择...')
    {
        $where = [];
        $result = [];
        if ($default_title !==false) {
            $result[0] = $default_title;
        }
        
        if ($id !== 0) {
            $hide_ids    = array_merge([$id], static::getSonsId($id));
            $where['id'] = ['notin', $hide_ids];
        }
        
        if ($mid !== 0) {
            //$where['mid'] = $mid;
        }
        $ck = false;
        $data_list = Tree::config(['title' => 'name'])->toList(self::where($where)->order('list desc,id asc')->column('id,pid,mid,name'));
        foreach ($data_list as $item) {
            if($mid!=0 && $item['mid']!=$mid){
                continue;
            }
            $result[$item['id']] = $item['title_display'];
            $ck = true;
        }
        return $ck?$result:[];
    }
    
    /**
     * 获取树状的栏目,包含栏目所有的信息
     * @param number $id
     * @param number $mid
     * @return unknown[]
     */
    public static function getTreeList($id = 0, $mid = 0)
    {
        $where = [];
        $result = [];
        
        if ($id !== 0) {
            $hide_ids    = array_merge([$id], static::getSonsId($id));
            $where['id'] = ['notin', $hide_ids];
        }
        if ($mid !== 0) {
            $where['mid'] = $mid;
        }
        $data_list = [];
        $array = Tree::config(['title' => 'name'])->toList(self::where($where)->order('list desc,id asc')->column(true,'id'));
        foreach($array AS $rs){
            $data_list[$rs[id]] = $rs;
        }
        return $data_list;
    }
    
    /**
     * 获取所有子ID
     * @param number $id
     * @return array
     */
    public static function getSonsId($id = 0)
    {
        $array = $id_array = self::where('pid', $id)->column('id');
        foreach ($id_array AS $id_value) {
            $array = array_merge($array, static::getSonsId($id_value));
        }
        return $array;
    }
}