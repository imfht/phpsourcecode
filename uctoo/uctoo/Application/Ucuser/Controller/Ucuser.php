<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------
/**
 *	公众号会员管理后台控制器
 *  @version 1.0
 */
namespace app\ucuser\controller;

use  app\admin\controller\Admin;
use app\admin\Builder\AdminConfigBuilder;
use app\admin\Builder\AdminListBuilder;
use app\admin\Builder\AdminTreeListBuilder;
use app\common\Model\Ucuser as UcuserModel;
use com\TPWechat;
use com\ErrCode;
use app\common\Model\VerifyModel;


class Ucuser extends Admin
{

    protected $weObj;          //管理后台自动注入的wechat SDK实例,用于管理公众号，自定义微信会员卡、优惠券、运营人员与微会员互动等场景

    //TP5 的架构方法绑定（属性注入）的对象
    public function __construct(TPWechat $weObj)
    {
        $this->weObj = $weObj;
        parent::__construct();
    }

    public function index($page=1,$r=10)
    {
        $model = model('Ucuser');
		$show_type = input('show_type');

        if(empty($show_type)){
            $show_type = '';
        }

        $map['status'] = array('EGT', 0);
        $map['mp_id'] = get_mpid();
		empty(input('taglist')) || $taglist = input('taglist/a');
		if(!empty($taglist))
		{
			switch(count($taglist))
			{
				case 0:
					break;
				case 1:
					$map['tagid_list'] = $model->get_tag_id_map($taglist['0']);
					break;
				default:
					$map['tagid_list'] =  $model->get_tag_id_map($taglist);
			}
		}
		empty(input('name_remark')) || $name_remark = input('name_remark','','text');
		empty($name_remark) || $map['_complex'] = array('nickname'=>array('like','%'.$name_remark.'%'),'remark'=>array('like','%'.$name_remark.'%'),'_logic'=>'or');
		$list = db('Ucuser')->where($map)->page($page, $r)->order('mid asc')->select();
        //$list = $list->toArray();
//		var_dump(__file__.' line:'.__line__,$model->getLastSql());exit;
        $totalCount = $model->where($map)->count();
        //显示页面
        $builder = new AdminListBuilder();
		$builder
			->setIdsKey('mid')
			->title('用户列表')
			->setSelectPostUrl(url('/ucuser/Ucuser/index'))
			->select('查看模式：', 'show_type', 'select', '', '', '', array(array('id'=>'','value'=>'用户资料'),array('id'=>1,'value'=>'用户标签')))
			->setSearchPostUrl(url('/ucuser/Ucuser/index'))
			->search('称呼或备注','name_remark','text','','','','')
			->button('全部用户', array('href' => url('ucuser/Ucuser/index/',array('show_type'=>$show_type))))
			->keyText('mid', 'mid')
			->keyText('nickname', '昵称');
		//				$builder->search('称呼或备注','name_remark','select','','','',array(array('id'=>0,'value'=>'类型1'),array('id'=>1,'value'=>'类型2')));
		if(($taglist_select = $this->get_tag_list_to_select()))
		{
			$builder->search('标签筛选:','taglist','checkbox','','','',$taglist_select);
		}
		switch($show_type)
		{
			case '1':
				foreach($list as &$l)
				{
					$this->func_get_ucuser_tag($l);
				}

				$builder
					->buttonModalPopup(url('/ucuser/Ucuser/editUcuserTagLink/'),'','批量打标签（勾选后设置）',array('target-form'=>'ids'))
					->buttonModalPopup(url('/ucuser/Ucuser/deleteTagLnik/'),'','批量撤标签（筛选后设置）',array('target-form'=>'ids'))
					->button('同步标签列表', array('href' => url('ucuser/Ucuser/sycUcuserTag')))
					->keytext('remark','备注')
					->keytext('tagid_lists','他的标签')
					->keyDoActionModalPopup('/ucuser/Ucuser/editUcuserTagLink/ids/{mid}','打标签','',array('class'=>'btn btn-sm btn-info'))
					->keyDoActionModalPopup('/ucuser/Ucuser/updateUserRemark/ids/{mid}','修改备注','',array('class'=>'btn btn-sm btn-info'))
					;
				break;
			default :
				$builder
					->button('同步粉丝数据', array('href' => url('Ucuser/sycUcuser')))
					->button('获取粉丝信息', array('href' => url('Ucuser/sycUcuserInfo')))
					->button('强制刷新粉丝信息', array('href' => url('Ucuser/sycUcuserInfo',array('force'=>1))))
					->button('数据剔重', array('href' => url('Ucuser/delDup')))
					//			->keyText('openid', 'openid')
					->keyImage('headimgurl','头像',array('style'=>'width:90px;hight:90px;border-radius:10px;'))
					->keyMap('sex', '性别',array(0 => '未知', 1 => '<span class="btn btn-sm" ><i class="icon icon-venus" style="color:blue"></i>男性</span>', 2 => '<span class="btn btn-sm " ><i class="icon icon-mars" style="color:orange"></i>女性</span>'))
					->keyText('country','国家')
					->keyText('province','省份')
					->keyText('city','城市')
					->keyText('score1','积分');
				break;
		}

		return $builder
			->data($list)
			->pagination($totalCount, $r)
			->fetch();
    }

