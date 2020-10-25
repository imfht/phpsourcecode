<?php


namespace People\Controller;

use Think\Controller;


class IndexController extends Controller
{
    /**
     * 业务逻辑都放在 WeiboApi 中
     * @var
     */
    public function _initialize()
    {
    }

    public function index($page = 1)
    {


        $peoples = D('Member')->where('status=1 and last_login_time!=0')->field('uid', 'reg_time', 'last_login_time')->order('last_login_time desc')->findPage(18);
        foreach ($peoples['data'] as &$v) {
            $v['user'] = query_user(array('avatar128', 'space_url', 'username', 'fans', 'following', 'signature', 'nickname'), $v['uid']);
        }
        unset($v);

        $this->assign('lists', $peoples);

        $this->display();
    }

    public function find($page = 1, $keywords = '')
    {
        $nickname = op_t($keywords);
        if ($nickname != '') {
            $map['nickname'] = array('like','%'.$nickname.'%');
        }
        $list = D('Member')->where($map)->findPage(18);
        foreach ($list['data'] as &$v) {
            $v['user'] = query_user(array('avatar128', 'space_url', 'username', 'fans', 'following', 'signature', 'nickname'), $v['uid']);
        }
        unset($v);
        $this->assign('lists', $list);
        $this->assign('nickname',$nickname);
        $this->display();
    }

    /**签到排行榜
     * @param int $page
     * @param int $limit
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function ranking($page = 1, $limit = 50)
    {
        $model = D('CheckInfo');
        $modelMember=D('Member');
        if(is_login()){
            //获取用户信息
            $user_info = query_user(array('uid', 'nickname', 'space_url', 'avatar64',), is_login());
            $check_info=$model->where(array('uid'=>is_login()))->find();

            if(!$check_info){
                $check_info['con_num']=0;
                $check_info['total_num']=0;
                $check_info['total_score']=0;
                $check_info['is_sign']=0;
            }else{
                if($check_info['ctime']>=get_some_day(0)){
                    $check_info['is_sign']=1;
                }else{
                    $check_info['is_sign']=0;
                }
            }
            $user_info=array_merge($user_info,$check_info);
            $ranking = $model->field('uid')->order('total_num desc,uid asc')->select();
            $ranking = array_column($ranking, 'uid');
            if(array_search(is_login(), $ranking)===false){
                $user_info['ranking'] = count($ranking) + 1;
            }else{
                $user_info['ranking'] = array_search(is_login(), $ranking) + 1;
            }
            $this->assign('user_info', $user_info);
            //获取用户信息end
        }
        $user_list=D('Member as m')->where(array('m.status'=>1))->field('m.uid,m.nickname,c.total_num,c.con_num,c.total_score,c.ctime')->page($page,$limit)->order('c.total_num desc,m.uid asc')->join('LEFT JOIN __CHECK_INFO__ as c ON c.uid=m.uid')->cache('ranking_list_'.$page,60)->select();
        $totalCount = $modelMember->count();
        $time = get_some_day(0);
        foreach ($user_list as $key => &$val) {
            $val['ranking'] = ($page - 1) * $limit + $key + 1;
            if ($val['ranking'] <= 3) {
                $val['ranking'] = '<span style="color:#EB7112;">' . $val['ranking'] . '</span>';
            }
            if(!$val['total_num']){
                $val['con_num']=$val['total_num']=$val['total_score']=0;
            }
            if ($val['ctime']&&$time <= $val['ctime']) {
                $val['status'] = '<span>已签到</span>';
            } else {
                $val['status'] = '<span style="color: #BDBDBD;">未签到</span>';
            }
        }
        unset($val, $key);
        $this->assign('user_list', $user_list);
        $this->assign('totalCount', $totalCount);
        $this->display();
    }
}