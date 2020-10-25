<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class AddstrAction extends CommonAction
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
        $build_info = R('Admin/GetModule/checkmodel', array($modelname));
        if ($build_info) {
            $getmodelarray = R('Admin/GetModule/getmodule', array($build_info));
            $tranarr = model_trans_cn($modelname, $getmodelarray);
        } else {
            $tranarr = model_trans_cn($modelname);
        }
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
//            $tranarr=model_trans_cn($modelname);

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
        $transtring = $tranarr;


        //查询自动构建的模块的表的名称并构建下拉框
        $table_define = M('add_module')->Field('model_name,main_table_title')->select();
        $table_custom = array();
        foreach ($table_define as $val) {
            $table_custom[$val["model_name"]] = $val["model_name"] . "  =>  " . $val["main_table_title"];;
        }

        $selectarr_totalnum = count($selectarr);
        $selectarr["str_table"] = $table_custom;
        if (!in_array('str_table', $select_index)) {
            $select_index_totalnum = count($select_index);
            $select_index[$select_index_totalnum] = 'str_table';
        }

        /* echo "transtring=>";   dump($transtring);
         echo "selectarr=>";  dump($selectarr);
         echo "select_index=>";  dump($select_index);
         die();*/


        $this->assign('transtring', $transtring); //标题模块信息
        $this->assign('selectarr', $selectarr); //下拉框数组
        $this->assign('select_index', $select_index); //下拉框字符索引数组

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


    function edit()
    {
        if (method_exists($this, '_before_edit')) {
            $this->_before_edit();
        }
        $model = M($this->getActionName());
        $modelname = $this->getActionName();
        $id = $_REQUEST [$model->getPk()];
        if (!$id) {
            $ids = explode(',', $_REQUEST ['ids']);
            $id = $ids [0];
        }
        if (!$id)
            $this->error("非法或错误ID！");
        $vo = $model->find($id);
        if (!$vo)
            $this->error("信息不存在或已被删除！");

        //判断模块的类型,去下拉框等相关数据
        $build_info = R('Admin/GetModule/checkmodel', array($modelname));

        if ($build_info) {
            $getmodelarray = R('Admin/GetModule/getmodule', array($build_info));
            $tranarr = model_trans_cn($modelname, $getmodelarray);
        } else {
            $tranarr = model_trans_cn($modelname);
        }

        //取中文名称
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


        $selectarr = $tranarr["select_array"];
        $select_index = array();
        foreach ($selectarr as $key => $val) {
            $select_index[] = $key;
        }


        //查询自动构建的模块的表的名称并构建下拉框
        $table_define = M('add_module')->Field('model_name,main_table_title')->select();
        $table_custom = array();
        foreach ($table_define as $val) {
            $table_custom[$val["model_name"]] = $val["model_name"] . "  =>  " . $val["main_table_title"];;
        }

        $selectarr_totalnum = count($selectarr);
        $selectarr["str_table"] = $table_custom;
        if (!in_array('str_table', $select_index)) {
            $select_index_totalnum = count($select_index);
            $select_index[$select_index_totalnum] = 'str_table';
        }


        //传首页等链接在首页的顶端
        $transtring = $tranarr;


        $index_link = U("Admin/$modelname/index");
        $update_link = U("Admin/$modelname/update");

        $this->assign('transtring', $transtring);
        $this->assign('index_link', $index_link);
        $this->assign('update_link', $update_link);
        $this->assign('select_index', $select_index);
        $this->assign('selectarr', $selectarr);

        $this->assign('addarr', $addarr);
        $this->assign('vo', $vo);
        $this->display();
    }


    function addstring()
    {
        $id = $_GET['id'];

        $strinfo = M('addstr')->where("id=$id")->find();

        //查看表是否存在
        $str_table = $strinfo["str_table"];
//        dump($strinfo);
        $str_table_sql = "show tables like '" . C('DB_PREFIX') . $str_table . "'";
        $show_str_table = M()->execute($str_table_sql);
        if ($show_str_table == false) {
            $this->error("你选择的字段所属表: " . $str_table . "不存在!");
        }
        //查看字段是否存在
        $str_name_sql = "desc " . C('DB_PREFIX') . $str_table . "  " . $strinfo["str_name"];
        $show_str_name = M()->execute($str_name_sql);
        if ($show_str_name == true) {
            $this->error("你选择的字段名称: " . $strinfo["str_name"] . "已经存在!");
        }

        //查看提交的注释是否为空
        if (empty($strinfo["str_des"])) {
            $thos->error("字段的标题不可以为空!");
        }
        //开始插入字段
        switch ($strinfo["str_type"]) {
            case "字符型":
                //判断提交的varchar大小是否为整数
                if (is_numeric($strinfo["str_lenth"] == false)) {
                    $this->error("字段的长度只能为整数!");
                }
                $add_sql = "ALTER TABLE " . C('DB_PREFIX') . $str_table . " ADD COLUMN " . $strinfo["str_name"] . "  varchar(" . $strinfo["str_lenth"] . ") DEFAULT '" . $strinfo["str_define"] . "' COMMENT '" . $strinfo["str_des"] . "'";
//                dump($add_sql);die();
                $add_exec = M()->execute($add_sql);
                break;
            case "整型":
                break;

            case "日期时间型":
                break;
        }

        //检查字段是否添加成功
        $str_name_sql = "desc " . C('DB_PREFIX') . $str_table . "  " . $strinfo["str_name"];
        $show_str_name = M()->execute($str_name_sql);
        if ($show_str_name) {
            if ($strinfo["is_success"] == "否") {
                $adddata["id"] = $id;
                $adddata["is_success"] = "是";
                if (M('addstr')->save($adddata)) {
                    $this->success("添加字段并更新状态成功!", U('Admin/Addstr/index'));
                } else {
                    $this->success("添加字段成功,更新状态失败!", U('Admin/Addstr/index'));
                }
            } else {
                $this->success("添加字段并更新状态成功!", U('Admin/Addstr/index'));
            };

        } else {
            $this->error("添加字段到表失败,请检查提交内容是否正确!", U('Admin/Addstr/index'));
        }


    }

