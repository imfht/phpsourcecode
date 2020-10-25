<?php

require_once THIRDPATH . '/taobao/taobao.class.php';

/**
 *  
 * 导入淘宝
 *
 *
 * */
class Importtaobao
{

    private $taoclient;
    private $filename;
    private $importdir;
    private $uploaddir;
    private $error;
    private $finish = false;
    private $map = array(
        "50" => array("title" => 0, "cid" => 1, "price" => 7, "inventory" => 9, "description" => 20, "prop" => 21, "img" => 28,
            "saleprop" => 30, "selfkey" => 31, "selfval" => 32, "bn" => 33, "outerid" => 36),
        "47" => array("title" => 0, "cid" => 1, "price" => 7, "inventory" => 9, "description" => 25, "prop" => 27, "img" => 36,
            "saleprop" => 38, "selfkey" => 39, "selfval" => 40, "bn" => 41, "outerid" => 45)
    );
    private $version;
    public $total = 0;

    /**
     *  
     * 构造函数
     *
     *
     * */
    public function __construct($version)
    {
        $appkey = getConfig("taobao_key", "12308347");
        $appsecret = getConfig("taobao_secret", "a2a13b80227fa9bc492468b24d575465");

        $this->taoclient = new TaoClient($appkey, $appsecret);

        $this->importdir = DATADIR . '/import/';
        list($year, $month, $day) = explode("-", date("Y-m-d"));
        $this->uploaddir = SITEPATH . '/uploads/' . $year . '/' . $month . '/' . $day . "/";
        $this->version = $version;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function import($filename, $queueid, $catid = 0, $total = 0)
    {
        $this->total = $total;
        if (!$this->map[$this->version]) {
            $this->error = __("cannot_import_theversion");
            return false;
        }

        $this->filename = $filename;
        $file = $this->importdir . $this->filename . '.csv';
        if (!$file) {
            $this->error = __("no_import_csv_file");
            return false;
        }

        $filelines = file($file);
        if (!isset($filelines[$queueid])) {
            $this->finish = true;
            return true;
        }
        $itemstr = $filelines[$queueid];
        if (!$itemstr)
            return true;
        return $this->importitem($itemstr, $catid);
    }

    public function delfile()
    {
        $file = $this->importdir . $this->filename . '.csv';
        @chmod($file, 0777);
        @unlink($file);
        deldir($this->importdir . $this->filename);
    }

    /**
     *  
     * 返回错误
     *
     *
     * */
    public function getError()
    {
        return $this->error;
    }

    /**
     *  
     * 返回是否结束
     *
     *
     * */
    public function getFinish()
    {
        return $this->finish;
    }

    /**
     *  
     * 导入淘宝商品
     *
     *
     * */
    public function importitem($itemstr, $catid)
    {
        $time = time();
        $iteminfo = explode("\t", $itemstr);
        $cid = isset($iteminfo[$this->map[$this->version]['cid']]) ? $iteminfo[$this->map[$this->version]['cid']] : 0;
        if (!$cid || !preg_match("/[0-9]+/", $cid))
            return true; //如果不是正确的记录，直接返回true



            
//如果已经存在相应的商品类型
        $type = DB::getDB()->selectrow("type", "*", "tbcid='$cid'");
        $the_properties = $the_values = $the_specs = $the_specvals = array();
        $typeid = 0;
        if (!$type) {
            $typeid = $this->importcid($cid);
            if (!$typeid)
                return false;
        } else {
            $typeid = $type['typeid'];
        }
        $where = "typeid='$typeid'";

        //map本地表与淘宝的pid,vid
        $the_properties = DB::getDB()->selectkv("type_property", "tbpid", "propertyid", $where);
        $the_values = DB::getDB()->selectkv("type_propertyvalue", "tbvid", "valueid", $where);
        $specids = DB::getDB()->selectcol("type_spec", "specid", $where);
        if ($specids) {
            $where = "specid in " . cimplode($specids);
            $the_specs = DB::getDB()->selectkv("spec", "tbpid", "specid", $where);
            $the_specvals = DB::getDB()->select("specval", "tbvid,specvalid,name", $where, "", "", "tbvid");
        }

        //解析淘宝基本信息
        $itemname = trim($iteminfo[$this->map[$this->version]['title']], '"');
        $price = getPrice($iteminfo[$this->map[$this->version]['price']], 2, 'int');
        $inventory = intval($iteminfo[$this->map[$this->version]['inventory']]);
        $imgstr = trim($iteminfo[$this->map[$this->version]['img']], '";');

        //导入淘宝图片，复制文件，
        $imgs = explode(";", $imgstr);
        foreach ($imgs as $k => $img) {
            $imgs[$k] = substr($img, 0, strpos($img, ":"));
        }
        $this->importimg($imgs);
        $itemimg = $imgs[0];

        //入商品库
        $bn = trim($iteminfo[$this->map[$this->version]['bn']], '";');
        $itemid = DB::getDB()->insert("item", caddslashes(array("itemname" => $itemname, "price" => $price,
            "inventory" => $inventory,
            "itemimg" => str_replace(SITEPATH . '/', '', $this->uploaddir) . $itemimg . ".jpg",
            "typeid" => $typeid,
            "bn" => $bn,
            "outer" => 'taobao',
            "outerid" => trim($iteminfo[$this->map[$this->version]['outerid']], '";'),
            "status" => 1,
            "created" => $time, "modified" => $time)));
        $this->total++;
        //$itemid = 0;
        if (!$itemid) {
            $this->error = __('import_item_error');
            return false;
        }
        if ($catid) {
            DB::getDB()->insert("item_cat", array("itemid" => $itemid, "catid" => $catid));
        }

        //入商品描述
        DB::getDB()->insert("item_desc", caddslashes(array("itemid" => $itemid, "itemdesc" => trim(str_replace("\"\"", "\"", $iteminfo[$this->map[$this->version]['description']]), '"'))));
        $iteminfo[$this->map[$this->version]['description']] = '';


        //入商品主图
        $imgdata = array();
        foreach ($imgs as $key => $img) {
            $imgdata[] = caddslashes(array("itemid" => $itemid, "imgpath" => str_replace(SITEPATH . '/', '', $this->uploaddir) . $img . ".jpg", "order" => $key + 1));
        }
        if ($imgdata)
            DB::getDB()->insertMulti("item_img", $imgdata);


        $selfkeys = $iteminfo[$this->map[$this->version]['selfkey']] ? explode(",", trim($iteminfo[$this->map[$this->version]['selfkey']], ',";')) : array();
        $selfvals = $iteminfo[$this->map[$this->version]['selfval']] ? explode(",", trim($iteminfo[$this->map[$this->version]['selfval']], ',";')) : array();
        $selfs = array();
        if ($selfkeys) {
            foreach ($selfkeys as $key => $val) {
                $selfs[$val] = $selfvals[$key];
            }
        }
        $propertydata = $specdata = array();

        //如果存在销售属性
        if (strlen($iteminfo[$this->map[$this->version]['saleprop']]) > 4) {
            $tempspecstr = trim($iteminfo[$this->map[$this->version]['saleprop']], '";');
            $tempspecs = explode(";", $tempspecstr);

            //校正
            $pricestr = substr($tempspecstr, 0, strpos($tempspecstr, ":") + 1);
            foreach ($tempspecs as $k => $tempspec) {
                if (!preg_match("/^$pricestr/", $tempspec)) { //如果不含有pricestr，处理下
                    $tempspecs[$k - 1] .= ';' . $tempspec;
                    unset($tempspecs[$k]);
                }
            }
            $specself = array();
            foreach ($tempspecs as $tempspec) {
                if (!preg_match("/(.+?):([0-9]+?):(.*?)?:(.+)/", $tempspec, $match))
                    continue;
                $price = getPrice($match[1], 2, 'int');
                if (!$price)
                    continue;
                $inventory = intval($match[2]);
                $pbn = trim($match[3]);
                $specstr = trim($match[4], ";");

                $product = array("itemid" => $itemid, "inventory" => $inventory, "price" => $price, "bn" => $pbn ? $pbn : $bn);
                $productid = DB::getDB()->insert("product", $product);
                //echo $specstr , "\r\n";

                $thespecs = explode(";", $specstr);
                foreach ($thespecs as $spec) {
                    list($specid, $specvalid) = explode(":", $spec);
                    if (!$specid || !$specvalid || !isset($the_specs[$specid]) || !isset($the_specvals[$specvalid]))
                        continue;

                    $thespecid = $the_specs[$specid];
                    $thespecvalid = $the_specvals[$specvalid]['specvalid'];
                    $specdata[] = caddslashes(array("specid" => $thespecid,
                        "specvalid" => $thespecvalid,
                        "itemid" => $itemid,
                        "productid" => $productid,
                        "typeid" => $typeid));
                    $specself[$thespecid]['text'][$thespecvalid] = $the_specvals[$specvalid]['name'];
                }
            }

            if ($specdata)
                DB::getDB()->insertMulti("product_spec", $specdata);
            if ($specself) {
                $selfdata = array();
                foreach ($specself as $specid => $self) {
                    $selfdata[] = caddslashes(array("specid" => $specid, "self" => serialize($self), "itemid" => $itemid));
                }
                DB::getDB()->insertMulti("item_spec", $selfdata);
            }
        }

        //如果存在属性
        if (strlen($iteminfo[$this->map[$this->version]['prop']]) > 4) {
            $propertystr = trim($iteminfo[$this->map[$this->version]['prop']], '";');
            $properties = explode(";", $propertystr);


            foreach ($properties as $val) {
                list($property, $value) = explode(":", $val);
                //echo $property , "=>" , $value,"<br />";

                if ($property && $value) {
                    //判断是否规格
                    if (isset($the_properties[$property])) {//属性
                        $valueid = isset($the_values[$value]) ? $the_values[$value] : 0;
                        $self = '';
                        if (!$valueid) {
                            $self = $selfs[$property];
                        }

                        $propertydata[] = caddslashes(array("itemid" => $itemid,
                            "propertyid" => $the_properties[$property],
                            "propertyvalueid" => $valueid,
                            "self" => $self));
                        unset($the_properties[$property]);
                    } else if (isset($the_specs[$property])) {//规格
                        //					$the_property = $the_specs[$property];
                        //					$the_value	  = isset($the_specvals[$value])?$the_specvals[$value]:0;
                    }
                }
            }
            //self手写的
            foreach ($selfs as $k => $v) {
                if (isset($the_properties[$k])) {//如果存在相关的
                    $propertydata[] = caddslashes(array("itemid" => $itemid,
                        "propertyid" => $the_properties[$k],
                        "propertyvalueid" => 0,
                        "self" => $v));
                    unset($the_properties[$k]);
                }
            }
            if ($propertydata)
                DB::getDB()->insertMulti("item_property", $propertydata);
        }

        return true;
    }

    /**
     *  
     * 导入图片
     *
     *
     * */
    public function importimg($imgs)
    {
        if (!file_exists($this->uploaddir) && !remkdir($this->uploaddir)) {
            return false;
        }
        foreach ($imgs as $img) {
            $source = $this->importdir . $this->filename . "/$img.tbi";

            if (!$img || !file_exists($source)) {
                continue;
            }
            $dest = $this->uploaddir . "$img.jpg";
            if (@copy($source, $dest)) {
                require_once COMMONPATH . "/image.class.php";
                foreach (array("50", "160", "310") as $k => $size) {
                    Image::thumb($dest, "{$dest}_{$size}x{$size}.jpg", $size, $size);
                }
            }
        }
    }

    /**
     *  
     * 导入淘宝cid
     *
     *
     * */
    public function importcid($cid)
    {
        $request = array(
            "method" => "taobao.itemcats.get",
            "paras" => array(
                "fields" => "name,cid",
                "cids" => $cid
            )
        );
        $req = $this->taoclient->execute($request);

        if (!isset($req['itemcats_get_response'])) {
            $this->error = __("import_tb_cid_error", $cid);
            return false;
        }
        $cat = current($req['itemcats_get_response']['item_cats']['item_cat']);
        ;


        $request = array(
            "method" => "taobao.itemprops.get",
            "paras" => array(
                "fields" => "pid,name,prop_values,is_sale_prop",
                "cid" => $cid
            )
        );
        $req = $this->taoclient->execute($request);

        if (!isset($req['itemprops_get_response'])) {
            $this->error = __("import_tb_cid_error", $cat['name']);
            return false;
        }

        //插入type表
        $typeid = DB::getDB()->insert("type", caddslashes(array("typename" => $cat['name'], "tbcid" => $cid)));

        if (!isset($req['itemprops_get_response']['item_props']['item_prop']))
            return $typeid; //不存在属性需要导入

        $props = $req['itemprops_get_response']['item_props']['item_prop'];
        $order2 = $order = 1;
        $property = array();
        foreach ($props as $prop) {
            if ($prop['is_sale_prop']) {//如果是销售属性，添加到规格中,都为文本型的
                //入规格表
                $specid = DB::getDB()->insert("spec", caddslashes(array("name" => $prop['name'], "memo" => $cat['name'], "type" => "text", "tbpid" => $prop['pid'])));
                if (isset($prop['prop_values']['prop_value'])) {
                    foreach ($prop['prop_values']['prop_value'] as $key => $val) {
                        $specval = caddslashes(array("specid" => $specid, "name" => $val['name'], "tbvid" => $val['vid'], "order" => $key + 1));
                        //入规格值表
                        $specvalid = DB::getDB()->insert("specval", $specval);
                    }
                }
                //入类型规格关系表
                DB::getDB()->insert("type_spec", caddslashes(array("typeid" => $typeid, "specid" => $specid, "specdptype" => 1, "order" => $order2++)));
            } else {//如果不是销售属性，添加到属性中
                $selval = array();
                $tbvids = array();
                if (isset($prop['prop_values']['prop_value'])) {
                    foreach ($prop['prop_values']['prop_value'] as $val) {
                        $name = str_replace(",", "", $val['name']);
                        $selval[] = $name;
                        $tbvids[] = $val['vid'];
                    }
                }
                $property = array(
                    "propertyname" => $prop['name'],
                    "dptype" => isset($prop['prop_values']['prop_value']) ? 2 : 3, //默认不允许过滤
                    "selval" => $selval ? implode(",", $selval) : '',
                    "order" => $order++,
                    "isdp" => 1, //默认都显示
                    "typeid" => $typeid,
                    "tbpid" => $prop['pid']
                );
                //加入propertyid
                $propertyid = DB::getDB()->insert("type_property", caddslashes($property));

                //加入propertyvalueid
                foreach ($selval as $key => $val) {
                    $propertyvalue = array("propertyid" => $propertyid,
                        "propertyvalue" => $val,
                        "tbvid" => $tbvids[$key],
                        "typeid" => $typeid,
                        "order" => $key + 1);
                    $valueid = DB::getDB()->insert("type_propertyvalue", caddslashes($propertyvalue));
                }
            }
        }

        return $typeid;
    }

}
