<?php
namespace app\articles\widget;

use think\Controller;

/*最新文章*/
class News extends Controller
{
	public function lists($map, $limit = 5)
	{
        $Nlist = model('Articles')->getListByMap($map,$limit);
        $this->assign('list',$Nlist);
        return $this->fetch('widget/news');
	}

}