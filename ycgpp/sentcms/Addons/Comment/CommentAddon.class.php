<?php 
namespace Addons\Comment;
use Common\Controller\Addon;
class CommentAddon extends Addon{

	public $info = array(
		'name' => 'Comment',
        'title' => '评论插件',
        'description' => '评论插件',
        'status' => 1,
        'author' => 'Colin',
        'version' => '0.1',
	);

	public $admin_list = array(
		'list_grid' => array(
			'id:ID',
			'nickname:评论人',
			'content:评论内容',
			'reply_num:评论数',
			'ip:评论IP',
			'create_time:评论时间',
			'id:操作:/admin.php?s=/addons/execute/_addons/Comment/_controller/AdminComment/_action/Commentlist/uid/,显示列表|/admin.php?s=/addons/execute/_addons/Comment/_controller/AdminComment/_action/DeleteComment/type/model/uid/,删除',
		),
		'model'=>'Comment',
		'order'=>'id asc'
	);


	public $custom_adminlist = '';

	public function install(){
		//读取插件sql文件
        $sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/install.sql');
        $sqlFormat = $this->sql_split($sqldata, C('DB_PREFIX'));
        $counts = count($sqlFormat);
        for ($i = 0; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            D()->execute($sql);
        }
        return true;
	}

	public function uninstall(){
		$sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/uninstall.sql');
        $sqlFormat = $this->sql_split($sqldata, C('DB_PREFIX'));
        $counts = count($sqlFormat);
         
        for ($i = 0; $i < $counts; $i++) {
            $sql = trim($sqlFormat[$i]);
            D()->execute($sql);
        }
		return true;
	}

	public function Config(){
		return $this->getConfig();
	}

	/*载入评论页面*/
	public function documentDetailAfter($info){
		$this->assign('info',$info);
		$this->assign('config',$this->getConfig());
		$model = D('Addons://Comment/Comment')->getAllComment();
		$remodel = D('Addons://Comment/CommentReply');
		foreach ($model as $key => $value) {
			array_push($value,$remodel->getAllReplyOrder($value['id']));
			$list[] = $value;
		}
		$this->assign('list',$list);
		$this->display('View/Default/Comment');
	}
}