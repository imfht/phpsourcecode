<?php
namespace app\common\fun;
use think\Db;
use app\qun\model\Content AS ContentModel;
use app\qun\model\Topic AS TopicModel;
use app\qun\model\Member AS MemberModel;
use app\qun\model\Moneylog AS MoneylogModel;

/**
 * 圈子
 *
 */
class Qun{
    
    /**
     * 圈币或圈豆消费与赚取日志
     * @param number $aid 圈子ID
     * @param number $uid 用户UID
     * @param number $money 圈币或圈豆,正数是加,负数是减
     * @param string $about 产生原因说明
     * @param number $type 1是圈币,2是圈豆
     */
    public static function money($aid=0,$uid=0,$money=0,$about='',$type=1){
        if ($money==0||$uid==0||$aid==0) {
            return ;
        }
        
        $result = MoneylogModel::create([
            'uid'=>$uid,
            'aid'=>$aid,
            'money'=>$money,
            'about'=>$about,
            'type'=>$type,
        ]);
        
        if ($result) {
            if ($money>0) {
                $result = MemberModel::where('uid',$uid)->where('aid',$aid)->setInc($type==1?'money':'dou',$money);
            }else{
                $result = MemberModel::where('uid',$uid)->where('aid',$aid)->setDec($type==1?'money':'dou',abs($money));
            }
            cache('user_'.$uid,null);
            if ($result) {                
                return true;
            }else{
                return '你还没加入当前圈子!';
            }            
        }
    }
    
    /**
     * 圈子的一些活跃信息,比如直播信息
     * @param number $aid
     * @param string $tag
     * @param array $array
     * @return unknown|array|string|unknown
     */
    public static function live($aid=0,$tag='',$array=[],$validity_time=0){
        static $data = null;
        if ($data===null) {
            $data = json_decode(substr(file_get_contents(RUNTIME_PATH.'qun_live.php'),14),true)?:[];
        }        
        $need_write = false;
        if ($validity_time>0 && $validity_time<time()) {
            $validity_time = time()+$validity_time;
        }
        if($array===''){
            unset($data[$aid][$tag]);
            if (empty($data[$aid])) {
                unset($data[$aid]);
            }
            $need_write = true;
        }elseif(is_array($tag)){
            $tag['validity_time'] = $validity_time;
            $data[$aid] = $tag;
            $need_write = true;
        }elseif($array){
            $data[$aid][$tag] = $array;
            $data[$aid][$tag]['validity_time'] = $validity_time;
            $data[$aid][$tag]['time'] = time();
            $need_write = true;
        }
        foreach($data AS $key=>$rs){
            if ($rs['validity_time']>0 && $rs['validity_time']<time()) {
                unset($data[$key]);
            }
        }
        if ($need_write) {
            file_put_contents(RUNTIME_PATH.'qun_live.php', '<?php die();?>'.json_encode($data));
        }elseif($tag){
            return $data[$aid][$tag];
        }elseif($aid){
            return $data[$aid];
        }else{
            return $data;
        }
    }
    
    /**
     * 获取圈子自定义的菜单
     * @param number $id 圈子ID
     * @param number $type 1是底部菜单 2是头部菜单 3是浮动按钮菜单
     * @return unknown
     */
    public static function menu($id=0,$type=3){
        if (!modules_config('qun')) {
            return [];
        }
        $tag = 'qun_menu_'.$type.'_'.$id;
        $menu = cache($tag);
        if (empty($menu)) {
            $menu = model('qun/menu')->getTreeList(['aid'=>$id,'ifshow'=>1,'type'=>$type]);
            cache($tag,$menu);
        }
        return $menu;
    }
    
    /**
     * 自定义条件查找圈子成员
     * @param array $map
     * @return array|unknown
     */
    public static function list_member($map=[]){
        if (!modules_config('qun')) {
            return [];
        }
        return MemberModel::where($map)->column(true);
    }
    
