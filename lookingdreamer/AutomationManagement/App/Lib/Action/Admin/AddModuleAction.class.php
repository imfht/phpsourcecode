<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class AddModuleAction extends CommonAction
{

    public function _filter(&$map)
    {
        if (!isset($map['pid'])) {
            $map['pid'] = 0;
        }
        $_SESSION['currentServerDetailId'] = $map['pid'];
        //获取上级节点
        $ServerDetail = D("ServerDetail");
        if ($ServerDetail->getById($map['pid'])) {
            $this->assign('level', $ServerDetail->level + 1);
            $this->assign('parentId', $ServerDetail->pid);
            $this->assign('columnName', $ServerDetail->title);
        } else {
            $this->assign('level', 1);
        }
    }

    public function _tigger_update($model)
    {
        $data = $model->data();
        if ($data['modid'] && $data['name']) {
            $M = M($data['name']);
            $M->where("id='" . $data['modid'] . "'")->data(array("ServerDetailpos" => $data['position']))->save();
        }
    }

    public function add()
    {
        //添加新的数据时赋值给模板,如:赋值添加菜单中  input name="{$name}"其中的$name以及label标签的中文名,中文名来自字段的注释。
        $modelname = $this->getActionName();
        $tb = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $modelname)); //正则:驼峰转换为下划线
        $Info = M("columns", "", "DB_INFO");
        // 查询字段和注释对应关系
        $intb = C('DB_PREFIX') . $tb;
        $map ['table_name'] = $intb;
        $xlsCell = $Info->where($map)->table("columns")->field("column_name, column_comment")->select();

        $addarr = array();
        foreach ($xlsCell as $add) {
//               echo $add["column_comment"]."||". $add["column_name"]."<br/>";
            $keyval = $add["column_comment"];
            $addarr["$keyval"] = $add["column_name"];
        }
        $tranarr = model_trans_cn($modelname);
        $selectarr = $tranarr["select_array"];

        //转换索引为索引数组
        $select_index = array();
        foreach ($selectarr as $key => $val) {
            $select_index[] = $key;
        }
        $index_link = U("Admin/$modelname/index");
        $insert_link = U("Admin/$modelname/insert");

        $nowtime = toDate(NOW_TIME, $format = 'Y-m-d H:i:s');
        //传首页等链接在首页的顶端
        $transtring = model_trans_cn($modelname);

        $this->assign('transtring', $transtring);
        $this->assign('selectarr', $selectarr);
        $this->assign('select_index', $select_index);
        $this->assign('index_link', $index_link); //取消按钮链接
        $this->assign('insert_link', $insert_link); //提交按钮链接
        $this->assign('nowtime', $nowtime); //初始时间
        $this->assign('addlist', $addarr); //初始模板数据
        $this->display();
    }

    /**
     * +----------------------------------------------------------
     * 默认排序操作
     * +----------------------------------------------------------
     * @access public
     * +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    public function sort()
    {
        $node = M('ServerDetail');
        if (!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id'] = array('in', $_GET['sortId']);
            $sortList = $node->where($map)->order('sort asc')->select();
        } else {
            if (!empty($_GET['pid'])) {
                $pid = $_GET['pid'];
            } else {
                $pid = $_SESSION['currentServerDetailId'];
            }
            if ($node->getById($pid)) {
                $level = $node->level + 1;
            } else {
                $level = 1;
            }
            $this->assign('level', $level);
            $sortList = $node->where('status=1 and pid=' . $pid . ' and level=' . $level)->order('sort asc')->select();
        }
        $this->assign("sortList", $sortList);
        $this->display();
        return;
    }

    // 缓存配置文件
    public function cache()
    {
        $ServerDetail = M("ServerDetail");
        $templist = $ServerDetail->where('status=1')->order('position DESC, sort ASC')->select();
        foreach ($templist as $val) {
            if ($val['modid']) {
                $Modules[$val['name']][] = $val['modid'];
            }
        }
        if ($Modules) {
            foreach ($Modules as $key => $ids) {
                $M = M($key);
                $list = $M->where("id IN(" . implode(",", $ids) . ")")->select();
                foreach ($list as $vo) {
                    $ModList[$key][$vo['id']] = $vo;
                }
            }
        }
        foreach ($templist as $val) {
            $val['module'] = $val['name'];
            if ($val['modid']) {
                $vo = $ModList[$val['name']][$val['modid']];
                if ($vo) {
                    $val['moduledata'] = $vo;
                } else {
                    continue;
                }
            }
            $list[$val['id']] = $val;
            $ServerDetaillist[$val['position']][$val['id']] = $val;
        }
        $savefile = DATA_PATH . '~ServerDetail.php';
        // 所有配置参数统一为大写
        $content = "<?php\nreturn " . var_export($list, true) . ";\n?>";
        $iscache = file_put_contents($savefile, $content);
        foreach ($ServerDetaillist as $key => $val) {
            $savefile = DATA_PATH . '~ServerDetail_' . $key . '.php';
            // 所有配置参数统一为大写
            $content = "<?php\nreturn " . var_export($val, true) . ";\n?>";
            $iscache = file_put_contents($savefile, $content);
        }
        if ($iscache) {
            $this->success('缓存生成成功！');
        } else {
            $this->error('缓存失败！');
        }

    }


    public function buildmodel()
    {
        //根据传参获取相关信息
        $id = $_GET["id"];
        if (empty($id)) {
            $this->error("传参失败!");
        }
        //取模块表信息
        $add_module = M('add_module');
        $mapid = array('id' => $id);
        $build_info = $add_module->where($mapid)->find();
        if ($build_info == false) {
            $this->error("在构建模块表中查询失败,请确认该模块的相关配置已经正确!");
        }
        //取下拉框信息
        if ($build_info['drop_down_id']) {
            $drop_down = M('drop_down');
            $mapdown = array('id' => $build_info['drop_down_id']);
            $drop_down_info = $drop_down->where($mapdown)->select();
            $build_info['drop_down'] = $drop_down_info;
        }
//        dump($build_info);die();

        //验证构建模块的数据表是否存在
        $Info = M("tables", "", "DB_INFO");
        $map ['table_name'] = C('DB_PREFIX') . $build_info['model_name'];
        $map['table_schema'] = C('DB_NAME');
        $table_check = $Info->where($map)->table('tables')->select();
        if ($table_check == false) {
            $this->error($build_info['model_name'] . "该表不存在,请确认并重新创建！");
        }

        //--复制模板文件
        $modelclass_file = LIB_PATH . "Action/" . GROUP_NAME . "/" . ucfirst($build_info['model_name']) . "Action.class.php";
//        dump($modelclass_file);
        $Hostclass_file = LIB_PATH . "Action/" . GROUP_NAME . "/" . "HostZzbAction.class.php";
//        dump($Hostclass_file);
        if (!file_exists($Hostclass_file)) {
            $this->error("不存在标配模板文件" . $Hostclass_file);
        }
        if (file_exists($modelclass_file)) {
            $this->error("已经存在模板文件" . $modelclass_file . "请重新修改模块的名称!");
        }

        if (!copy($Hostclass_file, $modelclass_file)) {
            $this->error("复制模板文件失败" . " $Hostclass_file" . "=>" . $modelclass_file);
        }
        //替换的class类的名称
        $sed_name = ucfirst($build_info['model_name']);
        //查找字符
        //读取文件
        $file_name = $modelclass_file;
        $fp = fopen($file_name, 'r');
        $file_pointer = fopen("tmpclass.php", "a+");
        while (!feof($fp)) {
            $buffer = fgets($fp, 4096);
            //替换文件
            $buffer = str_replace("HostZzb", "$sed_name", $buffer);
            fwrite($file_pointer, $buffer);

//            echo $buffer."<br />";
        }
        fclose($fp);
        fclose($file_pointer);
        if (!rename("tmpclass.php", $modelclass_file)) {
            $this->error("复制替换后模板文件失败" . " tmpclass.php" . "=>" . $modelclass_file);
        }

        //--复制tpl模板文件夹
        $model_tpl_dir = TMPL_PATH . GROUP_NAME . "/" . C('DEFAULT_THEME') . "/" . ucfirst($build_info['model_name']);
        $host_tpl_dir = TMPL_PATH . GROUP_NAME . "/" . C('DEFAULT_THEME') . "/HostZzb";
        if (file_exists($model_tpl_dir)) {
            $this->error(ucfirst($build_info['model_name']) . "Tpl模板文件已经存在,请确认是否已经创建!");
        } else {
            R('Admin/File/copyDir', array("$host_tpl_dir", "$model_tpl_dir"));


        }

        if (file_exists($modelclass_file) && file_exists($model_tpl_dir)) {
            $this->success("类库文件和模板文件创建成功!", U('Admin/AddModule/index'));
        }


    }


    function createtable()
    {

        $tablename = $_POST["tablename"];
        $tablename = C('DB_PREFIX').$tablename;
        if ($tablename) {
            //判断该数据表是否已经存在!
            $judgesql = "desc $tablename ";
            $judgetable = M()->execute($judgesql);
            if ($judgetable) {
                $this->error("该数据表[".$_POST["tablename"]."]已经存在,请重新创建!");
            }


            $ctabsql = "CREATE TABLE $tablename(
   id int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  created_time datetime DEFAULT NULL COMMENT '创建时间',
  updated_time datetime DEFAULT NULL COMMENT '更新记录的时间',
  status varchar(64) DEFAULT '开启' COMMENT '使用状态',
  note varchar(128) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8";

            M()->execute($ctabsql);

            if ( M()->execute($judgesql)) {
                $data['model_name']=$_POST["tablename"];
                $data['created_time']=  toDate(NOW_TIME, $format = 'Y-m-d H:i:s');
                $data['updated_time']=  toDate(NOW_TIME, $format = 'Y-m-d H:i:s');
                $id=M('add_module')->add($data);
                $datanext['drop_down_id']=$id;
                $datanext['id']=$id;
                $datanext['model_tr_string']="id,created_time,updated_time,status,note";


                if(M('add_module')->save($datanext)){
                    $this->success("创建数据表并插入模块数据成功!",U('Admin/AddModule/index'));
                    exit();
                }


                $this->success("创建数据表成功 And 插入模块数据失败!",U('Admin/AddModule/index'));
            } else {
                $this->error("创建数据表失败,请检查表名是否规范!");
            }


        } else {

            $this->error("数据表的名称不能为空!");
        }


    }


}

?>