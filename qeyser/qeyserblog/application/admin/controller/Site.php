<?php
namespace app\admin\controller;
use app\admin\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯萨尔 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Site extends Base{
 	/**
 	 * 网站基本信息
 	 */
 	public function config(){
 		if(request()->isPost()){
			//修改数据
			$postData = input('post.');
			foreach ($postData as $name => $value){
			 	db('site')->where(array('name' => $name))->update(array('value' => $value));
			}
			//把数据合法化存入文件中,便于前台调用
			$data = "<?php return " . var_export($postData,true) . "?>";
			//判断是否有写的权限
			if (!file_put_contents(APP_PATH . 'webConfig.php', $data)) {
			 	$this->error('يېزىش ھوقۇقىڭىزنى تەكشۈرۈڭ !');
			}
			//成功跳转
			$this->success('ساقلاش مۇۋاپىقيەتلىك بولدى !');
 		}
 		$data = db('site')->select();
 		$this->assign('data',$data);
 		return $this->fetch();
 	}
 }