	/*
	 *	编辑用户的标签
	 */
	public function editUcuserTagLink()
	{
		$ids = input('ids/a','','intval');
		if(request()->isPost())
		{
			$taglink = input('taglink/a','','intval');
			is_numeric($ids) && $ids = array($ids);
			if(count($ids)==0)
			{
				$this->error('缺少mid');
			}
			$Ucuser = model('Ucuser');
			$UcTag = model("Ucuser/UcuserTag");
			if(count($taglink)>50)
			{
				$this->error('批量修改个数超过上限');
			}
			foreach($taglink as $tagid)
			{
				$Ucuser->startTrans();
				foreach ($ids as  &$mid )
				{
					$map = array('mid'=>$mid,'mp_id'=>get_mpid());
					$tagidlist = $Ucuser->where($map)->getfield('tagid_list');
					$tagidlist = empty($tagidlist)?array():json_decode($tagidlist,true);
					$tagidlist = array_merge($tagidlist,array($tagid));
					$tagidlist = array_unique($tagidlist);
					$tagidlist = array_values($tagidlist);
					if(count($tagidlist)>3)
					{
						$Ucuser->rollback();
						$this->error('修改失败，有用户超过3个标签');
					}
					empty($tagidlist) && $tagidlist =array();
					$ret= $Ucuser->where($map)->save(array('tagid_list'=>json_encode($tagidlist)));
					$ret && $UcTag->where(array('id'=>$tagid,'mp_id'=>get_mpid()))->setInc('count');
				}
				//todo 调用微信接口修改标签 失败则回滚
				$this->init_wx();
				$openid_list = $Ucuser->where(array('mid'=>array('in',$ids),'mp_id'=>get_mpid()))->getfield('openid',true);
				$ret = $this->weObj->batchtaggingTagsMembers($tagid,array($openid_list));
				if(!$ret)
				{
					$Ucuser->rollback();
					$this->error('批量打标签失败，错误：'.(ErrCode::getErrText($this->weObj->errCode)?ErrCode::getErrText($this->weObj->errCode):$this->weObj->errMsg));
				}
				$Ucuser->commit();
			}
			$this->success('修改成功');
		}
		else
		{
			if(	is_numeric($ids)
				|| (is_array($ids) && (count($ids)==1) && ($ids = $ids['0'])))
			{
				$map['mp_id'] = get_mpid();
				$map['mid'] = $ids;
				$Ucuser = D('Ucuser');
				$user_tagidlist = $Ucuser->where($map)->getField('tagid_list');
				$this->assign('user_tagidlist',json_decode($user_tagidlist,true));
			}
			$option['mp_id'] = get_mpid();
			$UcTag = D("Ucuser/UcuserTag");
			$ret = $UcTag->get_tag_list($option);
			$tagidlist = $ret['list'];
			foreach($tagidlist as $a)
			{
				if(in_array($a['id'],json_decode($user_tagidlist,true))|| ($a['id'] == 1)) continue;
				$inputs['checkbox'][] =array('key'=>'taglink','id'=>$a['id'],'type'=>'checkbox','name'=>$a['name'],'value'=>'false');
			}
			$this->assign('confirm_text','给用户打标签');
			$this->assign('inputs',$inputs);
			$this->assign('data',array('ids'=>$ids,''));
			$this->assign('posturl',U('ucuser/Ucuser/editUcuserTagLink'));
			$this->display('Public/popup');
		}
	}

