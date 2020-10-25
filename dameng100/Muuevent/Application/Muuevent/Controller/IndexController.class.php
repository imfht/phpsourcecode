<?php
namespace Muuevent\Controller;
use Common\Controller\CommonController;

class IndexController extends CommonController
{
    protected $muueventModel;
    protected $muueventTypeModel;

    public function _initialize()
    {
        //基础公共控制器
        parent::_initialize();
        $this->muueventModel = D('Muuevent/Muuevent');
        $this->muueventTypeModel = D('Muuevent/MuueventType');

        $topType = D('MuueventType')->getTypeListByPid(0);
        $this->assign('top_type', $topType);

        $custom_nav = array(
            array(
                'title'=>'发布活动',
                'url'=>U('Index/edit'),
                'icon'=>'<i class="icon icon-edit"></i>'
            ),
            array(
                'title'=>'我的活动',
                'url'=>U('My/index'),
                'icon'=>'<i class="icon icon-list-alt"></i>'
            )
        );
        
        $this->assign('custom_nav',$custom_nav);

    }

    /**
     * 活动首页
     */
    public function index($page = 1,$r = 20)
    {
        $map['status'] = 1;
        $order = 'create_time desc';
        $content = $this->muueventModel->where($map)->order($order)->page($page, $r)->select();

        $totalCount = $this->muueventModel->where($map)->count();
        foreach ($content as &$v) {
            $v['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->muueventTypeModel->getType($v['type_id']);
            $v['province'] = D('district')->where(array('id' => $v['province']))->getField('name');
            $v['city'] = D('district')->where(array('id' => $v['city']))->getField('name');
            $v['district'] = D('district')->where(array('id' => $v['district']))->getField('name');
        }
        unset($v);
        $this->assign('type_id', $type_id);
        $this->assign('contents', $content);
        $this->assign('norh', $norh);
        $this->assign('totalPageCount', $totalCount);
        $this->display();
    }

    public function lists($page = 1,$r=20){
        $type = I('type',0,'intval');
        $pid = I('pid',0,'intval');
        $keyword = I('keyword','','text');

        if($keyword){
            $map['title'] = array('like', '%' . $keyword . '%','or');
            $map['content'] = array('like', '%' . $keyword . '%','or');
        }
        if($pid){
            //获取同级子分类
            $son_type = $this->muueventTypeModel->getTypeListByPid($pid);
            $this->assign('son_type',$son_type);
            //获取子类型所有活动条件
            $types=$this->muueventTypeModel->getTypeList(array('pid'=>$pid));
            if(count($types)){
                $types=array_column($types,'id');
                $types=array_merge(array($type),$types);
                $map['type_id']=array('in',$types);
            }else{
                $map['type_id'] = $pid;
            }
        }
        if($type){
            $map['type_id'] = $type;
        }

        $hot_city = C('HOT_CITY');
        $this->assign('city',$hot_city);

        //时段查询条件
        $d = I('d','','text');
        if($d=='today'){
            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            $map['sTime'] = array(array('gt',$beginToday),array('lt',$endToday));
        }
        if($d=='tomorrow'){
            $beginTom=mktime(0,0,0,date('m'),date('d')+1,date('Y'));
            $endTom=mktime(0,0,0,date('m'),date('d')+2,date('Y'))-1;
            $map['sTime'] = array(array('gt',$beginTom),array('lt',$endTom));
        }
        if($d=='week'){
            $beginWeek = strtotime(date('Y-m-d', strtotime("this week Monday", time())));
            $endWeek = strtotime(date('Y-m-d', strtotime("this week Sunday", time()))) + 24 * 3600 - 1;
            $map['sTime'] = array(array('gt',$beginWeek),array('lt',$endWeek));
        }
        if($d=='wkend'){
            $beginWeekend = strtotime(date('Y-m-d', strtotime("this week Saturday", time())));
            $endWeekend = strtotime(date('Y-m-d', strtotime("this week Sunday", time()))) + 24 * 3600 - 1;
            $map['sTime'] = array(array('gt',$beginWeekend),array('lt',$endWeekend));
        }
        if($d=='month'){
            $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
            $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
            $map['sTime'] = array(array('gt',$beginThismonth),array('lt',$endThismonth));

        }
        //获取城市查询条件
        $city = I('city',0,'intval');
        if($city){
            $map['_query'] = 'province='.$city.'&city='.$city.'&_logic=or';
        }
        //状态查询条件
        $zt = I('zt','','text');
        if($zt=='enr'){
            $map['sTime'] = array('gt',time());
        }
        if($zt=='ing'){
            //$map['sTime'] = array('gt',time());
            $map['_string'] = 'sTime<'.time().' AND eTime>'.time();
        }
        if($zt=='end'){
            $map['eTime'] = array('lt',time());
        }
        //获取列表
        $map['status'] = 1;
        $order = 'create_time desc';
        $norh == 'hot' && $order = 'signCount desc';
        $content = $this->muueventModel->where($map)->order($order)->page($page, 10)->select();
        //echo M()->getLastSql();

        $totalCount = $this->muueventModel->where($map)->count();
        foreach ($content as &$v) {
            $v['user'] = query_user(array('id','nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->muueventTypeModel->getType($v['type_id']);

            $v['province'] = D('district')->where(array('id' => $v['province']))->getField('name');
            $v['city'] = D('district')->where(array('id' => $v['city']))->getField('name');
            $v['district'] = D('district')->where(array('id' => $v['district']))->getField('name');

            if($v['city']=='市辖区' || $v['city']==$v['province']){
                $v['city']='';
            }
        }
        unset($v);
        $this->assign('keyword', $keyword);
        $this->assign('type_id', $type_id);
        $this->assign('contents', $content);
        $this->assign('totalPageCount', $totalCount);
        $this->display();
    }

    
    /**
     * 编辑活动
     */
    public function edit()
    {
        $id=I('id',0,'intval');
        if(IS_POST){
            $data['id'] = $id;
            $data['uid'] = is_login();
            $data['title'] = I('post.title','','text');
            $data['explain'] = I('post.explain','','html');
            $data['description'] = I('post.description','','text');
            $startDate = I('post.startDate','','text');
            $startTime = I('post.startTime','','text');
            $endDate = I('post.endDate','','text');
            $endTime = I('post.endTime','','text');
            $data['province'] = I('post.province',0,'intval');
            $data['city'] = I('post.city',0,'intval');
            $data['district'] = I('post.district',0,'intval');
            $data['address'] = I('post.address','','text');
            $data['limitCount'] = I('post.limitCount',0,'intval');
            $data['cover_id'] = I('post.cover_id',0,'intval');
            $data['type_id'] = I('post.type_id',0,'intval');
            $data['point'] = I('post.point','','text');
            //将时间组合转时间戳
            $data['sTime'] = strtotime($startDate.' '.$startTime); 
            $data['eTime'] = strtotime($endDate.' '.$endTime); 

            $this->doPost($data);

        }else{
            $this->_needLogin();

            $title=$id?"编辑":"发布";
            if($id){
                $event_content = $this->muueventModel->where(array('status' => 1, 'id' => $id))->find();
                if (!$event_content) {
                    $this->error('404 not found');
                }
                $event_content['sDate_format'] = date("Y-m-d",$event_content['sTime']);
                $event_content['sTime_format'] = date("H:i",$event_content['sTime']);

                $event_content['eDate_format'] = date("Y-m-d",$event_content['eTime']);
                $event_content['eTime_format'] = date("H:i",$event_content['eTime']);
                //dump($event_content);
                $this->assign('content', $event_content);
            }
            $tree = D('Muuevent/MuueventType')->getTree();
            $this->assign('tree',$tree);
            $this->setTitle($title.'活动' . '—活动');
            $this->display();
        }
    }

    /**
    * 发布、编辑活动数据处理
    */
    private function doPost($data)
    {
        $this->_needLogin();

        if ($data['sTime'] > $data['eTime']) {
            $this->error('活动开始时间不能大于活动结束时间');
        }
        //dump($data);exit;
        if($content = $this->muueventModel->create($data)){

            if ($data['id']) {

                $rs = $this->muueventModel->save($data);

                if ($rs) {
                    $this->success('编辑活动成功。', U('detail', array('id' => $content['id'])));
                } else {
                    $this->error('编辑失败。', '');
                }
            } else {
                
                if (modC('MUUEVENT_CONFIG_NEED_VERIFY', 0,'Muuevent') && !is_administrator()) //需要审核且不是管理员
                {
                    $data['status'] = 0;
                    $tip = '但需管理员审核通过后才会显示在列表中，请耐心等待。';
                    //$user = query_user(array('username', 'nickname'), is_login());
                    //D('Common/Message')->sendMessage(C('USER_ADMINISTRATOR'), $title = '活动发布提醒', "{$user['nickname']}发布了一个活动，请到后台审核。",  'Admin/Muuevent/verify', array(),is_login(), 2);
                }

                $rs = $this->muueventModel->add($content);

                if ($rs) {
                    $this->success('发布活动成功。' . $tip, U('detail', array('id' => $rs)));
                } else {
                    $this->error('发布失败。' . $this->muueventModel->getError());
                }
            }
        }else{
            $this->error('数据错误。' . $this->muueventModel->getError());
        }
    }

    /**
     * 活动详情
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function detail($id = 0)
    {
        //判断用户是否已报名该活动
        $check_isSign = D('MuueventAttend')->where(array('uid' => is_login(), 'event_id' => $id))->select();
        $this->assign('check_isSign', $check_isSign);
        //获取活动信息
        $event_content = $this->muueventModel->where(array('status' => 1, 'id' => $id))->find();
        if (!$event_content) {
            $this->error('404 not found');
        }
        $event_content['province'] = D('district')->where(array('id' => $event_content['province']))->getField('name');
        $event_content['city'] = D('district')->where(array('id' => $event_content['city']))->getField('name');
        $event_content['district'] = D('district')->where(array('id' => $event_content['district']))->getField('name');
        if($event_content['city']=='市辖区' || $event_content['city']==$event_content['province']){
            $event_content['city']='';
        }
        //浏览量增加
        $this->muueventModel->where(array('id' => $id))->setInc('view_count');
        //获取发布者信息
        $event_content['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar64', 'rank_html', 'signature'), $event_content['uid']);
        //获取活动分类信息
        $event_content['type'] = $this->muueventTypeModel->getType($event_content['type_id']);
        //获取已报名人数
        $event_content['attend_count'] = D('MuueventAttend')->where(array('event_id' => $id))->count();
        
        $this->assign('content', $event_content);
        $this->assign('member',$member);
        $this->setTitle('{$content.title}' . '—活动');
        $this->setKeywords('{$content.title}' . ',活动');
        $this->getRecommend();
        $this->display();
    }


    /**
     * 报名参加活动
     */
    public function doSign($event_id)
    {
        $this->_needLogin();
        if(IS_POST){

            $name = I('post.name','','text');
            $phone = I('post.phone','','text');

            if (!$event_id) {
                $this->error('参数错误');
            }
            if (trim(($name)) == '') {
                $this->error('请输入姓名。');
            }
            if (trim($phone) == '') {
                $this->error('请输入手机号码。');
            }
            $check = D('MuueventAttend')->where(array('uid' => is_login(), 'event_id' => $event_id))->select();
            if ($check) {
                $this->error('您已经报过名了。');
            }
            $event_content = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->find();
            
            if (!$event_content) {
                $this->error('活动不存在！');
            }
            if ($event_content['attentionCount'] + 1 > $event_content['limitCount']) {
                $this->error('超过限制人数!');
            }

            $data['uid'] = is_login();
            $data['event_id'] = $event_id;
            $data['name'] = $name;
            $data['phone'] = $phone;
            $data['create_time'] = time();
            $res = D('MuueventAttend')->add($data);
            if ($res) {

                D('Message')->sendMessageWithoutCheckSelf($event_content['uid'], '报名通知',query_user('nickname', is_login()) . '报名参加了活动]' . $event_content['title'] ,  'Muuevent/Index/member', array('id' => $event_id));

                $this->muueventModel->where(array('id' => $event_id))->setInc('signCount');
                
                $this->success('报名成功。', 'refresh');
            } else {
                $this->error('报名失败。', '');
            }
            

        }else{
            $event_content = $this->muueventModel->where(array('status' => 1, 'id' => $event_id))->find();
            if (!$event_content) {
                $this->error('活动不存在！');
            }
            $this->assign('content',$event_content);

            $this->display(); 
        }
        
    }

    /**
     * 获取推荐活动数据
     */
    public function getRecommend()
    {
        $rec_event = $this->muueventModel->where(array('is_recommend' => 1))->limit(2)->order('rand()')->select();
        foreach ($rec_event as &$v) {
            $v['user'] = query_user(array('id', 'username', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            $v['type'] = $this->muueventTypeModel->getType($v['type_id']);
            $v['check_isSign'] = D('MuueventAttend')->where(array('uid' => is_login(), 'event_id' => $v['id']))->select();
        }
        unset($v);

        $this->assign('rec_event', $rec_event);
    }
    /**
     * 需要登录
     * @return [type] [description]
     */
    private function _needLogin()
    {   
        //调用通用用户授权方法
        if(!_need_login()){
            $this->error(L('_ERROR_NEED_LOGIN_'));
        }
    }
    /**
     * 获取Restful签名
     * @param  [type] $noce [description]
     * @return [type]       [description]
     */
    public function getSignature($noce)
    {   
        $arr['timestamp'] = time();
        $arr['noce'] = $noce;
        $arr['signature'] = $this->muueventModel->createSignature($arr['timestamp'],$arr['noce']);

        $return['data'] = $arr;
        $this->ajaxReturn($return);
    }

}