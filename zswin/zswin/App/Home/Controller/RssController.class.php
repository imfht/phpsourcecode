<?php
namespace Home\Controller;
use Org\Util\Rss;

class RssController extends HomeController {
    public function article(){
	
		$Blogs = D('Article');
		$blog = $Blogs->where(array('status'=>1))->getField('id,title,description,create_time,uid');
		
	
		
		$RssConf = array('channelTitle'=>'zswin社交类博客',
			             'channelLink'=>'http://zswin.cn',
			             'channelDescrīption'=>'zswin开源博客',
			             'copyright'=>'zswin');
		$RSS = new Rss($RssConf);
		foreach ($blog as $k => $v) {
			
			$RSS->AddItem($v['title'] ,ZSU('/artc/'.$v['id'],'Index/artc',array('id'=>$v['id'])) ,$v['description'] ,toDate($v['create_time']) ,$v['id'] ,get_username($v['uid']));
		}
		$RSS->SaveToFile("./rss.xml");
		echo $RSS->Show();
	
    }
}