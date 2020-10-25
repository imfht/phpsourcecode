<?php

defined('IN_CART') or die;

/**
 *  
 * 商品标签
 *
 *
 * */
class Tag extends Base
{

    /**
     *  
     * 商品标签列表
     *
     *
     * */
    public function index()
    {
        $this->data["taglist"] = DB::getDB()->select("tag", "*", null, "order");
        $this->output("tag_index");
    }

    /**
     *  
     * 添加商品标签
     *
     *
     * */
    public function tagadd()
    {
        $this->data["opertype"] = "add";
        $this->output("tag_oper");
    }

    /**
     *  
     * 修改商品标签
     *
     *
     * */
    public function tagedit()
    {
        $tagid = intval($_GET["tagid"]);
        $this->data["tag"] = DB::getDB()->selectrow("tag", "*", "tagid='$tagid'");
        $this->data["opertype"] = "edit";
        $this->data["tagid"] = $tagid;
        $this->output("tag_oper");
    }

    /**
     *  
     * 商品标签列表
     *
     *
     * */
    public function tagsave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $text = __("tag");
        switch ($opertype) {
            case 'add':
            case 'edit':
                $tagname = trim($_POST["tagname"]);
                $ismore = isset($_POST["ismore"]) ? 1 : 0;
                $pic = $ismore ? trim($_POST["pic"]) : "";
                $link = $ismore ? trim($_POST["link"]) : "";

                $data = array("tagname" => $tagname, "pic" => $pic, "link" => $link, "ismore" => $ismore);

                $tagid = intval($_POST["tagid"]);
                if ($tagid) {
                    $ret = DB::getDB()->update("tag", $data, "tagid='$tagid'");
                    $this->adminlog("al_tag", array("do" => "edit", "tagname" => $tagname));
                    $this->setHint(__('edit_success', $text));
                } else {
                    $data['order'] = 50;
                    DB::getDB()->insert("tag", $data);
                    $this->adminlog("al_tag", array("do" => "add", "tagname" => $tagname));
                    $this->setHint(__('add_success', $text));
                }
                break;
            case 'editfield':
                $field = strtolower(trim($_POST["field"]));
                $tagid = 0;
                if ($field == "delete") {//直接删除
                    $tagid = intval($_POST["idstr"]);
                    $where = "tagid='$tagid'";

                    $this->adminlog("al_tag", array("do" => "del", "tagid" => $tagid));

                    DB::getDB()->delete("tag", $where);
                    DB::getDB()->delete("item_tag", $where);
                } else {//更新字段
                    !in_array($field, array("tagname")) && exit("failure");
                    $value = trim($_POST["value"]);
                    $tagid = intval($_POST["id"]);

                    $this->adminlog("al_tag", array("do" => "edit", "tagid" => $tagid));

                    DB::getDB()->update("tag", array($field => $value), "tagid='$tagid'");
                }


                exit("success");
            case 'save'://修改排序
                $tagids = $_POST["tagid"];
                foreach ($tagids as $key => $tagid) {
                    DB::getDB()->update("tag", array("order" => $key + 1), "tagid='$tagid'");
                }
                $this->adminlog("al_tag_order");
                $this->setHint(__("edit_success", $text));
        }
    }

    /**
     *
     * 商品标签列表
     *
     */
    public function tagitem()
    {
        $tags = $this->getTags();

        $tagid = isset($_REQUEST["tagid"]) ? intval($_REQUEST["tagid"]) : 0;
        (!$tagid && $tags) && ($tagid = $tags[0]['tagid']);
        $this->data['tagopt'] = array2select($tags, "tagid", "tagname", $tagid);

        $jpara = array("on" => "itemid");
        $this->data["items"] = DB::getDB()->join("item_tag", "item", $jpara, array("b" => "itemname,itemimg,itemid"), array("a" => "tagid='$tagid'"), array("a" => "order"));

        $this->data["tagid"] = $tagid;
        $this->output("tag_item");
    }

    /**
     *
     * 保存标签商品
     *
     */
    public function tagitemsave()
    {
        $opertype = strtolower($_POST["opertype"]);
        $text = __("item_to_tag");
        switch ($opertype) {
            case 'save':
                $tagid = intval($_POST["tagid"]);
                $itemids = $_POST["itemid"];
                foreach ($itemids as $key => $itemid) {
                    DB::getDB()->update("item_tag", array("order" => $key + 1), "tagid='$tagid' AND itemid='$itemid'");
                }
                $this->setHint(__("edit_success", $text), 'tag_tagitem');
                break;
            case 'editfield':
                $field = trim($_POST["field"]);
                if ($field == "delete") {
                    $str = trim($_POST["idstr"]);
                    list($itemid, $tagid) = @explode("_", $str);
                    if (!$itemid || !$tagid)
                        exit("failure");

                    DB::getDB()->delete("item_tag", "itemid='$itemid' AND tagid='$tagid'");
                    exit("success");
                }
                break;
        }
    }

}
