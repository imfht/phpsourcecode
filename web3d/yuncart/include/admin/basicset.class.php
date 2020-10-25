<?php

defined('IN_CART') or die;

/**
 *
 * 商城基本设置 
 * 
 */
class Basicset extends Base
{

    /**
     *
     * 商城设置首页 basicset 
     * 
     */
    public function index()
    {
        if (ispostreq()) {
            $data["mallname"] = trim($_POST["mallname"]);
            $data["malllogo"] = trim($_POST["pic"]);
            $data["bossemail"] = trim($_POST["bossemail"]);
            $data["bossmobile"] = trim($_POST["bossmobile"]);

            $data["tpl"] = trim($_POST["tpl"]);
            $data["pagefoot"] = trim($_POST["pagefoot"]);

            $data["malltitle"] = trim($_POST["malltitle"]);
            $data["mallkeywords"] = trim($_POST["mallkeywords"]);
            $data["malldesc"] = trim($_POST["malldesc"]);
            $data["rewrite"] = isset($_POST["rewrite"]) ? 1 : 0;

            $data["gzip"] = isset($_POST["gzip"]) ? 1 : 0;

            $replacedata = array();
            foreach ($data as $key => $val) {
                $replacedata[] = array("key" => $key, "val" => $val, "type" => 'basicset');
            }
            $this->adminlog("al_basicset");
            DB::getDB()->replaceMulti("config", $replacedata);
            $this->setHint(__("set_success", __('basicset')));
        } else {

            $this->data["basicset"] = DB::getDB()->selectkv("config", "key", "val", "type='basicset'");

            $tpldir = SITEPATH . '/template/front';
            $temp = scandir($tpldir);
            $tpls = array();
            foreach ($temp as $k => $v) {
                if ($v != '.' && $v != '..' && is_dir($tpldir . '/' . $v)) {
                    $tpls[] = $v;
                }
            }
            !$tpls && $tpls = array("default");
            $tpl = !empty($this->data["basicset"]['tpl']) ? $this->data["basicset"]['tpl'] : "default";
            $this->data["tplopt"] = array2select($tpls, "val", "val", $tpl);

            $this->output("basicset_index");
        }
    }

    /**
     *
     * 商品分享
     * 
     */
    public function share()
    {
        if (ispostreq()) {
            $content = trim($_POST["sharecontent"]);
            $data["key"] = "share";
            $data["val"] = str_replace(array("｛", "｝"), array("{", "}"), $content);
            $data["type"] = "shareset";
            DB::getDB()->replace("config", $data);
            $this->adminlog("al_share");
            $this->setHint(__("set_success", __('shareset')), "basicset_share");
        } else {
            $this->data["shareset"] = DB::getDB()->selectkv("config", "key", "val", "type='shareset'");
            $this->output("basicset_share");
        }
    }

    /**
     *
     * 会员注册
     * 
     */
    public function agree()
    {
        if (ispostreq()) {
            $content = trim($_POST["agreecontent"]);
            $data["key"] = "agree";
            $data["val"] = str_replace(array("｛", "｝"), array("{", "}"), $content);
            $data["type"] = "agreeset";
            DB::getDB()->replace("config", $data);
            $this->adminlog("al_agree");
            $this->setHint(__("set_success", __('agree')), "basicset_agree");
        } else {
            $this->data["agreeset"] = DB::getDB()->selectkv("config", "key", "val", "type='agreeset'");
            $this->output("basicset_agree");
        }
    }