    /**
     * 获取我加入过的圈子个人信息,此方法将弃用,因为用户信息那里有缓存了.
     * @param number $uid 当前用户UID
     * @param number $qun_id 是否指定查询某个圈子
     * @return void|number|number[]|array[]|NULL[][]|unknown[]
     */
    public static function get_my_group($uid=0,$qun_id=0){
        if (!modules_config('qun')) {
            return ;
        }
        $map = [
            'uid'=>$uid,
        ];
        if ($qun_id){
            $map['aid'] = $qun_id;
            $info = getArray(MemberModel::where($map)->find());
            if ($info['end_time']>0 && $info['end_time']<time() && $info['type']==4) {
                $info['type']=1;
                MemberModel::where('id',$info['id'])->update(['type'=>1]);
                cache('user_'.$uid,null);
            }
            return $info;
        }else{
            $data = [];
            $listdb = MemberModel::where($map)->order('type','desc')->column(true);
            foreach($listdb AS $info){
                if ($info['end_time']>0 && $info['end_time']<time() && $info['type']==4) {
                    $info['type']=1;
                    MemberModel::where('id',$info['id'])->update(['type'=>1]);
                    cache('user_'.$uid,null);
                }
                if ($data[$info['aid']]) {
                    MemberModel::where('id',$info['id'])->delete();
                }else{
                    $data[$info['aid']] = $info;
                }                
            }
            return $data;
        }
    }
    
    /**
     * 查找某个主题被多少个圈子调整用过.
     * @param number $ext_id 主题ID
     * @param string $ext_sys 主题所在频道ID或目录
     * @return array|unknown
     */
    public static function get_aids_byid($ext_id=0,$ext_sys=''){
        if (!modules_config('qun')) {
            return [];
        }
        $sys = is_numeric($ext_sys)?$ext_sys:modules_config($ext_sys)['id'];
        $map = [
            'ext_sys'=>$sys,
            'ext_id'=>$ext_id,
        ];
        return TopicModel::where($map)->column('aid');
    }
    
    /**
     * 获取用户所在圈子里边的角色ID值 注意:设置$field='type'时,返回值 ===null  代表还没加入圈子 ==0 非正式成员,
     * @param number $id 圈子ID
     * @param number $uid 用户UID
     * @param string $field 获取哪个字段,留空则是所有字段
     * @return void|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function get_user_group($id=0,$uid=0,$field='type'){
        if (!modules_config('qun')) {
            return [];
        }
        if (empty($id)) {
            return ;
        }
        if (empty($uid)) {
            $uid = login_user('uid');
        }
        if (empty($uid)) {
            return ;
        }
        $info = Db::name('qun_member')->where('uid',$uid)->where('aid',$id)->find();
        if (empty($info)) {
            return ;
        }
        if ($info['end_time'] && $info['end_time']<time()) {  //设置了有效期
            $info['type'] = $info['type']>1 ? 1 : 0 ;
        }
        if ($field){
            return $info[$field];
        }else{
            return $info;
        }
    }
    
    /**
     * 根据关键字获取模型的ID值
     * @param string $keywrod 关键字比如 qz hy
     * @return string
     */
    public static function getid_bykey($keywrod=''){
        if (!modules_config('qun')) {
            return [];
        }
        if(!is_numeric($keywrod)){  //不是模型ID,而是关键字的情况,比如是qz hy之类的
            foreach(model_config(null,'qun') AS $rs){
                if($keywrod==$rs['keyword']){
                    $keywrod = $rs['id'];
                    break;
                }
            }
        }
        return intval($keywrod);
    }
    