	/*
	 *	删除某个用户的某个标签
	 */
	public function deleteTagLnik()
	{
		$mid = I('ids/a','','intval');
		is_numeric($mid) && $mid = array($mid);
		$id = I('id','','intval');
		is_numeric($id) && $id = array($id);
		isset($_REQUEST['taglink']) && $id = I('taglink/a','','intval');
		if(IS_POST)
		{
			if(count($id)==0)
			{
				$this->error('请选择一个想要被撤销的标签');
			}
			if(count($id)>50)
			{
				$this->error('批量修改个数超过上限');
			}
			$Ucuser = D('Ucuser');
			$UcTag = D("Ucuser/UcuserTag");
			$map['mp_id'] = get_mpid();
			$map['mid'] = array('in',$mid);
			foreach($id as $tagid)
			{
				$map['tagid_list'] = $Ucuser->get_tag_id_map($tagid);
				$ucuser_info_list = $Ucuser->where($map)->Field('mid,openid,tagid_list')->select();
				if(empty($ucuser_info_list)) continue;
				$Ucuser->startTrans();
				foreach($ucuser_info_list as $ucuser_info)
				{
					$ucuser_info['tagid_list'] = json_decode($ucuser_info['tagid_list'],true);
					$tagid_list = array_diff($ucuser_info['tagid_list'],array($tagid));
					$tagid_list = array_values($tagid_list);
					$ret = $Ucuser->where(array('mid = '.$ucuser_info['mid']))
						->save(array('tagid_list'=>json_encode($tagid_list)));
					$ret && $UcTag->where(array('id'=>$tagid,'mp_id'=>get_mpid()))->setDec('count');
				}
				//todo 调用微信接口删除标签 失败则回滚
				$this->init_wx();
				$openid = array_column($ucuser_info_list,'openid');
				$ret = $this->weObj->batchuntaggingTagsMembers($tagid,$openid);
				if(!$ret)
				{
					$Ucuser->rollback();
					$this->error('取消标签失败，错误：'.(ErrCode::getErrText($this->weObj->errCode)?ErrCode::getErrText($this->weObj->errCode):$this->weObj->errMsg));
				}
				$Ucuser->commit();
			}

			$this->success('成功取消标签');
		}
		else{

			if(	is_numeric($mid)
				|| (is_array($mid) && (count($mid)==1) && ($mid = $mid['0']) && isset($_REQUEST['id']))
				)
			{
				$this->assign('confirm_text','确定取消标签？');
				$this->assign('data',array('ids'=>$mid,'id'=>$id));
				$this->assign('posturl',url('ucuser/Ucuser/deleteTagLnik'));
				$this->display('Public/popup');
			}
			else{
				$option['mp_id'] = get_mpid();
				$UcTag = D("Ucuser/UcuserTag");
				$ret = $UcTag->get_tag_list($option);
				$tagidlist = $ret['list'];
				foreach($tagidlist as $a)
				{
					if($a['id'] == 1) continue;
					$inputs['checkbox'][] =array('key'=>'taglink','id'=>$a['id'],'type'=>'checkbox','name'=>$a['name'],'value'=>false);
				}
				$this->assign('confirm_text','确定要撤销用户标签？');
				$this->assign('inputs',$inputs);
				$this->assign('data',array('ids'=>$mid));
				$this->assign('posturl',url('ucuser/Ucuser/deleteTagLnik'));
				$this->display('Public/popup');
			}

		}
	}

