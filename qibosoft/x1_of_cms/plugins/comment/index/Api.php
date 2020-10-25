<?php
namespace plugins\comment\index;
use plugins\comment\model\Content AS contentModel;
use app\index\model\Label AS LabelModel;
use app\common\controller\IndexBase;

class Api extends IndexBase
{
    protected $validate = '';
    private static $get_children = null;    //仅仅只取引用回复,发表评论时使用
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new contentModel();
    }
    
    public function act($type='',$id=0,$sysid=0,$aid=0,$rows=0,$status=0,$order='',$by='',$page=0){
        if($type=='delete'){
            return $this->delete($id);
        }
    }
    
    /**
     * 被各频道调用发布评论接口
     * @param number $sysid
     * @param number $aid
     * @return string
     */
    public function add($name='',$page='',$pagename='',$sysid=0, $aid=0 ,$rows=0,$order='',$by='',$status=''){
        $_array = input();
        $agree = $_array['agree'];
        $type = $_array['type'];
        $data_type = $_array['data_type'];
        $pid = $_array['pid'];
        $id = $_array['id'];
        
//         self::$get_children = $pid;
//         echo self::ajax_content($name,$page,$pagename,$sysid, $aid,$rows,$order,$by,$status,$type);
//         exit;
        
        if($agree==1){  //点赞            
            if(time()-get_cookie('comment_'.$id)<3600){
                return $this->err_js('一小时内,只能点赞一次!');
            }
            set_cookie('comment_'.$id, time());

			//监听点赞回复
			$this->get_hook('comment_agree',$data=[],[],['id'=>$id,'aid'=>$aid,'sysid'=>$sysid]);
            hook_listen( 'comment_agree' , $id , ['aid'=>$aid,'sysid'=>$sysid] );      
			

            if( contentModel::where('id',$id)->setInc('agree',1) ){
                //echo self::ajax_content($name,$page,$pagename,$sysid, $aid,$rows,$order,$by,$status,$data_type);
                return $this->ok_js();
            }else{
                return $this->err_js('数据库执行失败!');
            }
        }elseif ($this->request->isPost()) {
            $data = $this->request->post();
            
            if($this->user['groupid']==2){
                return $this->err_js('很抱歉,你已被列入黑名单,没权限发布评论,请先检讨自己的言行,再联系管理员解封!');
            }elseif($this->user['yz']==0){
                return $this->err_js('很抱歉,你的身份还没通过审核验证,没权限发布评论!');
            }elseif(empty($this->user) && empty($this->webdb['allow_guest_post_comment'])){
                return $this->err_js('很抱歉,系统禁止游客发表评论!<br>请复制保存好你的内容,登录后,再评论<br>'.$data['content']);
            }elseif( $this->webdb['can_post_comment_group'] && !in_array($this->user['groupid'],$this->webdb['can_post_comment_group'])){
                return $this->err_js('很抱歉,你所在用户组禁止评论!');
            }elseif(empty($this->admin) && $this->webdb['forbid_comnent_phone_noyz'] && empty($this->user['mob_yz'])){
                return $this->err_js('很抱歉,你还没有验证手机,不能发表评论,请进会员中心验证手机');
            }
            
            if (!empty($this -> validate)) {   //验证数据
                $result = $this -> validate($data, $this -> validate);
                if (true !== $result){
                    return $this->err_js($result);
                }
            }            
            if($data['content']==''){
                return $this->err_js('内容不能为空');
            }elseif(!$this->admin){
                if(get_cookie('reply_content')==md5($data['content'])){
                    return $this->err_js('请不要重复发表相同的内容!');
                }
                set_cookie('reply_content', md5($data['content']));
            }
            
            if (fun('ddos@reply',$data)!==true) {    //防灌水
                return $this->err_js(fun('ddos@reply',$data));
            }
            
            if ( $this->user  ) {
                //如果不设置的话,就默认都通过审核
                if ( empty($this->webdb['post_auto_pass_comment_group']) || in_array($this->user['groupid'],$this->webdb['post_auto_pass_comment_group']) ) {
                    $data['status'] = 1;
                }else{
                    $data['status'] = 0;
                }
            }else{
                $data['status'] = $this->webdb['guest_auto_pass_comment'];
            }
            
            if(empty($this->admin) && $this->webdb['forbid_pass_phone_noyz'] && empty($this->user['mob_yz'])){
                $data['status'] = 0;    //没验证手机,强制变为非审核状态
            }
            
            $data['aid'] = $aid;
            $data['sysid'] = $sysid;
            $data['pid'] = intval($pid);
            $data['uid'] = intval($this->user['uid']);
            $result = contentModel::create($data);
            if ($result) {

				//钩子接口
				$this->get_hook('comment_add_end',$data,[],['id'=>$result->id]);
                hook_listen('comment_add_end',$data,$result->id);		

                if($pid){
                    self::$get_children = $pid;
                    contentModel::where('id',$pid)->setInc('reply',1);
                }
                $this->send_msg($data);
                return self::ajax_content($name,$page,$pagename,$sysid, $aid,$rows,$order,$by,$status,$data_type);
            } else {
                return $this->err_js('数据库执行失败!');
            }
        }
    }
    
    /**
     * 发送消息
     * @param array $data
     */
    protected function send_msg($data=[]){
        $topic = fun('Content@info',$data['aid'],$data['sysid'],false);
        $mods = modules_config($data['sysid']);
        
        $pinfo = $data['pid'] ? getArray($this->model->get($data['pid'])) : [];
        //if( $this->webdb['comment_send_msg'] ){
            $content = $mods['name'].' 里的信息: 《' . $topic['title'] . '》刚刚 “'.$this->user['username'].'” 对此进行了评论,<a target="_blank" href="'.get_url(urls($mods['keywords'].'/content/show',['id'=>$data['aid']])).'">你可以点击查看详情</a>';
            $title = $this->user['username'].' 对你评论了!';            
            if($topic['uid']!=$this->user['uid']){
                //if($this->forbid_remind($topic['uid'])!==true){
                    send_msg($topic['uid'],$title,$content);
                    send_wx_msg($topic['uid'], '你发表的'.$content);
                //}
            }
            
            if($pinfo && $topic['uid']!=$pinfo['uid']){
                if($pinfo['uid']!=$this->user['uid']){                    
                    //if($this->forbid_remind($pinfo['uid'])!==true){
                        send_msg($pinfo['uid'],$title,$content);
                        send_wx_msg($pinfo['uid'], '你参与评论的'.$content);
                    //}
                }
            }
        //}
    }
    
    private function get_tag_config($name='',$pagename=''){
        static $tag_array = null;
        if($tag_array!=null){
            return $tag_array;
        }
        $tag_array = cache('qb_tag_'.$name);    //数据库参数配置文件
        if(empty($tag_array)){                             //数据库设定的模板优先
            $tag_array = LabelModel::get_tag_data_cfg($name , $pagename);
            //cache('qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
            //trim($tag_array['view_tpl']) && $view_tpl = $tag_array['view_tpl'];
        }
        return $tag_array;
    }
    
    /**
     * 获取模板
     * @param string $name 标签名
     * @param string $pagename 模板路径
     * @return string|\app\index\model\NULL
     */
    private function get_tpl($name='',$pagename=''){
        $page_tpl = cache('tags_comment_tpl_'.$pagename);  //模板缓存
        if(!empty($page_tpl)){
            $view_tpl = $page_tpl[$name];
        }
        
        $tag_array = self::get_tag_config($name,$pagename);    //数据库参数配置文件
        trim($tag_array['view_tpl']) && $view_tpl = $tag_array['view_tpl'];
        
        if(self::$get_children!==null){
            $string = stristr($view_tpl,'<?php if(is_array($rs[\'children\'])'); //变量名必须是 $rs['children']
            $num =  stripos($string,'<?php endforeach; endif; else: echo "" ;endif; ?>');
            $view_tpl = substr($string,0,$num).'<?php endforeach; endif; else: echo "" ;endif; ?>';
        }else{
            //截取循环那段模板，其它不需要
            $string = stristr($view_tpl,'<?php if(is_array($listdb)');  //变量名必须是 $listdb
            $num =  strripos($string,'<?php endforeach; endif; else: echo "" ;endif; ?>');
            $view_tpl = substr($string,0,$num).'<?php endforeach; endif; else: echo "" ;endif; ?>';
        }
        return $view_tpl;
    }
    
    /**
     * 取JSON数据
     * @param string $name
     * @param string $page
     * @param string $pagename
     * @param number $sysid
     * @param number $aid
     * @param number $rows
     * @param string $order
     * @param string $by
     * @param string $status
     * @param string $data_type
     * @return void|\think\response\Json
     */
    private function ajax_content($name='',$page='',$pagename='',$sysid=0, $aid=0 ,$rows=0,$order='',$by='',$status='',$data_type=''){
        
        //对应fetch方法,传入一些常用的参数
        $admin = $this->admin;
        $userdb = $this->user;
        $timestamp = $this->timestamp;
        $webdb = $this->webdb;
        
        //$tag_array = self::get_tag_config($name,$pagename);
        $info = fun('content@info',$aid,$sysid);    //主题信息
        $id = $aid;
        
        $view_tpl = self::get_tpl($name,$pagename);        
        
        if(empty($view_tpl)){
            return $this->err_js('not_tpl');
            //die('tpl not exists !');
        }
        
        $data_list = $this->get_list($sysid,$aid,$rows,$status,$order,$by,$page);
        $array = getArray($data_list);
        $listdb = $array['data'];
        
        if(empty($listdb)){
            //die('null');
            $content = '';
        }else{
            if(self::$get_children!==null){
                $rs['children'] = $listdb;
            }
            @ob_end_clean();ob_start();
            eval('?>'.$view_tpl);
            $content = ob_get_contents();
            ob_end_clean();
        }
        
        $array['data'] = $content;
        return $this->ok_js($array);
    }
    
    /**
     * AJAX获取分页数据
     * @param string $name 标签名
     * @param string $page 第几页
     * @param string $pagename 模板文件名
     * @param number $sysid 频道系统ID
     * @param number $aid 内容ID
     * @param number $rows 每页取几条
     * @param string $order 按什么排序
     * @param string $by 升序还是降序
     * @param string $status 是否审核
     */
    public function ajax_get($name='',$page='',$pagename='',$sysid=0, $aid=0 ,$rows=0,$order='',$by='',$status=''){
        $_array = input();
        $data_type = $_array['data_type'];
        $content = self::ajax_content($name,$page,$pagename,$sysid, $aid,$rows,$order,$by,$status,$data_type);
        return  $content;
    }
    
    
    
    /**
     * 被各频道调用评论数据
     * @param number $sysid 频道模块的ID
     * @param number $aid 频道内容的ID
     * @param number $page 显示第几页
     * @param number $rows 每页显示几条
     * @param number $status 设置为1的时候代表只取已审的，为0显示所有
     * @param string $order 按什么排序
     * @param string $by 升序还是降序
     * @return unknown
     */
    public function get_list($sysid=0,$aid=0,$rows=0,$status=0,$order='',$by='',$page=0){
        
        if(self::$get_children!==null){  //取引用回复
            $map = [
                    'pid'=>self::$get_children,
            ];
        }else{
            $map = [
                    'aid'=>$aid,
                    'sysid'=>$sysid,
                    'pid'=>0,
            ];
        }
        
        if($status==1){
            $map['status']=1;
        }
        if(!in_array($order, ['id','list','create_time','agree','reply'])){
            if(self::$get_children!==null){
                $order = 'id asc';  //引用回复要按时间早的在前面
                $rows = 100; //引用回复全部读出来
            }else{
                $order = 'list desc,id desc';   //普通回复的话,时间晚的在前面
            }
        }
        $rows = intval($rows);
        if($rows<1){
            $rows=10;
        }
        $page = intval($page);
        if ($page<1) {
            $page=1;
        }
        $min = ($page-1)*$rows;
        $listdb = contentModel::where($map)->order($order)->paginate($rows);
        if(!is_object($listdb)){
            return $listdb;
        }
        $listdb->each(function($rs,$key){
            $rs['time'] = format_time(strtotime($rs['create_time']),true);
            $rs['username'] = get_user_name($rs['uid']);
            $rs['icon'] = get_user_icon($rs['uid']);            
            if($rs['reply']){
                $_children = contentModel::where('pid',$rs['id'])->column(true);
                foreach ($_children AS $k=>$v){
                    $_children[$k]['username'] = get_user_name($v['uid']);
                }
                $rs['children'] = $_children;
            }
            return $rs;
        });
        return $listdb;
    }
    

	
    public function delete($ids){
        if (empty($ids)) {
            return $this->err_js('ID有误');
        }
        $ids = is_array($ids)?$ids:[$ids];
        foreach ($ids AS $id){
            $info = contentModel::get($id);
            if($info['uid']!=$this->user['uid'] && !$this->admin){
                return $this->err_js('你没权限');
            }
            if(contentModel::where('pid',$id)->count('id')>0){
                contentModel::where('pid',$id)->update(['pid'=>0]);
            }
        }
        if (contentModel::destroy($ids)) {
            return $this->ok_js();
        } else {
            return $this->err_js('删除失败');
        }
    }
	
}