    /**
     * 获取群的角色名称
     * @param number|string $groupid 可以是当前圈子会员组的ID,此时就得到相应的名称,也可以是name将获取当前用户组的所有名称
     * @param number|array $qid 可以指定圈子的ID或者是圈子信息
     * @param number $mid 可以指定圈子的模型ID或者是关键字,否则就是系统默认的 第二个参数存在的话,这个参数其实可以不填的
     * @return number|string|unknown|array|number[][]|string[][]|unknown[][]|array[][]
     */
    public static function get_group($groupid=null,$qid=0,$mid=0){
        if (!modules_config('qun')) {
            return [];
        }
        $array = $name_array = [];
        $i = 0;
        if ($qid) {
            if (is_array($qid)) {
                $info = $qid;
            }else{
                $info = self::getByid($qid);
            }
            if ($info['qun_groups']) {
                $name_array = json_decode($info['qun_groups'],true);
            }
            if (!$mid) {
                $mid = $info['mid'];
            }
        }
        if (empty($name_array)) {
            if($mid){
                if(!is_numeric($mid)){  //不是模型ID,而是关键字的情况,比如是qz hy之类的
                    $mid = self::getid_bykey($mid);
                }
                $webdb = model_config($mid,'qun');
            }
            if(empty($webdb['qun_groups'])){
                $webdb = config('webdb.M__qun');
            }
            $name_array = explode("\n", str_replace("\r", '', $webdb['qun_groups']));
        }
        
        foreach($name_array AS $value){
            if (empty($value)) {
                continue;
            }
            list($name,$sysgid) = explode("|", $value);
            $i++;
            $admin = 0; //是否有管理权限
            
            //$gid = 1,2,3是保留数字，分别是正式成员、副管理员、管理员
            if ($i==1) {    //管理员
                $gid = 3;
                $admin = 1;
            }elseif($i==2){ //副管理员
                $gid = 2;
                $admin = 1;
            }elseif($i==3){ //正式成员
                $gid = 1;
            }else{
                $gid = $i;
            }
            $array[$gid] = [
                'gid'=>$gid,
                'name'=>$name,
                'sysgid'=>$qid?null:$sysgid,  //关联了系统用户组,升级会用到。用户自定义用户组的话,就不给关联,安全考虑
                'admin'=>$admin,
            ];
        }
        $array[3] || $array[3] = [
            'gid'=>3,
            'name'=>QUN.'管理员',
            'admin'=>1,
        ];
        $array[2] || $array[2] = [
            'gid'=>2,
            'name'=>'副管理员',
            'admin'=>1,
        ];
        $array[1] || $array[1] = [
            'gid'=>1,
            'name'=>'正式成员',
            'admin'=>0,
        ];
        $array[0] = [
            'gid'=>0,
            'name'=>'待审成员',
        ];
        if (is_numeric($groupid)) {
            return $array[$groupid]['name'];
        }elseif($groupid=='name'){
            $data = [];
            foreach ($array AS $rs){
                $data[$rs['gid']] = $rs['name'];
            }
            return $data;
        }
        return $array;
    }
    
    /**
     * 统计某个圈子里的图片或商品或贴子的数量
     * @param string $table 统计的数据表,不用加前缀
     * @param number $id 圈子ID
     * @return number|string
     */
    public static function count($table='',$id=0){
        if (!modules_config('qun')) {
            return [];
        }
        if (preg_match('/^qb_/', $table)) {
            $table = str_replace('qb_', '', $table);
        }
        if (preg_match('/member$/', $table)) {
            $map = ['aid'=>$id];
        }else{
            $map = ['ext_id'=>$id];
        }
        return Db::name($table)->where($map)->count('id');
    }
    
    /**
     * 根据圈子ID获取圈子的信息
     * @param unknown $id
     * @param number $time 缓存时间
     * @return void|string|mixed
     */
    public static function getByid($id,$time=3600){
        if (!modules_config('qun')) {
            return [];
        }
        if (empty($id)) {
            return [];
        }
        static $array = [];
        $info = $array[$id];
        if ($info) {
            return $info;
        }
        $info = cache('qunById'.$id);
        if (empty($info)) {
            $info = ContentModel::getInfoByid($id);
            if (empty($info)) {
                return ;
            }
            $info['url'] = iurl("qun/content/show",['id'=>$info['id']]);
            $info['picurl'] = tempdir($info['picurl']);
            unset($info['sncode']);
            //$array[$id] = $info;
            cache('qunById'.$id,$info,$time);
        }
        $array[$id] = $info;
        return $info;
    }
    
