<?php
namespace app\api\controller;

use expand\ApiReturn;

class Post extends Base {

    public function initialize() {
    	parent::initialize();

	}

	// 获取文章列表接口
    public function index($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$arctypeModel = new \app\common\model\Arctype();
		if( empty($data['typeid']) ){
			$typeid_arr = cache('ARCTYPE_ID_ARR');
	        if(!$typeid_arr){
	        	$id_arr = $arctypeModel->where(['status'=>1,'mid'=>21])->order('sorts asc')->column('id');	// 栏目列表 ID
	            cache('ARCTYPE_ID_ARR', $id_arr);
				$typeid_arr = $id_arr;
	        }
		}else{
			$id_arr = $arctypeModel->where(['pid'=>$data['typeid'],'status'=>1])->order('sorts asc')->column('id');	// 栏目列表 ID
			$id_arr[] = $data['typeid'];
			$typeid_arr = $id_arr;
		}
		$where[] = ['typeid','in',$typeid_arr];
		if( !empty($data['search']) ){
			$where[] = ['title|keywords|description','like','%'.$data['search'].'%'];
		}
		if( !empty($data['uid']) ){
			$where[] = ['writer','=',$data['uid']];
		}
        $archiveModel = new \app\common\model\Archive();
		$dataList = $archiveModel->api_arclist($where,'id DESC',$data['page'],$data['number'],$this->fname);// 文章列表数据
		if( empty($dataList) ){
			return ApiReturn::r('-800');
		}
		$ding_data = cache('DING_DATA');
		if( !$ding_data ){
			$ding_data = $archiveModel->arclist('','','a','id DESC',$this->fname);	//置顶内容列表
			cache('DING_DATA',$ding_data,7200);
		}
		$tou_data = cache('TOU_DATA');
		if( !$tou_data ){
			$tou_data = $archiveModel->arclist('','','h','id DESC',$this->fname);	//头条内容列表
			cache('TOU_DATA',$tou_data,7200);
		}
		$tui_data = cache('TUI_DATA');
		if( !$tui_data ){
			$tui_data = $archiveModel->arclist('','','c','id DESC',$this->fname);	//推荐内容列表
			cache('TUI_DATA',$tui_data,7200);
		}
		$gun_data = cache('GUN_DATA');
		if( !$gun_data ){
			$gun_data = $archiveModel->arclist('','','s','id DESC',$this->fname);	//滚动内容列表
			cache('GUN_DATA',$gun_data,7200);
		}
		$return['ding_list'] = $ding_data;
		$return['tou_list'] = $tou_data;
		$return['tui_list'] = $tui_data;
		$return['gun_list'] = $gun_data;
		$return['dataList'] = $dataList;
		return ApiReturn::r(1,$return);
    }

	// 添加文章接口
    public function create($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
		$uid = \app\common\model\TokenUser::where(['token'=>$usertoken])->value('uid');
		$arctype = \app\common\model\Arctype::get(['id'=>$data['typeid']]);
		if( $arctype['is_release'] == 0 || empty($arctype) ){
			return ApiReturn::r('0','','该分类不允许发布文章' );
		}
		$data['writer'] = $uid;
		$data['description'] = auto_description('',$data['content']);
		$titles = trimall($data['title']);	//清除字符串中的空格和换行
		$data['keywords'] = csubstr($titles, 10, "", 0, false);	//中文字符串截取长度
		$archive = new \app\common\model\Archive;
		$add = $archive->allowField(true)->save($data);	//添加数据
		if( $add ){
			$archive->addonarticle()->save(['content'=>$data['content']]);
			if( !empty($data['imgurl']) ){
				$image = new \app\common\model\Image;
				$image->imgurl = $data['imgurl'];
				$image->allowField(true)->save(['fid'=>$archive->id]);
			}
			$return_data['id'] = $archive->id;
			return ApiReturn::r('1',$return_data);
		}else{
			return ApiReturn::r('0');
		}
    }
	// 获取文章内容详情接口
    public function details($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$arcdata = \app\common\model\Archive::get(['id'=>$data['id'],'status'=>1]);
		if( empty($arcdata) ){
			return ApiReturn::r('-800');
		}
		\app\common\model\Archive::where(['id'=>$data['id']])->setInc('click');
		$arcdata['avatar'] = $arcdata->UserInfo->avatar_turn;
		$arcdata['info'] = $arcdata->UserInfo->info;
		$arcdata['username'] = $arcdata->user->name?$arcdata->user->name:$arcdata->user->username;
		$arcdata['typename'] = $arcdata->arctype->typename;
		$arcdata['reply_num'] = $arcdata->reply_num;
		$arcdata['zan_num'] = $arcdata->zan_num;
		$arcdata['content'] = $arcdata->addonarticle->content;
		$arcdata['imgurl'] = $arcdata->imgurl;
		$arcdata['collect_num'] = $arcdata->collect_num;
		$return_data = $arcdata->visible($this->fname)->toArray();	// 根据返回字段来返回
		return ApiReturn::r(1,$return_data);
    }

}