	private function func_get_ucuser_tag(&$user_info)
	{
		if(!empty($user_info['tagid_list']))
		{
			$UcTag = db("UcuserTag");
			$user_info['tagid_list'] = json_decode($user_info['tagid_list'],true);
			asort($user_info['tagid_list']);
			$user_info['tagid_lists'] = '';

			for($i = 0;$i<	4;$i++)
			{
				if(!empty($user_info['tagid_list'][$i]))
				{
//					$user_info['tagid'.$i] = $user_info['tagid_list'][$i];
//					$user_info['tagid'.$i.'name'] = $UcTag->where(array('id'=>$user_info['tagid_list'][$i]))->getfield('name');

					$user_info['tagid_lists'] .= '<a class = "btn btn-sm btn-info" href="javascrapt:void(0);" modal-url="';
					$user_info['tagid_lists'] .= url('/ucuser/Ucuser/deleteTagLnik/',array('ids'=>$user_info['mid'],'id'=>$user_info['tagid_list'][$i])).'" data-role="modal_popup"> ';
					$user_info['tagid_lists'] .= $UcTag->where(array('id'=>$user_info['tagid_list'][$i]))->getfield('name').'<span class="text-danger">  X</span>';
					$user_info['tagid_lists'] .= '</a> ';
					if($user_info['tagid_list'][$i]==1)
					{
						$user_info['tagid_lists'] = '<sapn class="btn btn-danger btn-sm">黑名单</sapn>';
					}
				}
			}
		}
		return $user_info;
	}

	/*
	 * 将tag_id 装换为 select 格式
	 */
	private function get_tag_list_to_select()
	{
		$UcTag = db("UcuserTag");
		$option['mp_id'] = get_mpid();
		$ret = $UcTag->where($option)->field('id,name,count')->select();
		$select = array();
		if(empty($ret))
		{
			return false;
		}
		foreach($ret as $a)
		{
			if($a['id'] == 1) continue;
			$select[] = array('id'=>$a['id'],'value'=>$a['name'].'('.$a['count'].')');
		}
		return $select;
	}

    /**
     * 同步公众号粉丝数据
	 * 粉丝超过10w 未防止超时或超内存 跳转接力同步数据
     */
    public function sycUcuser(){

		($temp_id = input('temp_id',false,'/\w{13}/')) && $arr =  session($temp_id);
		$res = $this->weObj->getUserList((empty($arr['next_openid'])?'':$arr['next_openid']));
		if(!$res)
		{
			$this->error('获取微信粉丝列表错误，错误信息：'.ErrCode::getErrText($this->weObj->errCode));
		}
		$res['count'] = $res['count']+ (empty($arr['count'])?0:$arr['count']);


        $Ucuser = model("Ucuser"); // 实例化Ucuser对象
        $map['mp_id'] = get_mpid();
        $allUcuser = $Ucuser->where($map)->getField('mid,openid');
		empty($allUcuser) && $allUcuser = array();
        $diff = array_diff($res['data']['openid'],$allUcuser);

        foreach($diff as $i) {
          	$Ucuser->registerUser( get_mpid() ,$i);
        }
		//还有粉丝信息 跳转接力
		if($res['count'] < $res['total'])
		{
			empty($arr) && $temp_id =  uniqid();
			$arr = array('next_openid'=>$res['next_openid'],'count'=>$res['count']);
			session($temp_id,$arr);
			$this->success('正在获取粉丝列表('.$res['count'].'/'.$res['total'].')，请不要关闭页面',
				url('ucuser/Ucuser/sycucuser/temp_id/'.$temp_id));
		}
		else
		{
			$this->success('成功获取粉丝列表，数量：'.$res['total'].'个',url('index'));
		}
    }

