<?php

/**
 *  
 * 导入
 *
 *
 * */
class Import extends Base
{

    /**
     *  
     * 首页
     *
     *
     * */
    public function index()
    {
        if (ispostreq()) {
            $file = trim($_GET["file"]);
            $version = trim($_GET["version"]);
            $catid = trim($_GET["catid"]);
            $total = trim($_GET["total"]);

            if (preg_match("/^taobao([0-9]+)/i", $version, $match)) {
                @set_time_limit(0);
                @ini_set("memory_limit", '256M');
                $queueid = isset($_GET["queueid"]) ? intval($_GET["queueid"]) : 1;
                $taobao = new Importtaobao($match[1]);
                $ret = $taobao->import($file, $queueid, $catid, $total);
                if ($ret) {//如果成功
                    if ($taobao->getFinish()) { //导入结束
                        $taobao->delfile();
                        $this->adminlog("al_import", array("do" => "taobao", "num" => $taobao->getTotal()));
                        echo __("import_item_finished_total", $taobao->getTotal()) . "<script>setTimeout(function(){ window.location.reload() },1000)</script>";
                    } else {//导入下一条
                        $queueid++;
                        $url = url("admin", "import", "index", "file={$file}&version={$version}&catid={$catid}&queueid={$queueid}&total=" . $taobao->getTotal(), false);
                        echo __("import_item", $queueid) . "<script>$.oper.runjs('{$url}')</script>";
                    }
                } else {
                    echo $taobao->getError();
                }
            }
        } else {
            $this->data["catopt"] = $this->getCatOption(null, 0, 0, true);
            $this->data['left_cur'] = "import_index";
            $importdir = DATADIR . '/import';
            $files = glob($importdir . "/*.csv");
            if ($files) {
                foreach ($files as $k => $file) {
                    $files[$k] = basename($file, ".csv");
                }
                $this->data['fileopt'] = array2select($files, 'val', 'val');
            }
            $this->output("import_index");
        }
    }

}
