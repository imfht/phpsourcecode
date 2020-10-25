<?php
namespace app\reply\controller;

use WxSDK\core\module\KeFuKit;
use app\common\model\WxApp;
use app\common\service\Tool;
use app\file\model\WxFile;
use app\file\service\UpService;
use app\msgReply\model\WxMsgreply;
use app\msg\model\WxMsg;
use app\news\model\WxNews;
use app\user\model\WxUser;
use think\Controller;
use app\news\service\UpNews;

class Hand extends Controller {
	// 传入视图的参数
	protected $data;
	
	// 输入的数据 array
	protected $in;
	/**
	 * 构造函数
	 * 获取配置、数据；
	 * 数据初始化
	 */
	public function __construct() {
		global $ecms_hashur;
		parent::__construct ();
		
		$ecms_hashur = isset ( $ecms_hashur ) ? $ecms_hashur : '';
		// 获取默认公众号aid，加入数据$r;
		$common = new \app\common\controller\Index ();
		$wx = $common->getDefaultWx ();
		if (! empty ( $wx ['errCode'] )) {
			$this->error ( '中止操作：' . $wx ['errMsg'] );
		}
		
		$this->data = [ 
				'title' => '手动回复',
				'version' => config ( 'version' ),
				'ecms_hashur' => $ecms_hashur,
				'form_error' => array (),
				'public' => url ( '/', '', false ),
				'wx' => $wx ['data'],
				'aid' => $wx ['data'] ['id'] 
		];
		$this->in = isset ( $_POST ) && count ( $_POST ) > 0 ? $_POST : $_GET;
	}
	/**
	 * index
	 * @method 主函数
	 *
	 * @return string 封面页
	 */
	public function index() {
		$data = $this->data;
		$in = $this->in;
		$WxMsg = new WxMsg ();
		$User = new \app\user\controller\Index ();
		if ($in ['type'] == 'msg') {
			$my_query ['type'] = 'msg';
			$data ['msg'] ['id'] = $in ['id'];
			$my_query ['id'] = $in ['id'];
			$res = $WxMsg->get ( $in ['id'] );
			if ($res) {
				$r = $User->getTheUser ( $res ['user_name'] );
				if ($r ['errCode']) {
					$data ['user'] = [ ];
				} else {
					$data ['user'] = $r ['data'];
				}
				$where ['user_name'] = $res->user_name;
			} else {
				$this->error ( '系统打了个盹，请稍后重试！' );
			}
		}else{
			$my_query ['type'] = "user";
			$my_query ['id'] = $in ['id'];
			$wxUser = WxUser::get($this->in["id"]);
			$data ['user'] = $wxUser;
			$where ['user_name'] = $wxUser->getAttr("union_id");
		}
		$my_query ['ecms_hashur'] = $data ['ecms_hashur'] ['href'];
		$where ['aid'] = $this->data ['aid'];
		if (isset ( $in ['search'] ) && (! empty ( $in ['search'] ) || $in ['search'] === 0)) {
			$where ['content'] = [ 
					'like',
					'%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		}
		// 获取数据
		
		$list = $WxMsg->where ( $where )->order('id',"desc")->paginate ( 5, false, [ 
				'query' => $my_query,
				'path' => '' 
		] ); // 保持链接稳定，尤其是修改等操作后跳转至本函数时
		if(!isset($data["msg"]["id"])){
			$data["msg"]["id"] = count($list) > 0 ? $list[0]["id"] : 0;
		}
		$WxMsgreply =new WxMsgreply(); //此处有修改
		foreach ( $list as $k => $v) {
			$res = $WxMsgreply->where ( [ 
					'aid' => $this->data['aid'],
					'msg_id'=>$v['id']
			] )->order('id','desc')->select ();
			if($res){
// 				$res=$res->toArray();

			    foreach ($res as $val){
			        $type = $val['msg_type'];
			        if($type == 'img' || $type == "voice" || $type == "video" || $type == "music"){
    			        $file = new WxFile();
    			        $r = $file->get($val[$type]);
    			        if($r){
    			            $url = '//'.$_SERVER['HTTP_HOST'].$r['path'].'/'.$r['name'];
    			            $r['url'] = $url;
    			            $val[$type] = $r;
    			        }
			        }else if($type == "news"){
			            if(empty($val['news'])){
			                $val['news']=[];
			                continue;
			            }
			            $ids = json_decode($val['news']);
			            if($ids){
			                $news = [];
			                foreach ($ids as $id){
			                    $newItem = new WxNews();
			                    $r = $newItem->get($id);
			                    if($r){
			                        $r['url'] = url("/../view.php?type=news&id=".$r['id']);
			                        $news[] = $r;
			                    }
			                }
			                $val['news']=$news;
			            }
			        }
			    }
				$v->msgreply=$res;
			}
			
// 			print_r($res);
// 			echo "<br><br>";
		}
// 		exit;
		$data ['msg_type'] = 'text';
		$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
		$data ['page'] = $list->render ();
		$list = Tool::transURL($list, $this->data["aid"]);
		$data ['list'] = $list;
		return $this->view ( './hand', $data );
	}
	/**
	 * 手动发送消息
	 */
	public function action(){
		//获取数据库中消息数据
		$mId = $_POST['msg_id_for_user'];
		if(!$mId){
		    $this->error("消息不存在", null);
		}
		$Model = new WxMsg();
		$r = $Model->get($mId);
		if(!$r){
		    $this->error("消息不存在", null);
		}
		if(!$r->aid){
		    $this->error("数据错误", null);
		}
		$app = new WxApp($r->aid);
		if($_POST['msg_type'] == 'text'){
    		$ret = KeFuKit::sendTextMsg($app, $r->user_name, urlencode($_POST['text']));
    		if($ret->ok()){
    		    //写回复记录
    		    $reply = new WxMsgreply();
    		    $reply->allowField(TRUE)->save([
    		        'aid'=>$r->aid,
    		        'msg_id'=>$mId,
    		        'my_name'=>$r->my_name,
    		        'user_name'=>$r->user_name,
    		        'msg_type'=>'text',
    		        'text'=>$_POST['text']
    		    ]);
    		    $this->success("发送成功");
    		}else{
    		    $this->error($ret->errMsg);
    		}
		}else if($_POST['msg_type'] == 'img'){
		    $ret = UpService::getImgShortMedia($app, $_POST['img']);
		    if($ret->ok()){
		        $mediaId = $ret->data['media_id'];
		        $ret = KeFuKit::sendImageMsg($app, $r->user_name, $mediaId);
		        if($ret->ok()){
		            //写回复记录
		            $reply = new WxMsgreply();
		            $reply->allowField(TRUE)->save([
		                'aid'=>$r->aid,
		                'msg_id'=>$mId,
		                'my_name'=>$r->my_name,
		                'user_name'=>$r->user_name,
		                'msg_type'=>'img',
		                'img'=>$_POST['img']
		            ]);
		            $this->success("发送成功");
		        }else{
		            $this->error($ret->errMsg);
		        }
		    }else{
		        $this->error($ret->errMsg);	
		    }
		}elseif($_POST['msg_type'] == "voice"){
		    $ret = UpService::getVoiceShortMedia($app, $_POST['voice']);
		    if($ret->ok()){
		        $mediaId = $ret->data['media_id'];
		        $ret = KeFuKit::sendVoiceMsg($app, $r->user_name, $mediaId);
		        if($ret->ok()){
		            //写回复记录
		            $reply = new WxMsgreply();
		            $reply->allowField(TRUE)->save([
		                'aid'=>$r->aid,
		                'msg_id'=>$mId,
		                'my_name'=>$r->my_name,
		                'user_name'=>$r->user_name,
		                'msg_type'=>'voice',
		                'voice'=>$_POST['voice']
		            ]);
		            $this->success("发送成功");
		        }else{
		            $this->error($ret->errMsg);
		        }
		    }else{
		        $this->error($ret->errMsg);
		    }
		}elseif($_POST['msg_type'] == 'video'){
		    $wf = new WxFile();
		    $video = $wf->get($_POST['video']);
		    if(!$video){
		        $this->error('数据不存在');
		    }
		    $ret2 = UpService::getThumbLongMedia($app, $video['thumb_id']);

		    if($ret2->ok()){
		        $thumbMediaId = $ret2->data['thumb_media_id'];
		    }else{
		        $this->error($ret2->errMsg);
		    }
		    $ret = UpService::getVideoLongMedia($app, $video);
		    if(!$ret->ok()){
		        $this->error($ret->errMsg);
		    }
		    $ret = KeFuKit::sendVideoMsg($app, $r->user_name, $ret->data['media_id'], $thumbMediaId
		        , urlencode($video['title']), urlencode($video['description']));
		    if($ret->ok()){
		        //写回复记录
		        $reply = new WxMsgreply();
		        $reply->allowField(TRUE)->save([
		            'aid'=>$r->aid,
		            'msg_id'=>$mId,
		            'my_name'=>$r->my_name,
		            'user_name'=>$r->user_name,
		            'msg_type'=>'video',
		            'video'=>$_POST['video']
		        ]);
		        
		        $this->success("操作成功");
		    }
		    $this->error($ret->errMsg);
		}elseif($_POST['msg_type'] == 'news'){
		    $newsId = $_POST['news'][0];//客服接口只允许发送单个图文
		    $service = new UpNews();
		    $ret = $service->getMediaId($app, $newsId);
		    if(!$ret->ok()){
		        $this->error($ret->errMsg);
		    }
    		$ret = KeFuKit::sendNewsMsgInner($app, $r->user_name, $ret->data['media_id']);
    		if(!$ret->ok()){
    		    $this->error($ret->errMsg);
    		}
    		//写回复记录
    		$reply = new WxMsgreply();
    		$news[]=$newsId;
    		$reply->allowField(TRUE)->save([
    		    'aid'=>$r->aid,
    		    'msg_id'=>$mId,
    		    'my_name'=>$r->my_name,
    		    'user_name'=>$r->user_name,
    		    'msg_type'=>'news',
    		    'news'=>json_encode($news)
    		]);
    		$this->success("发送成功");
		    	    
		}
	}
	/**
	 * view
	 * @method 显示
	 * @param array $temp 模板路径
	 * @param array $data 数据
	 * @return string HTML代码
	 */
	private function view($temp, $data) {
		$head = $this->fetch ( 'common@./head', $data );
		$foot = $this->fetch ( 'common@./foot', $data );
		return $head . $this->fetch ( $temp, $data ) . $foot;
	}
}
