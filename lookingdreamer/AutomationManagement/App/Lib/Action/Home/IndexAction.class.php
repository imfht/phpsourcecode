<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class IndexAction extends BaseAction{
    public function index()
    {
        $this->assign("WEBTITLE", C("WEB_TITLE"));
        $this->assign("KEYWORDS", C("WEB_KEY"));
        $this->assign("DESCRIPTION", C("WEB_DESCRIPTION"));
        $this->assign("SEOKEY", explode(',',C("SEOKEY")));
        $this->display();
    }
}
?>