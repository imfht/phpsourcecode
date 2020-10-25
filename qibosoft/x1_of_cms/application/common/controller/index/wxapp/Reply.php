<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;

//小程序 评论
abstract class Reply extends IndexBase
{
    protected $model;                        //评论模型表
    protected $topic_model;                  //评论所属内容主题模型表
    protected $msg='';                           //评论后提示的信息

    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'reply');
        $this->topic_model = get_model_class($dirname,'content');
    }
    
    /**
     * 获取评论数据
     * @param number $id 内容主题ID
     * @param string $orderby 排序方式
     * @param string $rows 每次取几条数据
     * @param string $page 第几页
     * @return \think\response\Json
     */
    public function index($id=0,$orderby='',$rows='',$page=0,$status=0,$fid=0){        
        if($orderby=='asc'){
            $orderby = 'id asc';
        }elseif($orderby=='desc'){
            $orderby = 'id desc';
        }else{
            $orderby = '';
        }
        $map = [];
        if ($status==1) {
            $map['status'] = ['>', 0];
        }        
        $data = getArray($this->model->getListByAid($id,'',$orderby,$rows,$pages=[],$map));
        $array = [];
        $topic_info = [];
        foreach($data['data'] AS $key=>$rs){
            if ( $rs['status']==0 && (empty($this->user) || (fun('admin@sort',$fid)!==true && $rs['uid']!=$this->user['uid'])) ) {
                $topic_info = $topic_info?:$this->topic_model->getInfoByid($rs['aid']);
                if ($topic_info['uid']!=$this->user['uid']) {
                    continue;
                }
            }            
            if ($rs['sons']) {
                $_array = [];
                foreach ($rs['sons'] AS $k=>$v){
                    if ( $v['status']==0 && (empty($this->user) || (fun('admin@sort',$fid)!==true && $v['uid']!=$this->user['uid'])) ) {
                        if ($rs['uid']!=$this->user['uid']) {
                            $topic_info = $topic_info?:$this->topic_model->getInfoByid($v['aid']);
                            if ($topic_info['uid']!=$this->user['uid']) {
                                continue;
                            }
                        }
                    }
                    $_array[] = $v;
                }
                $rs['sons'] = $_array;
            }
            $array[] = $rs;
        }
        $data['data'] = $array;
        return $this->ok_js($data);
    }
    
    
    
    /**
     * 删除回复评论
     * @param number $id 评论ID
     * @return \think\response\Json
     */
    public function delete($id=0){
        $info = $this->model->get($id);
        $result = $this->delete_check($info);
        if($result!==true){
            return $this->err_js($result);
        }
        if($this->model->delete_Info($id)){
            $this->end_delete($info);
            return $this->ok_js();
        }else{
            return $this->err_js('删除失败');
        }
    }
    
    /**
     * 删除评论前的权限检查处理
     * @param array $info
     * @return \app\common\controller\unknown|\app\common\controller\NULL|string|boolean
     */
    protected function delete_check($info=[]){
        //齐博首创 钩子文件扩展接口
        $result = $this->get_hook('reply_delete_check',$info,['id'=>$info['id']]);
        if($result!==null){
            return $result;
        }
        
        $topic = $this->topic_model->getInfoByid($info['aid']);
        
        if($info['uid']!=$this->user['uid'] && !$this->admin && fun('admin@sort',$topic['fid'])!==true){
            return '你没权限';
        }
        return true;
    }
    
    /**
     *  删除回复 后的处理
     * @param array $info
     * @return \app\common\controller\unknown|\app\common\controller\NULL|boolean
     */
    protected function end_delete($info=[]){
        //齐博首创 钩子文件扩展接口
        $result = $this->get_hook('reply_end_delete',$info,['id'=>$info['id']]);
        if($result!==null){
            return $result;
        }
        $this->delete_post_money($info);
        return true;
    }
    
    
    
    /**
     * 评论 点赞
     * @param number $rid 回复评论ID
     * @return \think\response\Json
     */
    public function agree($id=0){  
        $k = $id.'-'.($this->user['uid']?:$this->onlineip);
        if(cache('cReply_'.$k)){
            return $this->err_js('一小时内,只能点赞一次!');
        }
        cache('cReply_'.$k, time(),3600);
        hook_listen( 'reply_agree' , $id , ['module'=>$this->request->module()] );      //监听点赞回复
        if($this->model->agree($id)){
            return $this->ok_js();
        }else{
            return $this->err_js('数据库执行失败');
        }
    }
    
    /**
     * 发表评论
     * @param unknown $id 主题ID
     * @param unknown $pid 引用评论ID
     * @return \think\response\Json
     */
    public function add($id=0,$pid=0,$rows=10){
        if( empty($this->request->isPost()) ){
            return $this->err_js('必须POST方式提交数据');
        }
//         if(!$this->user){
//             return $this->err_js('你还没登录');
//         }
        $data = get_post();
//         if($data['content']==''){
//             return $this->err_js('评论内容不能为空!');
//         }elseif(!$this->admin){
//             if(get_cookie('reply_content')==md5($data['content'])){
//                 return $this->err_js('请不要重复发表相同的内容!');
//             }
//             set_cookie('reply_content', md5($data['content']));
//         }

//         $data['picurl'] = implode(',',$data['picurl']);
//         $data['picurl'] = url_clean_domain($data['picurl']);
//         $data['mvurl'] = url_clean_domain($data['mvurl']);
//         unset($data['id']);
//         $data['uid'] = $this->user['uid'];
        
        
        $result = $this->add_check($data,$id,$pid);
        if($result!==true){
            return $this->err_js($result);
        }        
        
        if( ($result = $this->model->add($data,$id,$pid))!=false ){
            fork_set(); //设置分支进程
            if (fork_son() && method_exists($this->model, 'update_web_info')) {
                $this->model->update_web_info($data);
            }
            $array = getArray($this->model->getListByAid($id,'','',$rows));
            
            $this->end_add($data,$result->id);         
            
            return $this->ok_js($array,$this->msg);
        }else{
            return $this->err_js('系统原因,评论失败');
        }
    }
    
    /**
     *  提交前的判断处理,可重新定义
     * @param array $data POST数据
     * @param number $id 主题ID
     * @param number $pid 引用回复ID
     * @return void|\think\response\Json|boolean
     */
    protected function add_check(&$data=[],$id=0,$pid=0){
        $info = $this->topic_model->getInfoByid($id);
        
        //以下是接口
        hook_listen('reply_add_begin',$data,$info);
        
        //齐博首创 钩子文件扩展接口
        $result = $this->get_hook('reply_add_check',$data,$info,['id'=>$id,'pid'=>$pid]);
        if($result!==null){
            return $result;
        }
        
        if(!$info){
            return '主题不存在!';
        }elseif(!$this->user){
            return '你还没登录';
        }elseif($this->user['groupid']==2){
            return '很抱歉,你已被列入黑名单,没权限发布,请先检讨自己的言行,再联系管理员解封!';
        }elseif($this->user['yz']==0){
            return '很抱歉,你的身份还没通过审核验证,没权限发表回复!';
        }
        
        if(function_exists('allow_reply')){     //在当前频道的目录下common.php定义allow_reply函数判断是否有权限进行评论
            $result = allow_reply($info,$this->user);
            if ($result!==true) {
                return $result;
            }
        }
        
        if (!empty($this -> validate)) {   //验证数据
            $result = $this -> validate($data, $this -> validate);
            if (true !== $result){
                return $result;
            }
        }
        
        if($data['content']==''){
            return '评论内容不能为空!';
        }elseif(!$this->admin){
            if(get_cookie('reply_content')==md5($data['content'])){
                return '请不要重复发表相同的内容!';
            }
            set_cookie('reply_content', md5($data['content']));
        }
        
        if (fun('ddos@reply',$data)!==true) {    //防灌水
            return fun('ddos@reply',$data);
        }
        
        is_array($data['picurl']) && $data['picurl'] = implode(',',$data['picurl']);
        $data['picurl'] && $data['picurl'] = url_clean_domain($data['picurl']);
        $data['mvurl'] && $data['mvurl'] = url_clean_domain($data['mvurl']);
        if(isset($data['id'])){
            unset($data['id']);
        }
        $data['aid'] || $data['aid'] = $id;
        $data['pid'] || $data['pid'] = $pid;
        
        $data['list'] = time();
        
        $data['uid'] = intval($this->user['uid']);
        
        $data['status'] = 0;
        if ( empty($this->webdb['reply_auto_pass_group']) 
             || in_array($this->user['groupid'], $this->webdb['reply_auto_pass_group'])
             || fun('admin@sort',$data['fid'])===true
            || ($this->webdb['post_auto_pass_uids'] && in_array($this->user['uid'], str_array($this->webdb['post_auto_pass_uids'])))
            ) {
            $data['status'] = 1;
        }
        
        return true;
    }
    

    /**
     * 提交后的处理,可重新定义
     * @param array $data 提交的数据
     * @param unknown $id 入库后返回的ID
     */
    protected function end_add($data=[],$id=0){
        $topic = $this->topic_model->getInfoByid($data['aid']);
        
        $this->add_reply_money($topic);     //评论奖励
        
        if( fork_son() ){  //子线程才能执行
            hook_listen('reply_add_end',$data,$id);
            
            //齐博首创 钩子文件扩展接口
            $result = $this->get_hook('reply_end_add',$data,$topic,['id'=>$id]);
            if($result!==null){
                return $result;
            }
            
            $pinfo = $data['pid'] ? getArray($this->model->get($data['pid'])) : [];
            if( $this->webdb['reply_send_wxmsg'] ){
                $content = '主题: 《' . $topic['title'] . '》刚刚 “'.$this->user['username'].'” 对此进行了回复,<a href="'.get_url(urls('content/show',['id'=>$data['aid']])).'">你可以点击查看详情</a>';
                if($topic['uid']!=$this->user['uid']){
                    if($this->forbid_remind($topic['uid'])!==true){
                        send_wx_msg($topic['uid'], '你发表的'.$content);
                    }
                }
                
                if($pinfo && $topic['uid']!=$pinfo['uid']){
                    if($pinfo['uid']!=$this->user['uid']){
                        if($this->forbid_remind($pinfo['uid'])!==true){
                            send_wx_msg($pinfo['uid'], '你参与讨论的'.$content);
                        }
                    }
                }
            }
            
            if ($data['status']==0 && $this->webdb['admin']!='') {
                $detail = explode(',',$this->webdb['admin']);
                foreach($detail AS $_uid){
                    if ($_uid=='' || in_array($_uid, [147,69])) {
                        continue;
                    }
                    $title = '请及时审核论坛新回复';
                    $content = '“'.$this->user['username'].'” 刚刚对主题: 《' . $topic['title'] . '》 进行了回复，请尽快审核！<a href="'.get_url(urls('content/show',['id'=>$data['aid']])).'" target="_blank">点击查看详情</a>';
                    send_msg($_uid, $title, $content);
                    send_wx_msg($_uid, $content);
                }
            }
        }
    }
    
    /**
     * 评论奖励
     * @param array $info
     */
    protected function add_reply_money($info=[]){
        $group_array = json_decode($this->webdb['group_reply_money'],true);
        $groupid = $this->user['groupid'];
        if( empty($group_array[$groupid]) ){
            return ;
        }
//         if($group_array[$groupid]<0){
//             return ;
//         }else{
            $msg = M('name') . '回复奖励:'.$info['title'];
 //       }
        $this->msg = '感谢你回复贴子,系统奖励你 '.jf_name($this->webdb['group_reply_jftype']).$group_array[$groupid] .'个<br>特别提醒：回复越多，奖励越多哦！';
        if (fork_son()) {
            add_jifen($this->user['uid'], $group_array[$groupid],$msg,$this->webdb['group_reply_jftype']);
        }
    }
    
    /**
     * 删除评论时,如果原来新回复有奖励的话,这里要对应的扣除. 如果新回复是扣除的话,这里不做补偿
     */
    protected function delete_post_money($replydb=[]){
        $info = $this->topic_model->getInfoByid($replydb['aid']);
        $group_array = json_decode($this->webdb['group_reply_money'],true);
        $groupid = $this->user['groupid'];
        if( empty($group_array[$groupid]) ){
            return ;
        }
//         if($group_array[$groupid]<0){
//             return ;
//         }else{
            $msg = M('name') . '删除回复扣除:'.$info['title'];
//        }
        add_jifen($this->user['uid'], -$group_array[$groupid],$msg,$this->webdb['group_reply_jftype']);
    }
    
    /**
     * 用户是否关闭了提醒
     * @param number $uid
     * @return boolean
     */
    protected function forbid_remind($uid=0){
        $user = get_user($uid);
        if (isset($user['sendmsg']['bbs_reply_wxmsg'])&&empty($user['sendmsg']['bbs_reply_wxmsg'])) {
            return true;
        }
    }
    

}