    /**
     * 获取圈币名称
     * @param number|array $id 可以是圈子数据,也可以是圈子ID
     * @return string
     */
    public static function moneyname($id){
        if (is_numeric($id)) {
            $info = self::getByid($id);
        }else{
            $info = $id;
        }
        return $info['moneyname']?:'圈币';
    }
    
    /**
     * 获取圈豆名称
     * @param number|array $id 可以是圈子数据,也可以是圈子ID
     * @return string
     */
    public static function douname($id){
        if (is_numeric($id)) {
            $info = self::getByid($id);
        }else{
            $info = $id;
        }
        return $info['douname']?:'圈豆';
    }
    
    /**
     * 某用户加入过的圈子
     * @param number $uid 用户UID
     * @param number $mid 指模型ID 或关键字
     * string $only_title 设置为1或true的话,只取标题
     * @return array|array|mixed
     */
    public static function myjoin($uid=0,$mid=0,$only_title=false){
        if (!modules_config('qun')) {
            return [];
        }
        $uid || $uid = login_user('uid');
		if(empty($uid)){
			return [];
		}
		//$listdb = Db::name('qun_member')->alias('A')->join('qun_content1 B','A.aid=B.id','left')->field('B.*')->where('A.uid='.$uid)->order('A.id desc')->select();
		$array = Db::name('qun_member')->where('uid',$uid)->where('type','>',0)->order('type desc,update_time desc')->column('aid');
		$listdb = [];
		foreach($array AS $aid){
		    //$info = ContentModel::getInfoByid($aid);
		    $info = self::getByid($aid);
		    //unset($info['sncode']);
		    if($mid){
		        if(is_numeric($mid) && $info['mid']!=$mid){
		            continue;
		        }elseif(!is_numeric($mid)){
		            $cfg = model_config($info['mid'],'qun');
		            if($cfg['keyword']!=$mid){
		                continue;
		            }
		        }
		    }
		    if($only_title){
		        $listdb[$aid] = $info['title'];
		    }else{
		        $listdb[] = $info;
		    }		    
		}
        return $listdb;
    }
    
    /**
     * 列出某个圈子的所有管理员,包括圈主与副管理员
     * @param number $aid
     * @return mixed|array
     */
    public static function admin($aid=0){
        static $data=[];
        if (!$aid) {
            return [];
        }
        if ($data[$aid]) {
            return $data[$aid];
        }
        $data[$aid] = Db::name('qun_member')->where('aid',$aid)->where('type','in',[2,3])->order('type desc,update_time desc')->column('uid');
        return $data[$aid];
    }
    
    /**
     * 某用户最近访问过的圈子
     * @param number $uid
     * @return array|array|mixed
     */
    public static function myvisit($uid=0){
        if (!modules_config('qun')) {
            return [];
        }
        $uid || $uid = login_user('uid');
        if(empty($uid)){
            return [];
        }
        //$listdb = Db::name('qun_visit')->alias('A')->join('qun_content1 B','A.aid=B.id','left')->field('B.*')->where('A.uid='.$uid)->order('A.id desc')->select();
        $array = Db::name('qun_visit')->where('uid',$uid)->order('visittime desc')->column('aid');
        $listdb = [];
        foreach($array AS $aid){
            $info = ContentModel::getInfoByid($aid);
            unset($info['sncode']);
            $listdb[] = $info;
        }
        return $listdb;
    }
    
