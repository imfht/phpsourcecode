<?php
/*
 *  2016年11月7日 星期一
 *  数据库服务逻辑库
*/
namespace app\Server;
use hyang\Logic;
use think\Db;
class Conero extends Logic
{
    /* 2017年3月1日 星期三
     * 快速获取网站的常量 常量名称.键值/ 常量名称
    */
    public function _const($id)
    {
        $map = '';
        if(is_string($id)){
            if(substr_count($id,'.') > 0){
                $pos = strpos($id,'.');
                $plusName = substr($id,$pos+1);
                $id = substr($id,0,$pos);
                $map = '`user_name`="CONST" and `gover_name`="'.$id.'" and `plus_name`="'.$plusName.'"';
                $ret = Db::table('sys_site')->where($map)->value('plus_desc');
                return $ret? $ret:'';
            }
            $map = '`user_name`="CONST" and `gover_name`="'.$id.'"';
        }
        elseif(is_array($id)) $map = $id;
        if(empty($map)) return '';
        $data = Db::table('sys_site')->where($map)->field('plus_name,plus_desc')->order('plus_name')->select();
        return $data;
    }
    // 系统常量 key => value 键值对
    public function constKV($id,$filter=null)
    {
        $data = $this->_const($id);
        $newArray = [];
        foreach($data as $v){
            $key = $v['plus_name'];
            $value = $v['plus_desc'];
            if($filter && is_string($filter)){
                 if($key == $filter) return $value;
            }
            $newArray[$key] = $value;
        }
        if($filter && is_string($filter)) return "";
        elseif($filter && is_array($filter)){
            return array_intersect_key($newArray,array_flip($filter));
        }
        return $newArray;
    }
    // 系统常量直接生产 select
    // option[value:选中值,default:默认值,phtml:select主体,unempty:无空选]
    public function const_option($id,$opt=[])
    {
        $value = isset($opt['value'])? $opt['value']:'';    // 选中值
        $default = isset($opt['default'])? $opt['default']:'';    // 默认值
        if(empty($value) && $default) $value = $default;
        $phtml = isset($opt['phtml'])? $opt['phtml']:'';    // select 头部
        $unempty = isset($opt['unempty'])? true:false;
        $xhtml = $unempty? '':'<option value=""></option>';
        $data = $this->_const($id);
        foreach($data as $v){
            $name = $v['plus_name'];
            $xhtml .= '<option value="'.$name.'"'.(($value && $name == $value)? ' selected':'').'>'.$v['plus_desc'].'</option>';
        }
        if($xhtml && $phtml) $xhtml = $phtml.$xhtml.'</select>';
        return $xhtml;
    }
    // 数据库日志存储表
    public function logDb($data,$fn=null)
    {
        if(is_callable($fn)){// 回调函数
            return $fn(Db::table('log_memord'));
        }
        if(is_string($data)){// 根据ID获取数据
            $res = Db::table('log_memord')->where('log_no',$data)->select();
            $res = isset($res[0])? $res[0]:$res;
            return $res;
        }
        elseif(is_array($data)){// 数据新增
            if(!isset($data['user_code'])) $data['user_code'] = uInfo('cid');
            return Db::table('log_memord')->insert($data);
        }
        return false;
    }
    // logDB-conero 日志
    public function logDbCro($indata)
    {
        $json = json_decode(file_get_contents(__DIR__.'/sysLogMemord.json'),true);
        $map = $json['map'];
        $ctt = Db::table('log_memord')->where($map)->count();
        $logNo = null;
        if($ctt == 0){
            $data = array_merge($map,$json['data']);
            $this->logDb($data);
        }
        else{
            $logNo = Db::table('log_memord')->where($map)->limit(1)->value('log_no');// 仅仅报错一条数据
        }
        if(empty($logNo)) return false;
        $map = ['log_no'=>$logNo,'name'=>'Conero_Report','keyword'=>'SYS'];
        $res = Db::table('log_memord2cld')->query('select `cld_no`,`content` from `log_memord2cld` where `log_no`=:log_no and `name`=:name `keyword`=:keyword and `date` like \''.(date('Y-m-')).'%\'',$map);
        if($res){// 更新数据
            $content = $res['content']."\r\n".$indata;
            return Db::table('log_memord2cld')->where('cld_no',$cld['cld_no'])->update(['content'=>$content,'edittm'=>sysdate()]);
        }
        else{// 新增数据
            $cld = array_merge($map,['date'=>sysdate('date'),'content'=>$content]);
            return Db::table('log_memord2cld')->insert($cld);
        }
    }
    // 任务记录表
    public function setTaskEvent($param,$delTask=false)
    {
        // 数据删除
        // if($delTask instanceof \Closure){
        if($delTask){
            return Db::table('sys_taskrpt')->where($param)->delete();
        }
        // 数据新增
        if(is_array($param)){
            $data = $param;
            $data['user_code'] = $this->code;
            return Db::table('sys_taskrpt')->insert($data);
        }
        elseif(is_string($param)){  // 任务结束登记
            return Db::table('sys_taskrpt')->where('listno',$param)->update([
                'end_mk'     => 'Y',
                'task_etime' => sysdate()
            ]);
        }        
    }
    // 系统发布信息数据获取
    public function sysInfor($type,$pushMk=null)
    {
        if(is_array($type) && empty($pushMk)) $map = $type;
        else{
            $map = [
                'type' => $type
            ];
            if($pushMk) $map['push_mk'] = $pushMk;
        }
        $tb = 'sys_infor';
        return $pushMk? Db::table($tb)->where($map)->find() : Db::table($tb)->where($map)->select();
    }
}