    /**
     * 获取公众号粉丝信息
     */
    public function sycUcuserInfo(){

        $Ucuser = model("Ucuser"); // 实例化Ucuser对象
        $map['mp_id'] = get_mpid();
		$force = input('force','','intval');
		if($force)
		{
			$Ucuser->where($map)->update(array('status'=>1));
		}
		$mid = input('mid','','intval');
		empty($mid) || $map['mid'] = array('gt',$mid);
        $map['status'] = array('neq',2);//只取未同步的
        $allUcuser = $Ucuser->where($map)->limit(100)->field('openid,language as lang')->select();//微信接口一次最多允许拉取100个粉丝信息

		$openids = array('user_list'=>$allUcuser);
		$res = $this->weObj->getUsersInfo($openids);
		if(!$res)
		{
			$this->error('获取粉丝列表失败，错误：'.ErrCode::getErrText($this->weObj->errCode));
		}
		foreach($res['user_info_list'] as &$user_info)
		{
			$user_info['status'] = 2;
			$user_info['tagid_list'] = json_encode($user_info['tagid_list']);
			// 用户资料
			$Ucuser->where('openid = "'.addslashes($user_info['openid']).'"')->update($user_info);
			$last_openid = $user_info['openid'];
		}
		$mid = $Ucuser->where('openid = "'.addslashes($last_openid).'"')->column('mid');
		$map['mid'] = array('gt',$mid);
		$count = $Ucuser->where($map)->count();
		if($count>0)
		{
			$this->success('正在获取粉丝信息，剩余数量：'.$count.'，请不要关闭页面',
			url('ucuser/Ucuser/sycUcuserInfo/mid/'.$mid));
		}
		else
		{
			$this->success('同步粉丝数据成功',url('index'));
		}


    }

    /**
     * 删除公众号重复粉丝信息，忘记当时怎么写的算法了，好像效果是只保留openid相同的最大的那个mid记录
     * @param null $ids
     * @author patrick<contact@uctoo.com>
     */
    public function delDup($ids = null){

        $Ucuser = D("Ucuser"); // 实例化Ucuser对象
        $map['mp_id'] = get_mpid();
        $allUcuser = $Ucuser->where($map)->getField('mid,openid');

        $unDup = array_flip(array_flip($allUcuser));
        $dup = array_diff_assoc($allUcuser,$unDup);
        $dupKeys = array_keys($dup);
        $Ucuser->delete(arr2str($dupKeys));
        $this->success('数据剔重成功',U('index'));
    }

    public function config()
    {
        $list['url'] = url('Ucuser/Index/index', array('mp_id' => get_mpid()),true,true);

        //显示页面
        $builder = new AdminConfigBuilder();
        $builder
            ->title('微会员配置')
            ->keyTextArea('url', '微会员用户中心链接')
            ->data($list)
            ->display();
    }

    /**
     * 微会员统计
     * @author patrick<contact@uctoo.com>
     */
    public function stats()
    {

        if (UID) {

            if(IS_POST){
                $count_day=I('post.count_day', C('COUNT_DAY'),'intval');
                if(M('Config')->where(array('name'=>'COUNT_DAY'))->setField('value',$count_day)===false){
                    $this->error('设置失败。');
                }else{
                    S('DB_CONFIG_DATA',null);
                    $this->success('设置成功。','refresh');
                }

            }else{
                $this->meta_title = '管理首页';
                $today = date('Y-m-d', time());
                $today = strtotime($today);
                $count_day = C('COUNT_DAY');
                $count['count_day']=$count_day;
                for ($i = $count_day; $i--; $i >= 0) {
                    $day = $today - $i * 86400;
                    $day_after = $today - ($i - 1) * 86400;
                    $week[] = date('m月d日', $day);
                    $user = Ucuser()->where('reg_time >=' . $day . ' and reg_time < ' . $day_after)->count() * 1;
                    $registeredMemeberCount[] = $user;
                    if ($i == 0) {
                        $count['today_user'] = $user;
                    }
                }
                $week = json_encode($week);
                $this->assign('week', $week);
                $count['total_user'] = $userCount = Ucuser()->where(array('subscribe' => 1))->count();
                $count['today_action_log'] = M('ActionLog')->where('status=1 and create_time>=' . $today)->count();
                $count['last_day']['days'] = $week;
                $count['last_day']['data'] = json_encode($registeredMemeberCount);
                // dump($count);exit;

                $this->assign('count', $count);
                $this->display('');
            }
        } else {
            $this->redirect('Public/login');
        }
    }


