<?php
namespace app\member\controller\wxapp;

use app\common\model\Msg AS Model;
use app\common\controller\IndexBase;
//use app\common\controller\MemberBase;

class Msg extends IndexBase
{
    protected $my_uid = 0;
    protected function _initialize()
    {
        parent::_initialize();
        $my_uid = input('my_uid');
        if ($my_uid) {
            if ( (empty($this->user) && $my_uid<9999999) || ($this->user&&$this->user['uid']!=$my_uid) ) {
                $this->error("my_uid数值有误");
            }
            $this->my_uid = $my_uid;
        }else{
            $this->my_uid = $this->user['uid'];
        }
        if (empty($this->my_uid)){
            $this->error("请先登录!");
        }
    }
    /**
     * 查看最新的信息
     * @param number $id
     * @return void|\think\response\Json|\app\member\controller\wxapp\unknown|void|unknown|\think\response\Json
     */
    public function showmore($id=0,$type='new')
    {
        $info = $this->get_info($id);
        if(!is_array($info)){
            return $info;
        }
        
        $this->map = [
            'touid'=>$this->my_uid,
            'uid'=>$info['uid'],
            'id'=>['<=',$id],
        ];
        
        $this->OrMap = [
            'uid'=>$this->my_uid,
            'touid'=>$info['uid'],
            'id'=>['<=',$id],
        ];
        
        $this->NewMap = [
            'uid'=>$this->my_uid,
            'touid'=>$info['uid'],
            'id'=>['>',$id],
            'ifread'=>0,
        ];
        
        $data_list = Model::where(function($query){
            $query->where($this->map);
        })->whereOr(function($query){
            $query->where($this->OrMap);
        })->whereOr(function($query){
            $query->where($this->NewMap);
        })->order("id desc")->paginate(5);

        $data_list->each(function(&$rs,$key){
            $rs['from_username'] = get_user_name($rs['uid']);
            $rs['from_icon'] = get_user_icon($rs['uid']);
            return $rs;
        });
        return $this->ok_js($data_list);
    }
    
    
    /**
     * AJAX调用，调用类似微信那样的用户列表。
     * @param number $uid 当前登录用户的UID
     * @param number $rows
     * @param number $page
     */
    public function get_listuser($rows=0,$page=0){
        $uid = $this->my_uid;
        $array = Model::get_listuser($uid,$rows,$page);
        if (empty($array) && $page<2) {
            \app\member\controller\Msg::add_kefu(true);
            $array =  Model::get_listuser($uid,$rows,$page);
        }
        return $this->ok_js($array);
    }
    
    /**
     * 消息列表
     * @return void|unknown|\think\response\Json
     */
    public function index()
    {
        if (empty($this->user)) {
            return $this->err_js('请先登录!');
        }
        $map = [
            'touid'=>$this->my_uid
        ];
        $data_list = Model::where($map)->order("id desc")->paginate(15);
        $data_list->each(function($rs,$key){
            $rs['from_username'] = get_user_name($rs['uid']);
            return $rs;
        });       
        return $this->ok_js($data_list);
    }
    
    /**
     * 检查是否有新消息
     * @return void|\think\response\Json
     */
    public function checknew(){
        $num = Model::where([ 'touid'=>$this->my_uid,'ifread'=>0 ])->count('id');
        if($num>0){
            return $this->ok_js(['num'=>$num]);
        }else{
            return $this->err_js('没有新消息');
        }
    }
    
    /**
     * 删除单条信息
     * @param unknown $id
     * @return void|unknown|\think\response\Json
     */
    public function delete($id)
    {
        if (empty($this->user)) {
            return $this->err_js('请先登录!');
        }
        $info = getArray(Model::where(['id'=>$id])->find());
        if(!$info){
            return $this->err_js('内容不存在');
        }
        
        $qun_info = [];
        if($info['qun_id']>0){
            $qun_info = fun('qun@getByid',$info['qun_id']);
        }
        
        if (empty($qun_info) || empty($this->user['uid']) || $this->user['uid']!=$qun_info['uid']) {
            if($info['uid']!=$this->user['uid'] && $info['touid']!=$this->user['uid']){
                return $this->err_js('你无权删除');
            }elseif($info['uid']==$this->user['uid']&&$info['qun_id']==0&&$info['ifread']){
                if(time()-strtotime($info['create_time'])>60*3){
                    return $this->err_js('该消息发送时间已超过3分钟，并且对方已读，你不能再删除！');
                }
            }
        }
        
        if (Model::where(['id'=>$id])->delete()) {
            return $this->ok_js();
        }else{
            return $this->err_js('删除失败');
        }
    }
    
