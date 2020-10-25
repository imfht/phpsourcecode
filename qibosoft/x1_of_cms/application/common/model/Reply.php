<?php
namespace app\common\model;
//use think\Db;
use think\Model;

abstract class Reply extends Model
{
    // 设置当前模型对应的完整数据表名称
    public $table; // '__FORM_FIELD__';
    
    //以下三项必须在这里先赋值，不然下面的重新定义table会不生效
    protected $autoWriteTimestamp = true;   // 自动写入时间戳
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $resultSetType = 'array';
    
    protected static $base_table;
    protected static $model_key;
    protected static $table_pre;
    protected static $content_model;    //内容模型
    
    //为了调用initialize初始化,生成数据表前缀$model_key
    protected static function scopeInitKey(){}
    protected function initialize(){
        parent::initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        self::$model_key = $array[0][1];
        self::$base_table = self::$model_key .'_content';
        self::$table_pre = config('database.prefix');
        //字段表，带数据表前缀如qb_form_field
        $this->table = self::$table_pre.self::$model_key.'_reply';
        self::$content_model = get_model_class(self::$model_key,'content');     //内容模型
    }
    
    /**
     * 点赞
     * @param number $rid
     * @return boolean
     */
    public static function agree($rid=0){
        empty(self::$model_key) && self::InitKey();
        if (empty($rid)) {
            return false;
        }
        if(self::where('id','=',$rid)->setInc('agree',1)){
            return true;
        }
    }
    
    /**
     * 删除评论,在前台的话,是伪删除
     * @param unknown $id
     * @return boolean
     */
    public static function delete_Info($id){
        empty(self::$model_key) && self::InitKey();
        $info = self::get($id);
        //$topic = self::$content_model->where('id',$info['aid'])->find(); 
        if (defined('IN_ADMIN')) {
            $result = self::destroy($id);
        }else{
            $result = self::where('id',$id)->update([
                'status'=>-1
            ]);
        }
        if($result){
            self::where('pid',$id)->update(['pid'=>0]); //把引用评论提取出来
            self::$content_model->addReply($info['aid'],false);
            return true;
        }
    }
    
    /**
     * 发表评论
     * @param array $data 回复的相关数据
     * @param number $id 主题ID
     * @param number $pid 引用回复的那个评论ID
     * @return boolean|\app\common\model\Reply
     */
    public static function add(&$data=[],$id=0,$pid=0){
        empty(self::$model_key) && self::InitKey();
        if (empty($id)) {
            $id = $data['aid'];
        }
        if (empty($id)) {
            return false;
        }
        if (empty($pid)) {
            $pid = $data['pid'];
        }
        if (empty($data['uid'])) {
            $data['uid'] = intval(login_user('uid'));
        }
        $data['sysid'] = intval(modules_config(self::$model_key)['id']);    //频道ID,其它频道调用的话,需要进一步判断与完善
        $data['create_time'] = $data['list'] = time();
        $data['ip'] = get_ip();
        $data['aid'] = $id;
        $data['pid'] = intval($pid);
        hook_listen('model_replyadd_begin',$data);    //入库前的钩子,可以在这里设置禁止发布信息
        $result = self::create($data);
        if($result){
            hook_listen('model_replyadd_end',$data,$result->id);    //成功发表信息后的钩子
            self::$content_model->addReply($id);            
            $array = ['id'=>$id,'replyuser'=>login_user('username'),];
            $info = self::$content_model->getInfoById($id);
            $info['list']>time() || $array['list'] = time();
            self::$content_model->editData($info['mid'],$array);
            if($pid){
                self::where('id','=',$pid)->setInc('reply',1);      //引用评论
                self::where('id','=',$pid)->update(['id'=>$pid,'list'=>time()]);
            }
        }
        return $result;
    }
    
    /**
     * 根据主题ID获取对应的评论数据
     * @param number $aid
     * @param number $sysid
     * @param string $order
     * @param number $rows
     * @param array $pages
     * @return unknown
     */
    public static function getListByAid($aid=0,$sysid=0,$order='',$rows=10,$pages=[],$map=[])
    {
        empty(self::$model_key) && self::InitKey();
        if(empty($order)){
            $order = 'list desc ,id desc';
        }elseif($order == 'list desc'){
            $order .= ',id desc';
        }        
        $_map = ['aid'=>$aid,'pid'=>0];
        if ( !defined('IN_ADMIN') && !$map['status'] ) {
            $map['status'] = ['<>', -1];
        }
        $data_list = self::where($_map)->where($map)->order($order)->paginate(
                empty($rows)?null:$rows,    //每页显示几条记录
                empty($pages[0])?false:$pages[0],
                empty($pages[1])?[]:$pages[1]
                );
        
        $data_list->each(function(&$rs,$key){
            $rs = self::format_content($rs);
            if($rs['reply']){
                $rs['sons'] = self::getSons($rs['id'],$map);
            }else{
                $rs['sons'] = [];
            }
            return $rs;
        });
        return $data_list;
    }
    
    protected static function format_content($rs=[]){
        $rs['username'] = get_user_name($rs['uid']);
        $rs['user_icon'] = get_user_icon($rs['uid']);
        $rs['time'] = format_time($rs['create_time'],true);
        $rs['content'] = fun('Content@bbscode',$rs['content']);
        if($rs['mvurl']){
            $rs['mvurl'] = tempdir($rs['mvurl']);
        }
        $rs['picurls'] = [];
        if($rs['picurl']!=''){
            $detail = explode(',', $rs['picurl']);
            foreach ($detail AS $k=>$value){
                $detail[$k] = tempdir($value);
            }
            $rs['picurls'] = $detail;
            $rs['picurl'] = $detail[0];
        }
        return $rs;
    }
    
    public static function getSons($pid,$map=[]){
        empty(self::$model_key) && self::InitKey();
        $_map = [
            'pid'=>$pid,
        ];
        $array = self::where($_map)->where($map)->order('id asc')->column(true);
        foreach($array AS $key=>$rs){
            $rs = self::format_content($rs);
            $array[$key] = $rs;
        }
        $array = array_values($array);
        return $array;
    }
    
    
    
}