<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class LinkAction extends BaseAction{
    
    // 友情链接首页
    public function index() {
				$LinkModel = M("Link");
				$list = $LinkModel -> where("status=1") -> order("sort DESC, update_time DESC") -> select();
				foreach($list as $val){
					if($val['logo']){
						$friendlink[$val['type']]['logo'][] = $val;
					}else{
						$friendlink[$val['type']]['text'][] = $val;
					}
				}
				$this->assign("list_logo",$friendlink[1]['logo']);
				$this->assign("list_text",$friendlink[1]['text']);
				$this->assign("friend_logo",$friendlink[0]['logo']);
				$this->assign("friend_text",$friendlink[0]['text']);
        $this->display();
    }

}
?>