    /**
     * 上传方法
     * @param string $url 服务器网址
     * @param string $path 本地文件路径
     * @return mixed
     */
    protected function curl_postfile($url='',$path=''){
        $curl = curl_init();
        if (class_exists('\CURLFile')) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            $data = array('file' => new \CURLFile(realpath($path)));//>=5.5
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
            }
            $data = array('file' => '@' . realpath($path));//<=5.5
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT,"TEST");
        $result = curl_exec($curl);
        $error = curl_error($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }
    
    protected function get_voice($sid=''){
        $access_token = wx_getAccessToken();
        $wx_api_url="https://api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$sid";
        $strcode = file_get_contents($wx_api_url);
        $filename = $this->user['uid'] . '_' . time() . '.amr';
        $path = config('upload_path') . '/files/' . date('Ymd') . '/' ;
        makepath($path);
        write_file($path . $filename,$strcode);
        if(filesize($path . $filename)>1024){
            $amr = $path . $filename;
        }else{
            return '下载微信语音文件失败!';
        }
        $mp3 = str_replace('.amr', '.mp3', $amr);
        $data = $this->curl_postfile('http://svn.php168.com/mp3.php',$amr);
        write_file($mp3, $data);
        if (filesize($mp3)>1024) {
            $fileurl = 'uploads/files/' . date('Ymd') . '/' . basename($mp3);
            return  [
                'url'=>$fileurl,
                'size'=>filesize($mp3),
                'content'=>'<audio controls="controls"><source src="'.tempdir($fileurl).'" type="audio/mp3" />你的浏览器不支持</audio>',
            ];
        }else{
            return 'amr转mp3失败';
        }
    }
    
    /**
     * 发送消息
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function add()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            if ($data['content']==''&&empty($data['voiceid'])) {
                return $this->err_js('内容不能为空');
            }
            if ($data['voiceid']!='') {
                $array = $this->get_voice($data['voiceid']);
                if (is_array($array)) {
                    $data['content'] = $array['content'];
                }else{
                    return $this->err_js($array);
                }
            }
            if($data['ext_sys'] && !is_numeric($data['ext_sys'])){
                $data['ext_sys'] = modules_config($data['ext_sys'])['id'];
            }
            $post_uid = $data['uid'];   //后面这个值$data['uid']会变动
            $touser_info = $info = [];
            if ($data['uid']<0) {   //圈子群聊
                if (empty($this->user)) {
                    return $this->err_js('请先登录!');
                }
                $qun_id = abs($data['uid']);
                $info = fun('qun@getByid',$qun_id);
                if (!$info) {
                    return $this->err_js('该'.__QUN__.'不存在!');
                }
                $data['qun_id'] = $qun_id;
                $data['title'] || $data['title'] = '';
                $data['touid'] = 0;
                $data['ifread'] = $info['uid']==$this->user['uid']?1:0;
                \app\qun\model\Content::updates($info['id'],[
                    'mid'=>$info['mid'],
                    'list'=>time(),
                ]);
                Model::where('qun_id',$qun_id)->update(['update_time'=>time()]);    //其它人的群聊消息方便排在前面
            }else{
                $guest_name = '游客';
                //这里有点BUG,如果用户名是数字的话,偶尔会冲突
                if ($data['uid']>9999999) {
                    $detail = ipfrom(long2ip($data['uid']));
                    $guest_name = is_array($detail)?$detail['city']:'游客';
                }else{
                    
                    cache("msg_listuser-".$data['uid'],null);
                    $touser_info = $data['uid'] ? get_user($data['uid']) : get_user($data['touser'],'username');
                    if (!$touser_info) {
                        return $this->err_js('该用户不存在!');
                    }
                    $data['uid'] || $data['uid'] = $touser_info['uid'];
                }
                if (!$data['title']) {
                    $data['title'] = '来自 '.($this->user['username']?:$guest_name).' 的私信';
                }
                $data['touid'] = $data['uid'];
            }      
            $data['uid'] = $this->my_uid;  //!!!!!!务必高度注意这里 uid值变成了当前用户自己的ID,不再是对方的ID
            $data['content'] = fun('Filter@str',$data['content']);            
            //$data['content'] = str_replace(["\n",' '],['<br>','&nbsp;'],filtrate($data['content']));
            $result = Model::add($data,$this->admin,$data['push_id']?false:true);
            if(is_numeric($result)){    //发送成功
                $content = ($this->user['username']?:'游客') . ' 给你发了一条私信,请尽快查收,<a href="'.get_url(urls('member/msg/show',['id'=>$result])).'">点击查收</a>';
                if(empty($qun_id)){
                    if ($touser_info['weixin_api']) {
                        //检查是否设置了只接收好友的微信通知
                        if (empty($touser_info['sendmsg']['forbid_stranger_wxmsg']) || fun('Friend@my',$data['touid'])['type']==2) {
                            send_wx_msg($touser_info['weixin_api'], $content);
                        }
                    }
                }else{
                    if( preg_match("/@([^ ]+)/", $data['content'],$array) ){
                        if(empty($data['send_to'])){
                            $data['send_to'] = get_user($array[1],'username')['uid'];
                        }
                        if($data['send_to']){
                            $_content = $this->user['username'] . ' 在 '.$info['title'].' 群中@你,请尽快查阅,<a href="'.get_url(get_url('msg',-$qun_id)).'">点击查阅</a>';
                            send_wx_msg($data['send_to'], $_content);
                        }                        
                    }
                }
                
                //推消息要用到
//                 $msginfo = [
//                     Model::format_msg(getArray(Model::get($result))),
//                 ];
//                 $msg_array = [
//                     'type'=>'newmsg',
//                     'data'=>$msginfo,
//                 ];
//                 $msg_array['ext']['maxid'] = $result;
//                 if ($post_uid<0) {//代表群聊 查找是否有直播数据
//                     $live_array = cache('live_qun');    //这里有个BUG,如果进后台操作过东西,缓存就会被清空,导致这里没数据
//                     if($live_array['qun'.$post_uid]){
//                         $live_array['qun'.$post_uid]['time'] = 0;    //此参数将弃用
//                         $live_array['qun'.$post_uid]['push_url']='';
//                         $msg_array['ext']['live_video'] = $live_array['qun'.$post_uid];
//                     }
//                 }
                
//                 fun("Gatewayclient@send_to_group",$this->user['uid'],$post_uid,$msg_array);     //同时通知其它客户, 推数据
                if ($data['push_id']) { //目的是方便把push_id替换为真实的ID,方便删除消息
                    $msg_array = [
                        'type'=>'new_msg_id',
                        'data'=>[
                            'id'=>$result,
                            'push_id'=>$data['push_id'],
                        ],
                    ];
                    fun("Gatewayclient@send_to_group",$this->my_uid,$post_uid,$msg_array); //ws要用到真实的消息ID,方便删除该条信息
                }
                return $this->ok_js(['id'=>$result]);
                
            }elseif($result['errmsg']){
                return $this->err_js($result['errmsg']);
            }else{
                return $this->err_js('发送失败');
            }
        }
    }
    
    /**
     * 获取单条信息
     * @param number $id
     * @return void|\think\response\Json|unknown
     */
    protected function get_info($id=0){
        if (empty($this->user)) {
            return $this->err_js('请先登录!');
        }
        $info = getArray(Model::where(['id'=>$id])->find());
        if(!$info){
            return $this->err_js('内容不存在');
        }elseif($info['uid']!=$this->user['uid']&&$info['touid']!=$this->user['uid']){
            return $this->err_js('你无权查看');
        }elseif($info['touid']==$this->user['uid']){
            Model::update(['id'=>$id,'ifread'=>1]);
        }
        return $info;
    }
    
    /**
     * 查看单条信息
     * @param number $id
     * @return void|\think\response\Json|unknown|void|unknown|\think\response\Json
     */
    public function show($id=0)
    {
        $info = $this->get_info($id);        
        if(!is_array($info)){
            return $info;
        }        
        return $this->ok_js($info);
    }
    
    /**
     * 清空信息
     * @return void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function clean()
    {
        if (empty($this->user)) {
            return $this->err_js('请先登录!');
        }
        $touid=$this->user['uid'];
       // if(Model::destroy(['touid' => $touid])){
        if(Model::where('touid','=',$touid)->delete()){
            return $this->ok_js();
        }else{
            return $this->err_js('清空失败');
        }
    }
    
    /**
     * 标志为已读
     */
    public function set_read($ids=[]){
        if (empty($this->user)) {
            return $this->err_js('请先登录!');
        }
        if (empty($ids)) {
            return $this->err_js('请必须选择一项');
        }
        foreach ($ids AS $id){
            Model::where('id',$id)->where('touid',$this->user['uid'])->update(['ifread'=>1]);
        }
        return $this->ok_js();
    }
}