	public function ucuser_tag($page = 1,$r=10)
	{
		$UcTag = D("Ucuser/UcuserTag");
		$option['mp_id'] = get_mpid();
		$ret = $UcTag->get_tag_list($option);
		$totalCount = $ret['count'];
//		var_dump(__file__.' line:'.__line__,$ret);exit;
		$builder = new AdminListBuilder();
		$builder
			->title('用户列表')
			->button('同步微信标签列表', array('href' => U('/ucuser/Ucuser/sycUcuserTag')))
			->buttonModalPopup(U('/ucuser/Ucuser/createUcuserTags'),'','新建微信标签')
			->keyText('id', 'id')
			->keyLinkByFlag('name', '分组名','/ucuser/Ucuser/index/show_type/1&taglist%5B%5D=###&','id')
			->keyText('count', '数量')
			->keyDoActionModalPopup('/ucuser/Ucuser/updateUcuserTag/id/###', '重命名','操作')
			->keyDoActionModalPopup('/ucuser/Ucuser/deleteUcusersTag/id/###', '删除','操作')
			->data($ret['list'])
			->pagination($totalCount, $r)
			->display();
	}

	/*
	 * 删除用户标签组
	 */
	public function deleteUcusersTag()
	{
		$id = I('id','','int');
		if(IS_POST)
		{
			$UcTag = D("Ucuser/UcuserTag");
			$Ucuser = D("Ucuser"); // 实例化Ucuser对象
			$map['mp_id'] = get_mpid();
			$map['id'] = $id;
			if(in_array($id,array('0','1','2')))
			{
				$this->error('这个标签默认保留的标签,不能修改');
			}
			$tagid = $UcTag->where($map)->find();
			if(empty($tagid))
			{
				$this->error('该公众号无此标签');
			}
			if($tagid['count']>100000)
			{
				$this->error('先进行取消标签的操作，直到粉丝数不超过10w后，才可直接删除该标签');
			}
			$map['tagid_list'] = $Ucuser->get_tag_id_map($id);
			$ucuser_list = $Ucuser->where($map)->field('mid,tagid_list')->select();
			$Ucuser->startTrans();
			foreach($ucuser_list as &$a)
			{

				$Ucuser->delete_tag_id($a,$id);
			}
			$UcTag->where($map)->delete();
			unset($ucuser_list);
			$this->init_wx();
			$ret = $this->weObj->deleteTags($id);
			if(!$ret)
			{
				$this->rollback();
				$this->error('删除公众号标签失败，错误：'.(ErrCode::getErrText($this->weObj->errCode)?ErrCode::getErrText($this->weObj->errCode):$this->weObj->errMsg));
			}
			$UcTag->commit();

			//todo 调用微信接口删除标签 失败则回滚
			$this->success('删除成功');
		}
		else{
			$this->assign('confirm_text','删除标签后，该标签下的所有用户将失去该标签属性。是否确定删除？');
			$this->assign('data',array('id'=>$id));
			$this->assign('posturl',U('ucuser/Ucuser/deleteUcusersTag'));
			$this->display('Public/popup');
		}
	}

	/*
	 * 设置备注名
	 */
	public function updateUserRemark()
	{
		$ids = I('ids','','intval');
		$name = I('name','','text');
		$model = D('Ucuser');
		$map['mp_id'] = get_mpid();
		$map['mid'] = $ids;
		$user_info = $model->where($map)->Field('remark,nickname,openid')->find();
		if(IS_POST)
		{
			if(empty($user_info))
			{
				$this->error('用户不存在');
			}
			if(mb_strlen($name,'utf-8')>30)
			{
				$this->error('超过30个字符,'.mb_strlen($name,'utf-8').'/30');
			}
			$this->init_wx();
			$ret = $this->weObj->updateUserRemark($user_info['openid'],$name);
			if(!$ret)
			{
				$this->error('设置备注失败，错误：'.(ErrCode::getErrText($this->weObj->errCode)?ErrCode::getErrText($this->weObj->errCode):$this->weObj->errMsg));
			}
			$model->where($map)->save(array('remark'=>$name));
			$this->success('操作成功');
		}
		else
		{
			$inputs['text'] = array(
				array('key'=>'name','type'=>'text','name'=>'备注(30字内):','value'=>(empty($user_info['remark'])?$user_info['nickname']:$user_info['remark']))
			);
			$this->assign('confirm_text','设置备注');
			$this->assign('inputs',$inputs);
			$this->assign('data',array('ids'=>$ids));
			$this->assign('posturl',U('ucuser/Ucuser/updateUserRemark'));
			$this->display('Public/popup');
		}
	}

