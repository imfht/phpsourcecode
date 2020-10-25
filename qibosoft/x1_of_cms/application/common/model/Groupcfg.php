<?php
namespace app\common\model;

use think\Model;
use think\Db;
//use plugins\config_set\model\Group AS configGroup;


/**
 * 会员组配置模型
 * @package app\admin\model
 */
class Groupcfg extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = '__GROUPCFG__';
	
	//主键不适合用默认的ID，因为设置每一组参数的话，有多条记录，不方便通过ID进行批量更新
	//protected $pk = 'c_key';

    // 自动写入时间戳
    protected $autoWriteTimestamp = false;
    
    
    /**
     * 更新或保存设置，这个没有限制具体某组，可以跨组设置，并且对于复选框没选择时未做判断处理
     * @param array $array
     * @param number $group
     * @return string|array|\think\false
     */
    public function save_data($array=[],$group=0){
        $sys_id = Db::name('config_group')->where('id',$group)->value('sys_id');     //避免有的出错,做修正处理
        $data = [];
        foreach($array AS $key=>$value){
            $num = $this->where('c_key',$key)->where('sys_id',$sys_id)->count('id');
            if ($num>1) {
                $this->where('c_key',$key)->where('sys_id',$sys_id)->where('type','<>',$group)->delete();   //避免有重复的,做修正处理
            }
            $id = $this->where('c_key',$key)->where('type',$group)->value('id');    //如果ID存在就执行更新，不存在就执行新增
            $data[] = [
                    'id'=>$id,
                     'c_key'=>$key,
                     'c_value'=>$value,
                    'sys_id'=>$sys_id,  //避免有的出错,做修正处理
                  ];
        }
        return empty($data) ? flase : $this->saveAll($data);
    }
    
    /**
     * 与系统雷同的变量名全删除
     */
    public function check_system_key_repeat(){
        //$array = getArray( $this->where('ifsys',1)->field('id,c_key,count(c_key) as num')->group('c_key')->order('num desc')->select() );
        $array = getArray( $this->where('ifsys',1)->field('c_key,sys_id,id,count(c_key) AS num')->group('c_key')->having('count(c_key)>1')->select() );
        foreach ($array AS $key=>$rs){
            $this->where('c_key','=',$rs['c_key'])->where('sys_id','<>','0')->limit($rs['num']-1)->delete();
        }
    }
    
    /**
     * 更新或保存参数设置,跨模块提交没做权限判断
     * @param array $data 提交的数据
     * @param number $group 分组ID
     * @return string|array|\think\false
     */
    public function save_group_data($data=[],$group=0){        
        $this->check_system_key_repeat();   //与系统雷同的变量名全删除
        $array = [];
        $config_data = $this->where('type' , $group)->column('c_key,form_type,id');
        foreach ($config_data as $name => $rs) {
            if (!isset($data[$name])) {
                switch ($rs['form_type']) {
                    // 开关
                    case 'switch':
                        $data[$name] = 0;
                        break;
                    case 'checkbox':
                        $data[$name] = '';
                    case 'checkboxtree':
                        $data[$name] = '';
                        break;
                }
            } else {
                // 如果值是数组则转换成字符串，适用于复选框等类型
                if (is_array($data[$name])) {
                    $data[$name] = implode(',', $data[$name]);
                    //continue;
                }
                switch ($rs['form_type']) {
                    // 开关
                    case 'switch':
                        $data[$name] = 1;
                        break;
                        // 日期时间
                    case 'date':
                    case 'time':
                    case 'datetime':
                        $data[$name] = strtotime($data[$name]);
                        break;
                }
            }
            $array[$name] = $data[$name];
            //$this->where('c_key', $name)->where('type', $group)->update(['c_value' => $data[$name]]);                
        }
        return $this->save_data($array,$group);
    }
    
    /**
     * 根据分类ID获得该组下的所有参数选项
     * @param unknown $group
     * @return unknown
     */
    public static function getListByGroup($group)
    {
        return self::where('type',$group)->order('list','desc')->column(true);
    }
    
    //只获取两个关键字段的信息
    public function getInfoByGroup($group)
    {
        return $this->where('type',$group)->column('c_key,c_value');
    }
    
	/**
	 * 取得系统参数,默认值的话,频道或插件以二维数据取出
	 * 一般情况,$name为空 $sys_id为null默认值的话,全局参数为一维数据取出来. 插件与频道为二维数据取出来,插件第一个下标是P_目录名,频道第一个下标是M_目录名
	 * @param string $name 关键字变量名,为空的话,就取出所有,不为空的话,就取指定的变量的值
	 * @param unknown $sys_id 频道或插件,频道为正数,插件为负数,0为系统全局参数. 指定正数或负数的话,只取对应频道或插件的数据
	 * @return mixed|array|array[]
	 */
    public static function getConfig($name = '',$sys_id=null)
    {
        $map = [];
        if ($sys_id!==null) {
//             //对应模型的所有分组ID
//             $group_ids = configGroup::getIdsBySys($sys_id);
//             if($sys_id==0){
//                 $group_ids = $group_ids ? array_merge($group_ids,[0]) : [0];    //未分组的字段，也作为系统字段
//             }
            
//             if(empty($group_ids)){
//                 return ;
//             }
//             $map = [
//                     'type' => ['in',$group_ids]
//             ];
            if($sys_id==0){
                $map['ifsys'] = $sys_id;    //其它插件也有部分是全局参数
            }else{
                $map['sys_id'] = $sys_id;
            }            
        }
        
        if(!empty($name)){
            $map['c_key'] = $name;
        }
        
        $listdb = self::where($map)->column('c_value,form_type,ifsys,sys_id,c_key','id');

        $result = [];
        foreach ($listdb AS $rs) {
            $key = $rs['c_key'];
            switch ($rs['form_type']) {
                case 'image':
                    $rs['c_value'] = tempdir($rs['c_value']);
                    break;
                case 'file':
                    $rs['c_value'] = tempdir($rs['c_value']);
                    break;
                case 'files':
                    $array = explode(',',$rs['c_value']);
                    $picdb = [];
                    foreach($array AS $file){
                        if (empty($file)) {
                            continue;
                        }
                        $picdb[] = tempdir($file);
                    }
                    $rs['c_value'] = $picdb;
                    break;
                case 'images':
                    $array = explode(',',$rs['c_value']);
                    $picdb = [];
                    foreach($array AS $pic){
                        if (empty($pic)) {
                            continue;
                        }
                        $picdb[] = [
                                'picurl'=>tempdir($pic)
                        ];
                    }
                    $rs['c_value'] = $picdb;
                    break;
                case 'images2':
                    $array = json_decode($rs['c_value'],true);
                    $picdb = [];
                    foreach($array AS $ps){
                        if (empty($ps['picurl'])) {
                            continue;
                        }
                        $ps['picurl'] = tempdir($ps['picurl']);
                        $picdb[] = $ps;
                    }
                    $rs['c_value'] = $picdb;
                    break;
                case 'array':
                    $rs['c_value'] = str_array($rs['c_value']);
                    break;
                case 'checkbox':
                    if ($rs['c_value'] != '') {
                        $rs['c_value'] = explode(',', $rs['c_value']);
                    } else {
                        $rs['c_value'] = [];
                    }
                    break;
            }
            if($sys_id){    //指定了插件或模块
                $result[$key] = $rs['c_value'];
            }else{  //读取所有                
                if($rs['sys_id']>0){
                    $dirname = modules_config($rs['sys_id'])['keywords'];
                    $dirname && $result['M__'.$dirname][$key] = $rs['c_value'];
                }elseif($rs['sys_id']<0){
                    $dirname = plugins_config(abs($rs['sys_id']))['keywords'];
                    $dirname && $result['P__'.$dirname][$key] = $rs['c_value'];
                }
                if($rs['ifsys']){
                    $result[$key] = $rs['c_value'];
                }
            }            
        }
        return $name != '' ? $result[$name] : $result;
    }

    //后台设置分组菜单
    public function nav(){
        $tab_list   = [];
        foreach ( config('webdb')['groups'] AS $key => $value) {
            $tab_list[$key]['title'] = $value;
            $tab_list[$key]['url']   = auto_url('index', ['group' => $key]);
        }

        $tab_list = array_merge($tab_list,[
            'other'=>[
                'title'=>'其它',
                'url'=>url('index', ['group' => 'other']),
            ]
        ]);
        return $tab_list;
    }
}