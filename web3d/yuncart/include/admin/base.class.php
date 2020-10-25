<?php

/**
 *
 * 后台操作基本类
 * 
 */
class Base
{

    public $data = array();
    public $model = 'dashboard';
    public $action = 'index';
    public $hinturl;

    /**
     *
     * 构造函数
     * 
     */
    public function __construct($model, $action)
    {
        $this->model = $model;
        $this->action = $action;
        $this->hinturl = url("admin", $model, "hint");


        $pagemodel = $this->getPageModel();
        if (isset($_SESSION['admin']) && !$_SESSION['admin']['issuper'] && $pagemodel && !in_array($pagemodel, $_SESSION['admin']['privs'])) {
            cerror(__("no_priv"));
        }
    }

    /**
     *
     * 分页
     * 
     */
    public function getRequestPage($defpagesize = 10)
    {
        $page = isset($_REQUEST["page"]) ? abs(intval($_REQUEST["page"])) : 1;
        $pagesize = isset($_REQUEST["pagesize"]) ? abs(intval($_REQUEST["pagesize"])) : $defpagesize;

        $page > 100000 && $page = 100000;
        $pagesize > 100 && $page = 100;
        return array($page, $pagesize);
    }

    /**
     *
     * 获取地址的下拉
     * 
     */
    public function getDistrictopt($province, $city = 0, $district = 0)
    {
        $this->data['provinceopt'] = Dis::getDistrict(0, $province, "option");
        $province && $this->data['cityopt'] = Dis::getDistrict($province, $city, "option");
        $city && $this->data['districtopt'] = Dis::getDistrict($city, $district, "option");
        return true;
    }

    /**
     *
     * 公用方法,操作提示
     * 
     */
    public function hint()
    {
        $this->data['hint'] = $_SESSION['hint'];
        if (isset($_SESSION['leftcur'])) {
            $this->data['leftcur'] = $_SESSION['leftcur'];
        }
        $this->output("hint");
    }

    /**
     *
     * 设置操作提示
     * 
     */
    public function setHint($hint, $leftcur = '')
    {
        $_SESSION['hint'] = $hint;
        if ($leftcur) {
            $_SESSION['leftcur'] = $leftcur;
        } else if (isset($_SESSION['leftcur'])) {
            unset($_SESSION['leftcur']);
        }
        redirect($this->hinturl);
    }

    /**
     *
     * 页面输出
     * 
     */
    public function output($filename)
    {
        require_once COMMONPATH . "/dwoo.php";
        $dwoo = new Dwoo(DWOOCOMPILED, DWOOCACHE);
        $compiler = Dwoo_Compiler::compilerFactory();
        $compiler->setDelimiters("<!--{", "}-->");
        $dwoo->setCacheTime(0);
        $this->data['pagemodel'] = $this->getPageModel();
        if (!isset($this->data['leftcur']))
            $this->data['leftcur'] = $this->getLeftCur();
        $dwoo->output(TPL . "/{$filename}.html", $this->data, $compiler);
    }

    private function getPageModel()
    {
        $models = array(
            "item" => array("brand", "type", "cat", "item", "spec", "tag", "import"),
            "promotion" => array("man", "gift", "coupon", "meal", "discount", "tuan"),
            "user" => array('user', 'ulevel', 'userqa', 'comment', "queue", "sendbox", "comprice", "notify"),
            "content" => array('content', 'navi', 'link', "flink", "page"),
            "trade" => array('trade', 'send', 'express', 'aftersale', 'payment'),
            "report" => array('report', 'sale'),
            "basicset" => array('basicset', "admin", "role", "message", "district"),
            "tools" => array('tools', 'data', "recycle", "third", "log", "pic")
        );
        foreach ($models as $key => $modelarr) {
            if (in_array($this->model, $modelarr)) {
                return $key;
            }
        }
    }

    private function getLeftCur()
    {
        $leftcur = $this->model . "_" . preg_replace("/(.+)edit|hint$/", "index", $this->action);
        return $leftcur;
    }

    /**
     *
     * 获取所有类目
     *
     */
    protected function getTypes($typeid = 0, $returntype = 'array')
    {
        static $types;
        !$types && $types = DB::getDB()->selectkv("type", "typeid", "typename", "isdel=0");

        if ($returntype == "array") {
            return $types;
        } else if ($returntype == "option") {
            $html = '';
            foreach ($types as $key => $val) {
                $html .= "<option value='" . $key . "'";
                if ($key == $typeid)
                    $html .= " selected";
                $html .= ">" . $val . "</option>";
            }
            return $html;
        }
    }

