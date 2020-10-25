<?php

defined('IN_CART') or die;

/**
 *  
 * 首页
 *
 *
 * */
class Front extends Base
{

    /**
     *  
     * 首页
     *
     *
     * */
    public function index()
    {
        $ads = DB::getDB()->select("adpic", "*", "isdel=0", "order");
        foreach ($ads as $ad) {
            if ($ad['tag'] == "index_circle") {
                $this->data['circleads'][$ad['picid']] = $ad;
            } elseif ($ad['tag'] == "index_bestsell") {
                $this->data['bestsellads'][$ad['picid']] = $ad;
            }
        }
        $this->getTag();

        $this->data["frontads"] = DB::getDB()->select("adfront", "*", "isdel=0", "order");
        $this->data["notice"] = DB::getDB()->select("content", "subject,contentid,contenttype,link", "type=2 AND isdel=0", "order");
        $this->data["cats"] = $this->getCats();
        $this->output("front");
    }

    private function getTag()
    {
        $this->data['tags'] = DB::getDB()->select("tag", "*", null, "order", "", "tagid");
        if ($this->data['tags']) {
            $items = DB::getDB()->join("item_tag", "item", array("on" => "itemid"), array("a" => "tagid", "b" => "itemid,itemname,itemimg,price"), array("b" => "isdel=0"), array("a" => "order"));
            foreach ($items as $item) {
                $this->data['tags'][$item['tagid']]['item'][$item['itemid']] = $item;
            }
        }
    }

    /**
     *  
     * 提示
     *
     *
     * */
    public function hint()
    {
        $this->error();
        $this->output("hint");
    }

    public function agree()
    {
        $this->data["agree"] = getConfig("agree");
        $this->output("agree");
    }

}