    /**
     *
     * 生成站点地图
     * 
     */
    public function updSitemap()
    {
        //xml文件
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . CRLF
                . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"   xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">" . CRLF;

        $date = date("Y-m-d");
        $rewrite = getConfig("rewrite");
        $weburl = getConfig("weburl");

        //item
        $items = DB::getDB()->select("item", "itemid", "isdel=0");
        foreach ($items as $item) {
            $url = $rewrite ? "item-" . $item['itemid'] . ".html" : url("index", "item", "index", "itemid=" . $item['itemid'], true);
            $xml .= "<url>" . CRLF
                    . "<loc>{$weburl}{$url}</loc>" . CRLF
                    . "<lastmod>$date</lastmod>" . CRLF
                    . "<changefreq>daily</changefreq>" . CRLF
                    . "<priority>1.0</priority>" . CRLF
                    . "</url>" . CRLF
            ;
        }

        //类别
        $cats = DB::getDB()->select("cat", "catid", "isdel=0");
        foreach ($cats as $cat) {
            $url = $rewrite ? "cat-" . $cat['catid'] . ".html" : url("index", "listing", "index", "catid=" . $cat['catid'], true);
            $xml .= "<url>" . CRLF
                    . "<loc>{$weburl}{$url}</loc>" . CRLF
                    . "<lastmod>$date</lastmod>" . CRLF
                    . "<changefreq>daily</changefreq>" . CRLF
                    . "<priority>1.0</priority>" . CRLF
                    . "</url>" . CRLF
            ;
        }

        //帮助文章
        $contents = DB::getDB()->select("content", "contentid", "isdel=0");
        foreach ($contents as $content) {
            $url = $rewrite ? "content-" . $content['contentid'] . ".html" : url("index", "content", "view", "contentid=" . $content['contentid'], true);
            $xml .= "<url>" . CRLF
                    . "<loc>{$weburl}{$url}</loc>" . CRLF
                    . "<lastmod>$date</lastmod>" . CRLF
                    . "<changefreq>daily</changefreq>" . CRLF
                    . "<priority>1.0</priority>" . CRLF
                    . "</url>" . CRLF
            ;
        }

        $pages = DB::getDB()->select("page", "pageid", "isdel=0");
        foreach ($pages as $page) {
            $url = $rewrite ? "page-" . $page['pageid'] . ".html" : url("index", "content", "page", "pageid=" . $page['pageid'], true);
            $xml .= "<url>" . CRLF
                    . "<loc>{$weburl}{$url}</loc>" . CRLF
                    . "<lastmod>$date</lastmod>" . CRLF
                    . "<changefreq>daily</changefreq>" . CRLF
                    . "<priority>1.0</priority>" . CRLF
                    . "</url>" . CRLF
            ;
        }

        $xml .= "</urlset>" . CRLF;
        $ret = file_put_contents(SITEPATH . "/sitemap.xml", $xml);
        $this->adminlog("al_sitemap");
        exit($ret ? "success" : "failure");
    }

    /**
     *
     * 暂停营业 对应closeset
     * 
     */
    public function close()
    {
        if (ispostreq()) {
            $data['status'] = trim($_POST["status"]);
            $data['closenotice'] = $data['status'] == 'close' ? trim($_POST["closenotice"]) : '';
            $replacedata = array();
            foreach ($data as $key => $val) {
                $replacedata[] = array("key" => $key, "val" => $val, "type" => 'closeset');
            }
            $this->adminlog("al_close");
            DB::getDB()->replaceMulti("config", $replacedata);
            $this->setHint(__("set_success", __("closeset")), "basicset_close");
        } else {
            $this->data["closeset"] = DB::getDB()->selectkv("config", "key", "val", "type='closeset'");
            $this->output("basicset_close");
        }
    }

    /**
     *
     * 客服 对应imset
     * 
     */
    public function im()
    {
        if (ispostreq()) {
            $data['imstatus'] = trim($_POST["imstatus"]);
            $data["imtext"] = trim($_POST["imtext"]);
            $imids = !empty($_POST["imid"]) ? $_POST["imid"] : array();
            $imnicks = !empty($_POST["imnick"]) ? $_POST["imnick"] : array();
            $imtypes = !empty($_POST["imtype"]) ? $_POST["imtype"] : array();
            $imarr = array();
            foreach ($imids as $key => $val) {
                if (!$val)
                    continue;
                $imarr[] = array("imid" => $val, "imnick" => $imnicks[$key], "imtype" => $imtypes[$key]);
            }
            $data["imusers"] = @serialize($imarr);

            $replacedata = array();
            foreach ($data as $key => $val) {
                $replacedata[] = array("key" => $key, "val" => $val, "type" => 'imset');
            }
            $this->adminlog("al_im");
            DB::getDB()->replaceMulti("config", $replacedata);

            $this->setHint(__("set_success", __("imset")), 'basicset_im');
        } else {
            $this->data["imset"] = DB::getDB()->selectkv("config", "key", "val", "type='imset'");
            $this->data["imusers"] = !empty($this->data["imset"]["imusers"]) ? @unserialize($this->data["imset"]["imusers"]) : array();
            $this->output("basicset_im");
        }
    }

}