    /**
     *
     * 返回已经确定子分类的类别属性，计算等级
     *
     */
    protected function getListCat($data = array(), $level = 0)
    {
        $cats = array();
        !$data && $data = $this->getCats();
        if (!$data)
            return $cats;


        foreach ($data as $key => $val) {
            $cats[$val['catid']] = &$data[$key];
            $cats[$val['catid']]['level'] = $level;
            if (isset($val['children'])) {
                $cats[$val['catid']]['haschild'] = 1;
                $cats += $this->getListCat($val['children'], $level + 1);
                unset($data[$key]['children']);
            }
        }
        return $cats;
    }

    /**
     *
     * 获取所有类别
     *
     */
    protected function getCats($return = 'tree')
    {
        static $retarr;
        if (isset($retarr[$return]))
            return $retarr[$return];

        $cats = DB::getDB()->select("cat", "catid,catname,pid,order,typeid", "isdel=0", "order", null, "catid");
        if ($return == 'tree') {   //返回数组
            $tree = array();
            foreach ($cats as $cat) { //循环cat
                if (isset($cats[$cat['pid']])) {//非第一级
                    $cats[$cat['pid']]['children'][] = &$cats[$cat['catid']];
                } else {//第一级
                    $tree[] = &$cats[$cat['catid']];
                }
            }
            $retarr[$return] = $tree;
        } else if ($return == 'source') {//返回source
            $retarr[$return] = $cats;
        }
        return $retarr[$return];
    }

    /**
     * 
     * 获取类别的第一级option 
     *
     */
    protected function getTopCatOption($selected = 0)
    {
        $cats = DB::getDB()->selectkv("cat", "catid", "catname", "pid=0 AND isdel=0", "order");
        return array2select($cats, "key", "val", $selected);
    }

    protected function getArticleSort($type = 0, $selected = 0, $returntype = 'option')
    {
        static $sorts;
        $where = "isdel=0";
        if ($type)
            $where .= ' AND `type`=$type';
        !$sorts[$type] && $sorts[$type] = DB::getDB()->selectkv("content_sort", "sortid", "sortname", $where, array("type", "order"));
        if ($returntype == 'array') {
            return $sorts[$type];
        } else {
            return array2select($sorts[$type], "key", "val", $selected);
        }
    }

    /**
     * 
     * 获取类别的所有option 
     *
     */
    protected function getCatOption($data = array(), $level = 0, $selected = 0, $disabled = false)
    {
        !$data && $data = $this->getCats();
        $html = "";
        if (!$data)
            return $html;

        foreach ($data as $key => $val) {
            $html .= "<option value='" . $val["catid"] . "'"; //option 开始
            if ($selected == $val["catid"])
                $html .= " selected"; //是否选中
            if ($disabled && isset($val['children']))
                $html .= " disabled"; //不可选择

            $html .= ">" . str_repeat("&nbsp;&nbsp;", $level) . ($level ? "|_" : "") . $val["catname"] . "</option>"; //option结束

            if (isset($val["children"]))
                $html .= $this->getCatOption($val["children"], $level + 1, $selected, $disabled);
        }
        return $html;
    }

    /**
     * 
     * 获取商品的tag
     *
     */
    protected function getTags()
    {
        static $tags;
        !$tags && $tags = DB::getDB()->select("tag", "*");
        return $tags;
    }

    /**
     *
     * 获取所有类目
     *
     */
    protected function getBrands($brandid = 0, $returntype = 'array')
    {
        static $brands;
        !$brands && $brands = DB::getDB()->selectkv("brand", "brandid", "brandname", "isdel=0", "order");

        if ($returntype == "array") {
            return $brands;
        } else if ($returntype == "option") {
            
        }
    }

    /**
     *
     * 获取类目下所有品牌
     *
     */
    protected function getTypeBrands($typeid, $selected = 0)
    {
        $brands = DB::getDB()->join("type_brand", "brand", array("on" => "brandid"), array("b" => "brandid,brandname"), array("a" => "typeid='$typeid'", "b" => "isdel=0"));

        return array2select($brands, 'brandid', 'brandname', $selected);
    }

    //获取类别
    public function getContentSort($type, $selected = 0, $returntype = 'option')
    {
        static $sorts;
        $where = array("type" => $type, "isdel" => 0);
        !$sorts && $sorts = DB::getDB()->selectkv("content_sort", "sortid", "sortname", $where, "order", null, "sortid");
        if ($returntype == 'option') {
            return array2select($sorts, "key", "val", $selected);
        } else {
            return $sorts;
        }
    }