//刷新字段信息一键添加到数据表中
    function flushaddstr()
    {
        $map['is_success'] = "否";
        $idadd = M('addstr')->where($map)->field('id')->select();
//        dump($id);die();
        if ($idadd == false) {
            $this->error("没有可以更新的字段!");
        }
        foreach ($idadd as $val) {
            $id = $val['id'];
            $strinfo = M('addstr')->where("id=$id")->find();

            //查看表是否存在
            $str_table = $strinfo["str_table"];
//        dump($strinfo);
            $str_table_sql = "show tables like '" . C('DB_PREFIX') . $str_table . "'";
            $show_str_table = M()->execute($str_table_sql);
            if ($show_str_table == false) {
                $this->error("你选择的字段所属表: " . $str_table . "不存在!");
            }
            //查看字段是否存在
            $str_name_sql = "desc " . C('DB_PREFIX') . $str_table . "  " . $strinfo["str_name"];
            $show_str_name = M()->execute($str_name_sql);
            //只检测没有添加表中的字段
            if ($show_str_name == false) {
                //查看提交的注释是否为空
                if (empty($strinfo["str_des"])) {
                    $thos->error("字段的标题不可以为空!");
                }
                //开始插入字段
                switch ($strinfo["str_type"]) {
                    case "字符型":
                        //判断提交的varchar大小是否为整数
                        if (is_numeric($strinfo["str_lenth"] == false)) {
                            $this->error("字段的长度只能为整数!");
                        }
                        $add_sql = "ALTER TABLE " . C('DB_PREFIX') . $str_table . " ADD COLUMN " . $strinfo["str_name"] . "  varchar(" . $strinfo["str_lenth"] . ") DEFAULT '" . $strinfo["str_define"] . "' COMMENT '" . $strinfo["str_des"] . "'";
//                dump($add_sql);die();
                        $add_exec = M()->execute($add_sql);
                        break;
                    case "整型":
                        break;

                    case "日期时间型":
                        break;
                }

                //检查字段是否添加成功
                $str_name_sql = "desc " . C('DB_PREFIX') . $str_table . "  " . $strinfo["str_name"];
                $show_str_name = M()->execute($str_name_sql);
                if ($show_str_name) {
                    if ($strinfo["is_success"] == "否") {
                        $adddata["id"] = $id;
                        $adddata["is_success"] = "是";
                        if (M('addstr')->save($adddata)) {
                            $this->success("添加字段并更新状态成功!", U('Admin/Addstr/index'));
                        } else {
                            $this->success("添加字段成功,更新状态失败!", U('Admin/Addstr/index'));
                        }
                    } else {
                        $this->success("添加字段并更新状态成功!", U('Admin/Addstr/index'));
                    };

                } else {
                    $this->error("添加字段到表失败,请检查提交内容是否正确!", U('Admin/Addstr/index'));
                }



            } else {
                $this->error("你选择的字段名称: " . $strinfo["str_name"] . "已经存在!");
            }

        }


    }


}

?>