<?php

defined('IN_CART') or die;

/**
 *
 * etao设置
 * 
 */
class Etao
{

    /**
     *
     * 构造函数，
     * 
     */
    private $etao_account = '';
    private $etao_url = '';
    private $url = '';
    private $etao_postfee = '';
    private static $inventory = 0;

    public function __construct()
    {

        $etao_status = getConfig("etao_status");
        $this->etao_account = getConfig("etao_account");
        $this->etao_postfee = getConfig("etao_postfee", '10');
        $this->url = getConfig("weburl");

        if (!$etao_status) {//判断后台是否开启了etao
            self::echoxml("<error>" . __("HAVENOT_OPEN_ETAO") . "</error>");
        }
        if (!$this->etao_account) {
            self::echoxml("<error>" . __("ETAO_ACCOUNT_CANNT_EMPTY") . "</error>");
        }
        $this->etao_url = $this->url . 'data/etao';

        $this->beginxml = '<root>'
                . '<version>1.0</version>' . CRLF
                . '<modified>' . date("Y-m-d H:i:s") . '</modified>' . CRLF
                . '<seller_id>' . $this->etao_account . '</seller_id>' . CRLF
        ;
        $this->otherxml = '<cat_url>' . $this->etao_url . '/SellerCats.xml' . '</cat_url>' . CRLF
                . '<dir>' . $this->etao_url . '/item/' . '</dir>' . CRLF;
        $this->endxml = '</root>';
    }

    public function index()
    {
        $this->full();
    }

    /**
     *
     * 全量索引
     * 
     */
    public function full()
    {

        $itemids = DB::getDB()->selectcol("item", "itemid", "status=1 AND isdel=0 AND inventory>" . self::$inventory, "itemid DESC");
        $itemxml = '<item_ids>' . CRLF;
        foreach ($itemids as $itemid) {
            $itemxml .= '<outer_id action="upload">' . $itemid . '</outer_id>' . CRLF;
        }
        $itemxml .= '</item_ids>' . CRLF;
        self::echoxml($this->beginxml . $this->otherxml . $itemxml . $this->endxml);
    }

    /**
     *
     * 增量索引
     * 
     */
    public function increment()
    {

        $time = time() - 1800; //30分钟
        //需要删除的item，包括isdel=1，status=2，inventory<5的，lasttime
        $where = "modified>'$time' AND (isdel=1 or status=2 or inventory<'" . self::$inventory . "')";
        $ditemids = DB::getDB()->selectcol("item", "itemid", $where, "itemid DESC");

        $itemxml = '<item_ids>' . CRLF;
        foreach ($ditemids as $itemid) {
            $itemxml .= '<outer_id action="delete">' . $itemid . '</outer_id>' . CRLF;
        }

        //需要增加的
        $where = "isdel=0 AND status=1 AND inventory>'" . self::$inventory . "' AND modified>'$time'";
        $uitemids = DB::getDB()->selectcol("item", "itemid", $where, "itemid DESC");
        foreach ($uitemids as $itemid) {
            $itemxml .= '<outer_id action="upload">' . $itemid . '</outer_id>' . CRLF;
        }
        $itemxml .= '</item_ids>' . CRLF;
        self::echoxml($this->beginxml . $this->otherxml . $itemxml . $this->endxml);
    }

    /**
     *
     * 分类
     * 
     */
    public function cat()
    {
        $cats = DB::getDB()->select("cat", "catid,catname,pid,order,typeid", "isdel=0", "order", null, "catid");
        $tree = array();
        foreach ($cats as $cat) { //循环cat
            if (isset($cats[$cat['pid']])) {//非第一级
                $cats[$cat['pid']]['children'][$cat['catid']] = &$cats[$cat['catid']];
            } else {//第一级
                $tree[$cat['catid']] = &$cats[$cat['catid']];
            }
        }

        $catxml = '<seller_cats>' . CRLF;
        foreach ($tree as $cat) {//第一级分类
            $catxml .= '<cat>' . CRLF
                    . '<scid>' . $cat['catid'] . '</scid>' . CRLF
                    . '<name>' . $cat['catname'] . '</name>' . CRLF
            ;
            if (isset($cat['children'])) {
                $catxml .= '<cats>' . CRLF;
                foreach ($cat['children'] as $child) {
                    $catxml .= '<cat>' . CRLF
                            . '<scid>' . $child['catid'] . '</scid>' . CRLF
                            . '<name>' . $child['catname'] . '</name>' . CRLF
                            . '</cat>' . CRLF;
                }
                $catxml .= '</cats>' . CRLF;
            }
            $catxml .= '</cat>' . CRLF;
        }
        $catxml .= '</seller_cats>' . CRLF;
        self::echoxml($this->beginxml . $catxml . $this->endxml);
    }

    /**
     *
     * 商品详情
     * 
     */
    public function item()
    {
        $itemid = intval($_GET["itemid"]);
        $item = DB::getDB()->selectrow("item", "*", "itemid='$itemid' AND isdel=0");
        if (!$item) {
            self::echoxml("<error>" . __("ITEM_NOT_EXIST") . "</error>");
        }
        //商品描述
        $itemdesc = DB::getDB()->selectrow("item_desc", "*", "itemid='$itemid'");
        //商品图片
        $itemimgs = DB::getDB()->select("item_img", "*", "itemid='$itemid' AND `order`!=1");
        //商品品牌
        $brand = DB::getDB()->selectval("brand", "brandname", "brandid='" . $item['brandid'] . "'");

        //商品标签
        $tag = DB::getDB()->join("item_tag", "tag", array("on" => "tagid"), array("b" => "tagname"), array("a" => "itemid='$itemid'"), array("a" => "order"));

        //商品分类
        $catids = DB::getDB()->selectcol("item_cat", "catid", "itemid='$itemid'");
        $imgxml = '';
        foreach ($itemimgs as $img) {
            $imgxml .= '<img>' . $this->url . $img['imgpath'] . '</img>' . CRLF;
        }
        $href = getConfig('rewrite') ? "item-$itemid.html" : url("index", 'item', 'index', "itemid=$itemid", true);
        $itemxml = '<item>' . CRLF
                . '<seller_id>' . $this->etao_account . '</seller_id>' . CRLF
                . '<outer_id>' . $itemid . '</outer_id>' . CRLF
                . '<title>' . $item['itemname'] . '</title>' . CRLF
                . '<type>fixed</type>' . CRLF
                . '<available>1</available>' . CRLF
                . '<price>' . getPrice($item['price']) . '</price>' . CRLF
                . '<desc>' . htmlspecialchars($itemdesc['itemdesc']) . '</desc>' . CRLF
                . '<brand>' . $brand . '</brand>' . CRLF
                . '<image>' . $this->url . $item['itemimg'] . '</image>' . CRLF
                . '<more_images>' . $imgxml . '</more_images>' . CRLF
                . '<scid>' . ($catids ? implode(',', $catids) : '') . '</scid>' . CRLF
                . '<post_fee>' . $this->etao_postfee . '</post_fee>' . CRLF
                . '<showcase>true</showcase>' . CRLF
                . '<props>' . '</props>' . CRLF
                . '<href>' . $this->url . $href . '</href>' . CRLF
        ;
        $itemxml .= '</item>' . CRLF;
        $this->echoxml($itemxml);
    }

    private static function echoxml($xml)
    {
        header("Content-type: application/xml; charset=utf-8");
        die($xml);
    }

}
