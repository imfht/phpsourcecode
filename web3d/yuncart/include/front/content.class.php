<?php

defined('IN_CART') or die;

/**
 *  
 * 内容
 *
 *
 * */
class Content extends Base
{

    /**
     *  
     * 查看内容
     *
     *
     * */
    public function view()
    {
        $contentid = isset($_GET["contentid"]) ? intval($_GET["contentid"]) : 0;
        if (!$contentid) {
            $contentid = DB::getDB()->selectval("content", "contentid", "isdel=0 AND ispublish=1 AND type=1");
        }
        $this->data["content"] = DB::getDB()->selectrow("content", "*", "contentid='$contentid' AND isdel=0 AND ispublish=1");
        if (!$this->data['content']) {
            cerror(__("article_is_not_exist"));
        }
        $this->output("content_view");
    }

    public function page()
    {
        $pageid = intval($_GET["pageid"]);
        $this->data["page"] = DB::getDB()->selectrow("page", "*", "pageid='$pageid' AND isdel=0");
        if (!$this->data["page"]) {
            cerror(__("page_is_not_exist"));
        }
        $this->output("selfpage");
    }

}