    /**
     * 某用户所创建的所有圈子
     * @param number $uid
     * @param number $aid 指模型ID 或关键字
     * @return array|array|mixed
     */
    public static function getByuid($uid=0,$aid=0){
        if (!modules_config('qun')) {
            return [];
        }
        if (empty($uid)) {
            return [];
        }
        static $array = [];
        $listdb = $array[$uid];
        if (empty($listdb)) {
            $array = Db::name('qun_content')->where('uid',$uid)->order('id desc')->column(true);
            $listdb = [];
            foreach($array AS $rs){
                if($aid){
                    if(is_numeric($aid) && $rs['mid']!=$aid){
                        continue;
                    }elseif(!is_numeric($aid)){
                        $cfg = model_config($rs['mid'],'qun');
                        if($cfg['keyword']!=$aid){
                            continue;
                        }
                    }
                }
                $info = ContentModel::getInfoByid($rs['id']);
                unset($info['sncode']);
                $listdb[] = $info;
            }
            $array[$uid] = $listdb;
        }        
        return $listdb;
    }
    
    /**
     * 获取某个圈子的广告位信息
     * @param number $id
     */
    public static function adsetByid($id=0){
        if (!modules_config('qun')) {
            return [];
        }
        if (empty($id)) {
            return ;
        }
        return Db::name('qun_adset')->where('aid',$id)->find();
    }
    
    /**
     * 获取某个圈子的广告位状态,什么时候可以轮流到可以显示广告
     * @param number $id
     * @return void|array|\think\db\false|PDOStatement|string|\think\Model
     */
    public static function adset_status($id=0){
        if (!modules_config('qun')) {
            return [];
        }
        if (empty($id)) {
            return ;
        }
        $info = Db::name('qun_adset')->where('aid',$id)->find();
        if (empty($info)) {
            return ;
        }
        $time = time();
        $end_time = Db::name('qun_aduser')->where('aid',$id)->order('id','desc')->value('end_time');
        if($end_time<$time){
            $end_time = $time;
        }
        $info['time'] = $end_time;
        return $info;
    }
    
    /**
     * 获取广告位内容
     * @param number $id
     * @return void|array[]|\think\db\false[]|PDOStatement[]|string[]|\think\Model[]
     */
    public static function adByid($id=0){
        if (!modules_config('qun')) {
            return [];
        }
        if (empty($id)) {
            return ;
        }
        $info = Db::name('qun_adset')->where('aid',$id)->find();
        if (empty($info)) { //不存在广告位
            return ;
        }
        $time = time();
        $data = Db::name('qun_aduser')->where('aid',$id)->where('begin_time','<',$time)->where('status',1)->where('end_time','>',$time)->find();
        if($info['status']==0 && empty($data)){ //关闭了广告位购买并且没有可以显示的广告,否则的话,还是要把别人的广告显示完才行的.
            return ;
        }
        return [
                'set'=>$info,
                'ad'=>$data,
        ];
    }
    
    /**
     * 列出所有风格
     * @param string $olny_free 设置为true的话,只列出免费风格 否则是所有风格
     * @param string $type 指定类型
     * @return string[]
     */
    public static function list_style($olny_free=false,$type=''){
        $array = [];
        $template_path = TEMPLATE_PATH."qun_style/";
        $dir=opendir($template_path);
        while( $file=readdir($dir)){
            if($file!='.' && $file!='..' && $file!='.svn' && is_file($template_path.$file.'/info.php')){
                $rs = include($template_path.$file.'/info.php');
                if ($olny_free==true && $rs['money']>0) {
                    continue;
                }
                if($type!='' && $rs['type']!='' && $rs['type']!=$type){
                    continue;
                }
                $rs['keyword'] = $file;
                $rs['picurl'] = config('view_replace_str.__STATIC__').'/qun_style/'.$file.'/demo_min.jpg';
                $rs['demo'] = config('view_replace_str.__STATIC__').'/qun_style/'.$file.'/demo.jpg';
                $array[] = $rs;
            }
        }
        return $array;
    }
    
}