    /**
     *
     * 记录管理员log
     *
     */
    protected function adminlog($logname = '', $dodata = array())
    {
        if (!getConfig("adminlog_status", 0))
            return;
        $adminid = $_SESSION['admin']['adminid'];
        $uname = $_SESSION['admin']['uname'];

        $data = array();
        if (isset($dodata['do']))
            $data[] = __($dodata['do']);

        switch ($logname) {
            case 'al_tuan':
                if (isset($dodata['tuanid'])) {
                    $data[] = DB::getDB()->selectval("tuan", "subject", "tuanid='" . $dodata['tuanid'] . "'");
                } else {
                    $data[] = $dodata['subject'];
                }
                break;
            case 'al_page':
                if (isset($dodata['pageid'])) {
                    $data[] = DB::getDB()->selectval("page", "pagetitle", "pageid='" . $dodata['pageid'] . "'");
                } else {
                    $data[] = $dodata['pagetitle'];
                }
                break;
            case 'al_pic':
                if (isset($dodata['picid'])) {
                    $data[] = DB::getDB()->selectval("pic", "name", "picid='" . $dodata['picid'] . "'");
                } else {
                    $data[] = $dodata['name'];
                }
                break;
            case 'al_adpic':
                if (isset($dodata['picid'])) {
                    $data[] = DB::getDB()->selectval("adpic", "name", "picid='" . $dodata['picid'] . "'");
                } elseif (isset($dodata['name'])) {
                    $data[] = $dodata['name'];
                }
                break;
            case 'al_adfront':
                if (isset($dodata['frontid'])) {
                    $data[] = DB::getDB()->selectval("adfront", "title", "frontid='" . $dodata['frontid'] . "'");
                } elseif (isset($dodata['title'])) {
                    $data[] = $dodata['title'];
                }
                break;
            case 'al_word':
                if (isset($dodata['wordid'])) {
                    $data[] = DB::getDB()->selectval("adword", "word", "wordid='" . $dodata['wordid'] . "'");
                } elseif (isset($dodata['word'])) {
                    $data[] = $dodata['word'];
                }
                break;
            case 'al_message_tpl2':
                $text = DB::getDB()->selectval("message_set", "text", "code='" . $dodata['code'] . "'");
                $data[] = $text . ' ' . $dodata['method'];
                break;
            case 'al_admin':
                if (isset($dodata['adminid'])) {
                    $data[] = DB::getDB()->selectval("admin", "uname", "adminid='" . $dodata['adminid'] . "'");
                } elseif (isset($dodata['uname'])) {
                    $data[] = $dodata['uname'];
                }
                break;
            case 'al_role':
                if (isset($dodata['roleid'])) {
                    $data[] = DB::getDB()->selectval("role", "name", "roleid='" . $dodata['roleid'] . "'");
                } elseif (isset($dodata['name'])) {
                    $data[] = $dodata['name'];
                }
                break;
            case 'al_trade':
                $data[] = $dodata['tradeid'];
                break;
            case 'al_payment':
                if (isset($dodata['paymentid'])) {
                    $data[] = DB::getDB()->selectval("payment", "name", "paymentid='" . $dodata['paymentid'] . "'");
                } elseif (isset($dodata['name'])) {
                    $data[] = $dodata['name'];
                }
                break;
            case 'al_exaddr':
                if (isset($dodata['addrid'])) {
                    $data[] = DB::getDB()->selectval("express_addr", "address", "addrid='" . $dodata['addrid'] . "'");
                } elseif (isset($dodata['address'])) {
                    $data[] = $dodata['address'];
                }
                break;
            case 'al_extpl':
                if (isset($dodata['tplid'])) {
                    $data[] = DB::getDB()->selectval("express_tpl", "name", "tplid='" . $dodata['tplid'] . "'");
                } elseif (isset($dodata['name'])) {
                    $data[] = $dodata['name'];
                }
                break;
            case 'al_exway':
                if (isset($dodata['wayid'])) {
                    $data[] = DB::getDB()->selectval("express_way", "name", "wayid='" . $dodata['wayid'] . "'");
                } elseif (isset($dodata['name'])) {
                    $data[] = $dodata['name'];
                }
                break;
            case 'al_excom':
                if (isset($dodata['companyid'])) {
                    $data[] = DB::getDB()->selectval("express_company", "company", "companyid='" . $dodata['companyid'] . "'");
                } elseif (isset($dodata['company'])) {
                    $data[] = $dodata['company'];
                }
                break;
            case 'al_import':
                $data[] = $dodata['num'];
            case 'al_item':
                if (isset($dodata['itemid'])) {
                    $data[] = DB::getDB()->selectval("item", "itemname", "itemid='" . $dodata['itemid'] . "'");
                } elseif (isset($dodata['itemname'])) {
                    $data[] = $dodata['itemname'];
                }
                break;
            case 'al_district':
                if (isset($dodata['districtid'])) {
                    $data[] = DB::getDB()->selectval("district", "district", "districtid='" . $dodata['districtid'] . "'");
                } elseif (isset($dodata['district'])) {
                    $data[] = $dodata['district'];
                }
                break;
            case 'al_coupon':
                if (isset($dodata['couponid'])) {
                    $data[] = DB::getDB()->selectval("coupon", "subject", "couponid='" . $dodata['couponid'] . "'");
                } elseif (isset($dodata['subject'])) {
                    $data[] = $dodata['subject'];
                }
                break;
            case 'al_discount':
                if (isset($dodata['discountid'])) {
                    $data[] = DB::getDB()->selectval("discount", "subject", "discountid='" . $dodata['discountid'] . "'");
                } elseif (isset($dodata['subject'])) {
                    $data[] = $dodata['subject'];
                }
                break;
            case 'al_meal':
                if (isset($dodata['mealid'])) {
                    $data[] = DB::getDB()->selectval("meal", "title", "mealid='" . $dodata['mealid'] . "'");
                } elseif (isset($dodata['title'])) {
                    $data[] = $dodata['title'];
                }
                break;
            case 'al_gift':
                if (isset($dodata['giftid'])) {
                    $data[] = DB::getDB()->selectval("gifts", "subject", "giftid='" . $dodata['giftid'] . "'");
                } elseif (isset($dodata['subject'])) {
                    $data[] = $dodata['subject'];
                }
                break;
            case 'al_man':
                if (isset($dodata['manid'])) {
                    $data[] = DB::getDB()->selectval("man", "subject", "manid='" . $dodata['manid'] . "'");
                } elseif (isset($dodata['subject'])) {
                    $data[] = $dodata['subject'];
                }
                break;
            case 'al_user':
                if (isset($dodata['uid'])) {
                    $data[] = DB::getDB()->selectval("user", "uname", "uid='" . $dodata['uid'] . "'");
                } elseif (isset($dodata['uname'])) {
                    $data[] = $dodata['uname'];
                }
                break;
            case 'al_tlogin':
                if (isset($dodata['tloginid'])) {
                    $data[] = DB::getDB()->selectval("tlogin", "name", "tloginid='" . $dodata['tloginid'] . "'");
                }
            case 'al_link':
                if (isset($dodata['linkid'])) {
                    $data[] = DB::getDB()->selectval("link", "linkname", "linkid='" . $dodata['linkid'] . "'");
                } elseif (isset($dodata['linkname'])) {
                    $data[] = $dodata['linkname'];
                }
                break;
            case 'al_noticearticle':
            case 'al_helparticle':
                if (isset($dodata['contentid'])) {
                    $data[] = DB::getDB()->selectval("content", "subject", "contentid='" . $dodata['contentid'] . "'");
                } elseif (isset($dodata['subject'])) {
                    $data[] = $dodata['subject'];
                }
                break;
            case 'al_noticesort':
            case 'al_helpsort':
                if (isset($dodata['sortid'])) {
                    $data[] = DB::getDB()->selectval("content_sort", "sortname", "sortid='" . $dodata['sortid'] . "'");
                } elseif (isset($dodata['sortname'])) {
                    $data[] = $dodata['sortname'];
                }
                break;
            case 'al_specpic':
                $data[] = basename($dodata['pic']);
                break;
            case 'al_brand':
                if (isset($dodata['brandid'])) {
                    $data[] = DB::getDB()->selectval("brand", "brandname", "brandid='" . $dodata['brandid'] . "'");
                } elseif (isset($dodata['brandname'])) {
                    $data[] = $dodata['brandname'];
                }
            case 'al_spec':
                if (isset($dodata['specid'])) {
                    $data[] = DB::getDB()->selectval("spec", "name", "specid='" . $dodata['specid'] . "'");
                } elseif (isset($dodata['name'])) {
                    $data[] = $dodata['name'];
                }
            case 'al_cat':
                if (isset($dodata['catid'])) {
                    $data[] = DB::getDB()->selectval("cat", "catname", "catid='" . $dodata['catid'] . "'");
                } elseif (isset($dodata['catname'])) {
                    $data[] = $dodata['catname'];
                }
                break;
            case 'al_navi':
                if (isset($dodata['naviid'])) {
                    $data[] = DB::getDB()->selectval("navi", "naviname", "naviid='" . $dodata['naviid'] . "'");
                } elseif (isset($dodata['naviname'])) {
                    $data[] = $dodata['naviname'];
                }
                break;
        }
        $data = array(
            'ip' => getClientIp(),
            'addtime' => time(),
            'adminid' => $adminid,
            'uname' => $uname,
            'oper' => $logname,
            'data' => $data ? serialize($data) : ''
        );
        DB::getDB()->insert("admin_log", $data);
        return true;
    }

    /**
     *
     * 错误
     *
     */
    protected function error()
    {
        $this->data['error'] = '';
        if (isset($_SESSION['adminerror'])) {
            $this->data['error'] = $_SESSION['adminerror'];
            unset($_SESSION['adminerror']);
        }
    }

}