	/*
	 * 增加用户标签组
	 */
	public function updateUcuserTag()
	{
		$id = I('id','','int');
		$UcTag = D("Ucuser/UcuserTag");
		$map['mp_id'] = get_mpid();
		if(IS_POST)
		{
			if(in_array($id,array('0','1','2')))
			{
				$this->error('这个标签默认保留的标签,不能修改',url('ucuser/Ucuser/ucuser_tag'));
			}
			$name = I('name','','text');
			$map['id'] = $id;
			$tagid = $UcTag->where($map)->find();
			if(empty($tagid))
			{
				$this->error('该公众号无此标签');
			}
			$tagid['name'] = $name;
			$data = $UcTag->create($tagid);
			if(!$data)
			{
				$this->error('操作失败，'.$UcTag->getError());
			}
			$this->init_wx();
			$ret = $this->weObj->updateTags($id,$name);
			if(!$ret)
			{
				$this->error('编辑公众号标签失败，错误：'.(ErrCode::getErrText($this->weObj->errCode)?ErrCode::getErrText($this->weObj->errCode):$this->weObj->errMsg));
			}
			$UcTag->save($data);
			$this->success('修改成功');
		}
		else
		{
			$map['id'] = $id;
			$value= $UcTag->where($map)->getfield('name');
			$inputs['text'] = array(
				array('key'=>'name','type'=>'text','name'=>'标签名称','value'=>$value)
			);
			$this->assign('confirm_text','编辑标签名');
			$this->assign('inputs',$inputs);
			$this->assign('data',array('id'=>$id));
			$this->assign('posturl',url('ucuser/Ucuser/updateUcuserTag'));
			$this->display('Public/popup');
		}
	}

	public function createUcuserTags()
	{
		$UcTag = D("Ucuser/UcuserTag");
		$map['mp_id'] = get_mpid();
		if(IS_POST)
		{
			$name = I('name','','text');

			$map['name'] = $name;
			$tagid = $UcTag->where($map)->find();
			if(!empty($tagid))
			{
				$this->error('该标签已存在');
			}
			$this->init_wx();
			$ret = $this->weObj->createTags($name);
			if(!$ret)
			{
				$this->error('创建公众号标签失败，错误：'.(ErrCode::getErrText($this->weObj->errCode)?ErrCode::getErrText($this->weObj->errCode):$this->weObj->errMsg));
			}
			$ret['mp_id'] = get_mpid();
			$data = $UcTag->create($ret);
			if(!$data)
			{
				$this->error('操作失败，'.$UcTag->getError());
			}
			$UcTag->add($data);
			$this->success('修改成功');
		}
		else
		{
			$inputs['text'] = array(
				array('key'=>'name','type'=>'text','name'=>'标签名称','value'=>'')
			);
			$this->assign('confirm_text','新建标签名');
			$this->assign('inputs',$inputs);
			$this->assign('data',array(''=>""));
			$this->assign('posturl',U('ucuser/Ucuser/createUcuserTags'));
			$this->display('Public/popup');
		}
	}

	/*
	 * 同步公众号的用户标签列表
	 */
	public function sycUcuserTag()
	{


		$this->init_wx();
		$res = $this->weObj->getTags();
		if(!$res)
		{
			$this->error('获取公众号已创建的标签失败，错误：'.ErrCode::getErrText($this->weObj->errCode));
		}
		$UcTag = db("UcuserTag");
		foreach($res as &$r)
		{
			$r['mp_id'] = get_mpid();
			$UcTag->add_or_edit_tag($r);
			$ids[] = $r['id'];
		}
		$UcTag->where(array('mp_id'=>get_mpid(),'id'=>array('not in',$ids)))->delete();
		$this->success('获取公众号已创建的标签成功',url('ucuser/Ucuser/ucuser_tag'));
	}

}
