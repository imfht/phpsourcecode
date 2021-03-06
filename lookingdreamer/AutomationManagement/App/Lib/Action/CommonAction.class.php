<?php

/*
 * This is NOT a freeware, use is subject to license terms [SEOPHP] (C) 2012-2015 QQ:224505576 SITE: http://seophp.taobao.com/
 */

class CommonAction extends Action
{
    public function _initialize()
    {
        C('SHOW_RUN_TIME', false); // 运行时间显示
        C('SHOW_PAGE_TRACE', false);
        $this->checkUser();
        $modfile = DATA_PATH . '~modulelist.php';
        if (file_exists($modfile))
            $modules = include($modfile);
        if ($_GET ['module']) {
            $li_current [strtolower($_GET ['module'] . '_' . MODULE_NAME . ($_GET ['newtype'] ? '_' . $_GET ['newtype'] : ''))] = ' class="current"';
            $li_active [strtolower($_GET ['module'])] = ' class="active"';
            $a_current [strtolower($_GET ['module'])] = ' id="current"';
        } else {
            $li_current [strtolower(MODULE_NAME . '_' . ACTION_NAME . ($_GET ['newtype'] ? '_' . $_GET ['newtype'] : ''))] = ' class="current"';
            $li_active [strtolower(MODULE_NAME)] = ' class="active"';
            $a_current [strtolower(MODULE_NAME)] = ' id="current"';
        }


        //获取一级菜单
        $first["level"] = 1;
        $first["status"] = array('neq', 0);
        $firstmenu = M('menus')->where($first)->order("sort")->field("id,title,display")->select();
        //获取二级菜单
        $second["level"] = 2;
        $second["status"] = array('neq', 0);
        $secondmenu = M('menus')->where($second)->order("sort")->field("id,name,title,pid,link,target,display")->select();
        $nextmenu = array();
        foreach ($secondmenu as $key => $val) {

            if ($val["link"]) {
                $urlname = $val["link"];
                $val["url"] = $val["link"] ;
                $nextmenu[] = array(
                    "url" => $val["url"],
                    "name" => $val["name"],
                    "title" => $val["title"],
                    "pid" => $val["pid"],
                    "ulinkrl" => $val["ulinkrl"],
                    "target" => $val["target"],
                    "display" => $val["display"],
                );
            } else {
                $urlname = "Admin/" . $val["name"] . "/index";
                $val["url"] = U("$urlname", '', '', '', true);
                $nextmenu[] = array(
                    "url" => $val["url"],
                    "name" => $val["name"],
                    "title" => $val["title"],
                    "pid" => $val["pid"],
                    "ulinkrl" => $val["ulinkrl"],
                    "target" => $val["target"],
                    "display" => $val["display"],
                );
            }
        }

        foreach ($firstmenu as $key => $fir) {
            foreach ($nextmenu as $sec) {
                if ($fir["id"] == $sec["pid"]) {
                    $firstmenu["$key"]["second"][] = $sec;
                }
            }

        }

       //dump($firstmenu);die();

        $this->assign('firstmenu', $firstmenu);
        $this->assign('li_current', $li_current);
        $this->assign('li_active', $li_active);
        $this->assign('a_current', $a_current);
        if ($this->_post()) {
            $inputKeys = array(
                'title_in_keywords',
                'title_in_description',
                'keywords_in_title',
                'keywords_in_description',
                'seokey_in_keywords',
                'seokey_in_description',
                'seokey_in_title',
                'urlwords_in_keywords',
                'urlwords_in_description',
                'urlwords_in_title',
                'is_title_in_url',
                'is_title_to_pinyin'
            );
            foreach ($inputKeys as $val) {
                $_POST [$val] = intval($_POST [$val]);
            }
        }
        $this->assign('modules', $modules);
        $this->getDefault();
        if (!empty ($_GET ["action_name"]) && method_exists($this, $_GET ["action_name"] . '_' . ACTION_NAME)) {
            $actionMethod = $_GET ["action_name"] . '_' . ACTION_NAME;
            $this->$actionMethod ();
            exit ();
        }
    }

    public function checkUser()
    {
        if (!session(C('USER_AUTH_KEY'))) {
            redirect(__APP__ . C('USER_AUTH_GATEWAY'));
        } else {
            $this->admin_userId = cookie('admin_loginId');
            $this->admin_nickname = cookie('admin_nickname');
        }
    }

    // 缓存文件

    public function getDefault()
    {
        $charts = array(
            'total_spider' => array(
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0
            ),
            'total_count' => array(
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0
            ),
            'total_user' => array(
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0
            )
        );
        // S('Default_links',null);
        // S('Default_siteinfo',null);
        $LinkModel = M('Link');
        $time = strtotime(date('Y-m-d'));
        $linklist = $LinkModel->order("linktype DESC, baidu_index DESC, update_time DESC")->where("status=1")->limit(20)->select();
        $this->assign('defaultlinks', $linklist);
        $LogModel = M('Todaylog');
        $logs = $LogModel->order("create_time DESC")->where("1")->limit(10)->select();
        $count = 0;
        foreach ($logs as $val) {
            if (!$siteinfo)
                $siteinfo = $val;
            $charts ['total_spider'] [$count] = $val ['total_spider'];
            $charts ['total_count'] [$count] = $val ['total_count'];
            $charts ['total_user'] [$count] = $val ['total_user'];
            $count++;
        }
        $sitechart ['spider_sum'] = array_sum($charts ['total_spider']);
        $sitechart ['count_sum'] = array_sum($charts ['total_count']);
        $sitechart ['user_sum'] = array_sum($charts ['total_user']);
        $sitechart ['spider'] = implode(',', $charts ['total_spider']);
        $sitechart ['count'] = implode(',', $charts ['total_count']);
        $sitechart ['user'] = implode(',', $charts ['total_user']);
        $this->assign('sitechart', $sitechart);
        $this->assign('siteinfo', $siteinfo);
    }

    public function cache($name = '', $fields = '')
    {
        $name = $name ? $name : $this->getActionName();
        $Model = M($name);
        $list = $Model->limit(20)->select();
        $data = array();
        foreach ($list as $key => $val) {
            if (empty ($fields)) {
                $data [$val [$Model->getPk()]] = $val;
            } else {
                // 获取需要的字段
                if (is_string($fields)) {
                    $fields = explode(',', $fields);
                }
                if (count($fields) == 1) {
                    $data [$val [$Model->getPk()]] = $val [$fields [0]];
                } else {
                    foreach ($fields as $field) {
                        $data [$val [$Model->getPk()]] [] = $val [$field];
                    }
                }
            }
        }
        $savefile = $this->getCacheFilename($name);
        // 所有参数统一为大写
        $content = "<?php\nreturn " . var_export(array_change_key_case($data, CASE_UPPER), true) . ";\n?>";
        if (file_put_contents($savefile, $content)) {
            $this->success('缓存生成成功！');
        } else {
            $this->error('缓存失败！');
        }
    }

    protected function getCacheFilename($name = '')
    {
        $name = $name ? $name : $this->getActionName();
        return DATA_PATH . '~' . strtolower($name) . '.php';
    }

    public function index()
    {
        // 列表过滤器，生成查询Map对象
        $map = $this->_search();
        /*
         * if(method_exists($this,'_filter')) { $this->_filter($map); }
         */
        $model = M($this->getActionName());
        if (!empty ($model)) {
            $this->_list($model, $map);
        }
        $this->display();
        return;
    }

    /**
     * +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     * +----------------------------------------------------------
     *
     * @access protected
     *         +----------------------------------------------------------
     * @param string $name
     *            数据对象名称
     *            +----------------------------------------------------------
     * @return HashMap +----------------------------------------------------------
     * @throws ThinkExecption +----------------------------------------------------------
     */
    protected function _search($name = '')
    {
        // 生成查询条件
        if (empty ($name)) {
            $name = $this->getActionName();
        }
        $model = M($name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (substr($key, 0, 1) == '_')
                continue;
            if (isset ($_REQUEST [$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }

    /**
     * +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     * +----------------------------------------------------------
     *
     * @access protected
     *         +----------------------------------------------------------
     * @param Model $model
     *            数据对象
     * @param HashMap $map
     *            过滤条件
     * @param string $sortBy
     *            排序
     * @param boolean $asc
     *            是否正序
     *            +----------------------------------------------------------
     * @return void +----------------------------------------------------------
     * @throws ThinkExecption +----------------------------------------------------------
     */
    protected function _list($model, $map = array(), $sortBy = '', $asc = false, $show = "false")
    {

        // 排序字段 默认为主键名
        if (isset ($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty ($sortBy) ? $sortBy : $model->getPk();
        }
        // 排序方式默认按照倒序排列
        // 接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset ($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            // $sort = $asc?'asc':'desc';
            $sort = $asc ? 'desc' : 'asc';
        }
        $whereArr [] = 1;
        if (is_array($map)) {
            foreach ($map as $key => $val) {
                $whereArr [] = $key . "='" . $val . "'";
            }
        } else {
            $whereArr [] = $map;
        }
        $modelname = $this->getActionName();
        $build_info = R('Admin/GetModule/checkmodel', array($modelname));
        if ($build_info) {
            $getmodelarray = R('Admin/GetModule/getmodule', array($build_info));
//            dump($getmodelarray);die();
            $transtring = model_trans_cn($modelname, $getmodelarray);
        } else {
            $transtring = model_trans_cn($modelname);
        }

        // 根据搜索得出结果
        if ($_GET ['search']) {
            $string = $_GET ['search'];
            $table_tr = $transtring ["main_table_tr"];
            foreach ($table_tr as $val) {
                $mapseach ["$val"] = array(
                    'like binary',
                    "%$string%"
                );
            }
            $mapseach ['_logic'] = 'or';
            $sql = $mapseach;
            //var_dump($sql); 
        } else {
            if ($_GET ['searchname']) {
                $table_string = transform_to_string($modelname, $_GET ['searchname']);
            } else {
                $table_string = "server_name";
            }
            if ($_GET ['searchtitle'])
                $whereArr [] = "$table_string LIKE '%" . $_GET ['searchtitle'] . "%'";

            $sql = implode(" AND ", $whereArr);
        }

        //dump($sql);
        // 取得满足条件的记录数
        $count = $model->where($sql)->count('id');
        import("ORG.Util.Page");
        // 创建分页对象
        if (!empty ($_REQUEST ['listRows'])) {
            $listRows = $_REQUEST ['listRows'];
        } else {
            $listRows = C('PER_PAGE');
        }
        $p = new Page ($count, $listRows);
        // $p->setConfig ( 'theme', '<div class="dataTables_info">%totalRow% %header% %nowPage%/%totalPage% 页</div><div class="dataTables_paginate paging_full_numbers">%upPage%%downPage%%first%%prePage%%linkPage%%nextPage%%end%</div>' );
        // $p->setConfig ( 'theme', '<div class="dataTables_info">%totalRow% %header% %nowPage%/%totalPage% 页</div><div class="dataTables_paginate paging_full_numbers">%upPage%%downPage%%first%%prePage%%linkPage%%nextPage%%end%</div>' );
        // 分页查询数据
       // dump($sql);    
        $list = $model->where($sql)->order($order . ' ' . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
         //dump($model->getLastSql());
        // $list = $model->where($sql)->order($order.' '.$sort)->limit($p->firstRow.','.$p->listRows)->buildSql();
        // 分页跳转的时候保证查询条件
        foreach ($map as $key => $val) {
            if (!is_array($val)) {
                $p->parameter .= "$key=" . urlencode($val) . "&";
            }
        }
        // 分页显示
        $page = $p->show();
        // 列表排序显示
        $sortImg = $sort; // 排序图标
        $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; // 排序提示
        $sort = $sort == 'desc' ? 1 : 0; // 排序方式
        // 模板赋值显示

        //dump($list);die();


        //根据字段转换为注释名称

        $modelname = $this->getActionName();
        $tb = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $modelname)); //正则:驼峰转换为下划线
        $Info = M("columns", "", "DB_INFO");
        // 查询字段和注释对应关系
        $intb = C('DB_PREFIX') . $tb;
        $map ['table_name'] = $intb;
        $xlsCell = $Info->where($map)->table("columns")->field("column_name, column_comment")->select();
//        dump($_GET['checkbox']);
        //$transtring = model_trans_cn($modelname);
        /*       if($_GET['checkbox']  == "Array"){
                   if(cookie('table_tr')){
                       $table_th=cookie('table_th');
                       $table_tr=cookie('table_tr');

                   }

                   echo 11;

               }*/
        $cookie_tr_name = $modelname . "_table_tr";
        $cookie_th_name = $modelname . "_table_th";
        if (cookie("$cookie_tr_name")) {
            if (!is_array($_GET['checkbox'])) {
                $table_th = cookie("$cookie_th_name");
                $table_tr = cookie("$cookie_tr_name");
            } else {
                $check_array = $_GET['checkbox'];
                $table_tr = $check_array;
                $cnname = array();
                foreach ($xlsCell as $val) {
                    if (in_array($val['column_name'], $check_array)) {
                        $cnname[] = $val['column_comment'];
                    }
                }
//            $num=count($cnname)-1;
//            $cnname[$num]="操作";
                $table_th = $cnname;
                cookie("$cookie_tr_name", $table_tr, array('expire' => 3600));
                cookie("$cookie_th_name", $table_th, array('expire' => 3600));

//           dump($table_th);
//            dump($table_tr);
            }


        } else {
            if (is_array($_GET['checkbox'])) {
                $check_array = $_GET['checkbox'];
                $table_tr = $check_array;
                $cnname = array();
                foreach ($xlsCell as $val) {
                    if (in_array($val['column_name'], $check_array)) {
                        $cnname[] = $val['column_comment'];
                    }
                }
//            $num=count($cnname)-1;
//            $cnname[$num]="操作";
                $table_th = $cnname;
                cookie("$cookie_tr_name", $table_tr, array('expire' => 3600));
                cookie("$cookie_th_name", $table_th, array('expire' => 3600));

//           dump($table_th);
//            dump($table_tr);

            } else {
                if ($_GET['checkbox'] == "Array") {
                    $table_th = cookie("$cookie_th_name");
                    $table_tr = cookie("$cookie_th_name");
                } else {


                    // 根据配置 设置模块显示的内容包括表格标题 内容等
                    $table_th = $transtring ["main_table_th"];
                    $table_tr = $transtring ["main_table_tr"];
                }

            }
        }


//        dump($_GET['checkbox']);


//        dump($table_tr);
//        die();

        $this->assign('transtring', $transtring);
        $this->assign('table_th', $table_th);
        $this->assign('table_tr', $table_tr);
        // $this->assign('table_tr',$table_tr);

        $listthird = array();
        foreach ($list as $key => $val) {

            foreach ($table_tr as $tr_val) {
                $listthird [$key] [$tr_val] = $val ["$tr_val"];
            }
        }
        // dump($listthird);die();

        $listsecond = array();
        foreach ($listthird as $key => $val) {
            foreach ($val as $keynext => $valnext) {
                if (in_array($keynext, $table_tr)) {
                    $listsecond [$key] [] = $valnext;
                    // echo $key."||".$keynext."||"."$valnext" ."<br/>";
                }
            }
        }


        //根据模块名生成CRUD按钮的超链接赋值给模板
        $exptb = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $modelname)); //正则:驼峰转换为下划线
        $add_link = U("Admin/$modelname/add");
        $delete_link = U("Admin/$modelname/delete");
        $edit_link = U("Admin/$modelname/edit");
        $cache_link = U("Admin/$modelname/cache");
        $index_link = U("Admin/$modelname/index");
        $export_link = U("Admin/Execl/export", array('exptb' => "$exptb", 'expAll' => '1'));
        $import_link = U("Admin/$modelname/import");

        /*      //根据配置生成表格<php>标签生成
              echo " <table>";
              foreach ($list as  $vo){
                         if($vo['id']){
                           echo   "<tr>";
                           echo        "<td class='notallow td_center'>";
                           echo           "<input type='checkbox' value=".$vo['id']." class='style checkboxrow'/>";
                           echo        "</td>";
                           echo       "<td class='visible-lg td_center'>".$vo['id']."</td>";

                           foreach ($table_tr as $trname){
                               if($trname != "id"){
                           echo       "<td class='visible-lg td_center'>".$vo["$trname"]."</td>";
                               }
                           }
                           $edit_url=$edit_link."?"."id=".$vo['id'];
      //                     echo   $edit_url;
                           echo       "<td style=' margin:0 auto;padding:0;clear:both;'>";
                           echo       "<div style='margin:0; padding:0;  display:inline-block; _display:inline; *display:inline;zoom:1;  ''>";
                           echo              "<a href=".$edit_url." class='btn btn-small btn-primary'>";
                           echo                   "<i class='icon-edit'>编辑按钮</i>";
                           echo               "</a>";
                           echo           "</div>";
                           echo           "<div style='margin:0; padding:0;  display:inline-block; _display:inline; *display:inline;zoom:1; '>";
                           echo               "<a href='".$delete_link."'";
                           echo                  " message='确定要删除 序号为".$vo['id']. "的这条记录么？'";
                           echo                  " params='"."id=".$vo['id']."' dialog='true' checkbox='false'";
                           echo                  " id='dialogtest' class='dialog-action btn btn-small btn-primary'>";
                           echo                   " <i class='icon-remove-sign'>删除按钮</i>";
                           echo               "</a>";
                           echo           "</div>";
                           echo       "</td>";
                           echo    "</tr>";
                         }


              }//--/foreach
              echo " </table>";
              die();*/


        //更新激活状态
        $ModelMenu = M('menus')->field("id,name")->select();
//        dump($ModelMenu);die();
        $names = array();
        foreach ($ModelMenu as $val) {
            $names[] = $val["name"];
        }

        $mapid["name"] = $modelname;
        $id = M('menus')->where($mapid)->field('id')->find();
        $id = $id["id"];
        $data["display"] = "active";
        $data["id"] = $id;
        M('menus')->save($data);
        $datas["display"] = "none";
        $mapmen["id"] = array("neq", $id);
        M('menus')->where($mapmen)->save($datas);









        //赋值超链接给模板
        $this->assign('add_link', $add_link);
        $this->assign('delete_link', $delete_link);
        $this->assign('edit_link', $edit_link);
        $this->assign('cache_link', $cache_link);
        $this->assign('export_link', $export_link);
        $this->assign('import_link', $import_link);
        $this->assign('index_link', $index_link);
        $this->assign('xlsCell', $xlsCell);


        // dump($listsecond);die();
//		$this->assign ( 'listsecond', $listsecond );
        $this->assign('list', $list);
        $this->assign('display', "block");
        $this->assign('sort', $sort);
        $this->assign('order', $order);
        $this->assign('sortImg', $sortImg);
        $this->assign('sortType', $sortAlt);
        $this->assign("page", $page);
        cookie('_currentUrl_', __SELF__);
        return;
    }

    public function showindex()
    {
        // 列表过滤器，生成查询Map对象
        $map = $this->_search();
        /*
         * if(method_exists($this,'_filter')) { $this->_filter($map); }
         */
        $tb = $this->_param('tb', '', 'mysql');
        $model = M($tb);
        if (!empty ($model)) {
            $this->_list($model, $map);
        }

        $tbtwo = $this->_param('tb', '', 'mysql_manage');
        $modeltwo = M($tbtwo);
        if (!empty ($modeltwo)) {
            $this->_showlist($modeltwo, $map);
        }

        $this->display('index');
        return;
    }

    protected function _showlist($model, $map = array(), $sortBy = '', $asc = false, $show = "false")
    {
        // 排序字段 默认为主键名
        if (isset ($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty ($sortBy) ? $sortBy : $model->getPk();
        }
        // 排序方式默认按照倒序排列
        // 接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset ($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            // $sort = $asc?'asc':'desc';
            $sort = $asc ? 'desc' : 'asc';
        }
        $whereArr [] = 1;
        if (is_array($map)) {
            foreach ($map as $key => $val) {
                $whereArr [] = $key . "='" . $val . "'";
            }
        } else {
            $whereArr [] = $map;
        }
        $modelname = $this->getActionName();
        if ($_GET ['searchname']) {
            $table_string = transform_to_string($modelname, $_GET ['searchname']);
        } else {
            $table_string = "server_name";
        }
        if ($_GET ['searchtitle'])
            $whereArr [] = "$table_string LIKE '%" . $_GET ['searchtitle'] . "%'";
        $sql = implode(" AND ", $whereArr);
        // 取得满足条件的记录数
        $count = $model->where($sql)->count('id');
        import("ORG.Util.Page");
        // 创建分页对象
        if (!empty ($_REQUEST ['listRows'])) {
            $listRows = $_REQUEST ['listRows'];
        } else {
            $listRows = C('PER_PAGE');
        }
        $p = new Page ($count, $listRows);
        // $p->setConfig ( 'theme', '<div class="dataTables_info">%totalRow% %header% %nowPage%/%totalPage% 页</div><div class="dataTables_paginate paging_full_numbers">%upPage%%downPage%%first%%prePage%%linkPage%%nextPage%%end%</div>' );
        // 分页查询数据
        $list = $model->where($sql)->order($order . ' ' . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
        // $list = $model->where($sql)->order($order.' '.$sort)->limit($p->firstRow.','.$p->listRows)->buildSql();
        // 分页跳转的时候保证查询条件
        foreach ($map as $key => $val) {
            if (!is_array($val)) {
                $p->parameter .= "$key=" . urlencode($val) . "&";
            }
        }
        // 分页显示
        $page = $p->show();
        // 列表排序显示
        $sortImg = $sort; // 排序图标
        $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; // 排序提示
        $sort = $sort == 'desc' ? 1 : 0; // 排序方式
        // 模板赋值显示

        // dump($list);die();
        $this->assign('listtwo', $list);
        $this->assign('sorttwo', $sort);
        $this->assign('ordertwo', $order);
        $this->assign('sortImgtwo', $sortImg);
        $this->assign('sortTypetwo', $sortAlt);
        $this->assign("pagetwo", $page);
        cookie('_currentUrl_two', __SELF__);
        return;
    }

    /**
     * +----------------------------------------------------------
     * 验证码显示
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return void +----------------------------------------------------------
     * @throws FcsException +----------------------------------------------------------
     */
    function verify()
    {
        import("ORG.Util.Image");
        Image::buildImageVerify();
    }

    public function seokeyReplace()
    {
        if ($_POST ["content"] && strtolower(C('SEOKEY_TIME')) == 'edit') {
            $seokey = include(DATA_PATH . '~seokey.php');
            import("ORG.Util.Seokey");
            $Rep = new Seokey ($seokey, $_POST ["content"]);
            $Rep->KeyOrderBy();
            $Rep->Replaces();
            $_POST ["content"] = $Rep->HtmlString;
        }
    }

    function insert()
    {
        $model = D(isset ($_POST ["insert_model"]) ? $_POST ["insert_model"] : $this->getActionName());
        $ping = $_POST ['seoping'];
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        $modelname = $this->getActionName();
//        dump($modelname);die();

        if ($modelname == "Addstr") {
            if (empty($_POST["str_name"])) {
                $this->error("字段名称不可以为空!");
            }
            if (empty($_POST["str_des"])) {
                $this->error("字段标题不可以为空!");
            }
            if (empty($_POST["str_lenth"])) {
                $this->error("字段长度不可以为空!");
            }

            if (!is_numeric($_POST["str_lenth"])) {
                $this->error("字段长度只能为整数!");
            }
            if (preg_match("/[\x7f-\xff]/", $_POST["str_name"])) { //判断字符串中是否有中文
                // echo "含中文!";die();
                $this->error("字段名称不可以含有中文!");
            }


        }


        $data = $model->data();
        // 保存当前数据对象
        if ($result = $model->add()) { // 保存成功
            // 成功提示
            $this->assign('jumpUrl', cookie('_currentUrl_'));
            $this->success(L('新增成功'));
        } else {
            // 失败提示
            $this->error(L('新增失败'));
        }
    }

    public function add()
    {
        if (method_exists($this, '_before_add')) {
            $this->_before_add();
        }
        $seokey = include_once DATA_PATH . '~seokey.php';
        import("ORG.Util.Seokey");
        $this->assign('seokeylist', $seokey);
        $Category = M('Category');
        $map ["status"] = 1;
        $map ["module"] = $this->getActionName();
        $categoryList = $Category->where($map)->order('level asc, pid asc, sort asc')->select();
        $categoryList = list_to_tree($categoryList);
        $this->assign('categoryList', $categoryList);
        $this->display();
    }

    function read()
    {
        $this->edit();
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


        //传首页等链接在首页的顶端
        $transtring = $tranarr;

//        dump($vo);die();

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

    function update()
    {
        // if(session(C('USER_AUTH_KEY')) != 1)$this->error("对不起，测试帐号无修改权限，您可以添加或发布新内容。");
        // $model = D($this->getActionName());
        $model = D(isset ($_POST ["update_model"]) ? $_POST ["update_model"] : $this->getActionName());

        if (false === $model->create()) {
            $this->error($model->getError());
        }
        $data = $model->data();
        // 更新数据
        if (false !== $model->save()) {
            // 成功提示
            $this->assign('jumpUrl', cookie('_currentUrl_'));
            $this->success(L('更新成功'));
        } else {
            // 错误提示
            $this->error(L('更新失败'));
        }
    }

    function ping($model, $ping)
    {
        $data = array(
            'title' => $model->title,
            'home' => $_SERVER ['SERVER_NAME'],
            'url' => getUrl($model->data, 'Home/' . MODULE_NAME . '/read?id=' . $model->id, 1),
            'rss' => getUrl('', 'Home/Sitemap/rss?mod=' . MODULE_NAME . '&catid=' . $model->catid, 1, 'xml')
        );
        $pingxml = PingXML($data);
        foreach ($ping as $key => $val) {
            $pingurl = C(strtoupper($key) . '_PING_ADDRESS');
            if ($pingurl)
                PingURL($pingurl, $pingxml);
        }
    }

    public function delete()
    {
        // 删除指定记录
        $model = M($this->getActionName());
        if (!empty ($model)) {
            $pk = $model->getPk();
            // $id = $_REQUEST[$pk];
            $id = $_POST [$pk];
            $ids = $_POST ['ids'];
            $del_ids = array();
            if ($id)
                $del_ids [] = $id;
            if ($ids)
                $ids = explode(',', $ids);
            foreach ($ids as $k) {
                if ($k)
                    $del_ids [] = $k;
            }
            if ($del_ids) {
                $condition = array(
                    $pk => array(
                        'in',
                        $del_ids
                    )
                );
                if (false !== $model->where($condition)->delete()) {
                    $this->success(L('删除成功'));
                } else {
                    $this->error(L('删除失败'));
                }
            } else {
                $this->error('非法操作');
            }
        }
    }

    // 通过审核
    public function pass()
    {
        // 删除指定记录
        $model = D($this->getActionName());
        if (!empty ($model)) {
            $pk = $model->getPk();
            if (isset ($_REQUEST [$pk])) {
                $id = $_REQUEST [$pk];
                $condition = array(
                    $pk => array(
                        'in',
                        explode(',', $id)
                    )
                );
                if (false !== $model->where($condition)->setField('status', 1)) {
                    $this->assign("jumpUrl", $this->getReturnUrl());
                    $this->success('审核通过！');
                } else {
                    $this->error('审核失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }

    /**
     * +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return string +----------------------------------------------------------
     * @throws ThinkExecption +----------------------------------------------------------
     */
    function getReturnUrl()
    {
        return __URL__ . '?' . C('VAR_MODULE') . '=' . MODULE_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
    }

    /**
     * +----------------------------------------------------------
     * 默认禁用操作
     *
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return string +----------------------------------------------------------
     * @throws FcsException +----------------------------------------------------------
     */
    public function forbid()
    {
        $model = D($this->getActionName());
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array(
            $pk => array(
                'in',
                $id
            )
        );
        if ($model->forbid($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态禁用成功！');
        } else {
            $this->error('状态禁用失败！');
        }
    }

    public function recycle()
    {
        $model = D($this->getActionName());
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array(
            $pk => array(
                'in',
                $id
            )
        );
        if ($model->recycle($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态还原成功！');
        } else {
            $this->error('状态还原失败！');
        }
    }

    // 检查是否是当前作者

    public function recycleBin()
    {
        $map = $this->_search();
        $map ['status'] = -1;
        $model = D($this->getActionName());
        if (!empty ($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    /**
     * +----------------------------------------------------------
     * 默认恢复操作
     *
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return string +----------------------------------------------------------
     * @throws FcsException +----------------------------------------------------------
     */
    function resume()
    {
        // 恢复指定记录
        $model = D($this->getActionName());
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array(
            $pk => array(
                'in',
                $id
            )
        );
        if ($model->resume($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态恢复成功！');
        } else {
            $this->error('状态恢复失败！');
        }
    }

    function recommend()
    {
        $model = D($this->getActionName());
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array(
            $pk => array(
                'in',
                $id
            )
        );
        if ($model->recommend($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('推荐成功！');
        } else {
            $this->error('推荐失败！');
        }
    }

    function unrecommend()
    {
        $model = D($this->getActionName());
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array(
            $pk => array(
                'in',
                $id
            )
        );
        if ($model->unrecommend($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('取消推荐成功！');
        } else {
            $this->error('取消推荐失败！');
        }
    }

    /**
     * +----------------------------------------------------------
     * 默认上传操作
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return void +----------------------------------------------------------
     * @throws ThinkExecption +----------------------------------------------------------
     */
    public function upload()
    {
        if (!empty ($_FILES)) { // 如果有文件上传
            foreach ($_FILES as $key => $file) {
                if ($file ['name']) {
                    $isupload = true;
                    break;
                }
            }
            // 上传附件并保存信息到数据库
            if ($isupload)
                $this->_upload(MODULE_NAME);
            // $this->forward();
        }
    }

    /**
     * +----------------------------------------------------------
     * 文件上传功能，支持多文件上传、保存数据库、自动缩略图
     * +----------------------------------------------------------
     *
     * @access protected
     *         +----------------------------------------------------------
     * @param string $module
     *            附件保存的模块名称
     * @param integer $id
     *            附件保存的模块记录号
     *            +----------------------------------------------------------
     * @return void +----------------------------------------------------------
     * @throws ThinkExecption +----------------------------------------------------------
     */
    protected function _upload($module = '', $recordId = '')
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile ();
        $module = $module ? $module : MODULE_NAME;
        // 设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        // 设置上传文件类型
        $upload->allowExts = explode(',', strtolower(C('TOPIC_UPLOAD_FILE_EXT')));
        // 设置附件上传目录
        $upload->savePath = './Public/Uploads/' . $module . '/';
        // 记录上传成功ID
        $uploadId = array();
        $savename = array();
        $uploadRecord = true;
        // 执行上传操作
        if (!$upload->upload()) {
            if ($this->isAjax() && isset ($_POST ['_uploadFileResult'])) {
                $uploadSuccess = false;
                $ajaxMsg = $upload->getErrorMsg();
            } else {
                // 捕获上传异常
                $this->error($upload->getErrorMsg());
            }
        } else {
            // 取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
            $remark = $_POST ['remark'];
            // 保存附件信息到数据库
            if ($uploadRecord) {
                $Attach = M('Attach');
                // 启动事务
                $Attach->startTrans();
            }
            if (!empty ($_POST ['_uploadFileTable'])) {
                // 设置附件关联数据表
                $module = $_POST ['_uploadFileTable'];
            }
            if (!empty ($_POST ['_uploadRecordId'])) {
                // 设置附件关联记录ID
                $recordId = $_POST ['_uploadRecordId'];
            } elseif ($_POST ['id']) {
                $recordId = $_POST ['id'];
            }
            if (!empty ($_POST ['_uploadFileId'])) {
                // 设置附件记录ID
                $id = $_POST ['_uploadFileId'];
            }
            if (!empty ($_POST ['_uploadFileVerify'])) {
                // 设置附件验证码
                $verify = $_POST ['_uploadFileVerify'];
            }
            if (!empty ($_POST ['_uploadUserId'])) {
                // 设置附件上传用户ID
                $userId = $_POST ['_uploadUserId'];
            } else {
                $userId = isset ($_SESSION [C('USER_AUTH_KEY')]) ? $_SESSION [C('USER_AUTH_KEY')] : 0;
            }
            foreach ($uploadList as $key => $file) {
                $savename [] = $file ['savepath'] . $file ['savename'];
                $sourcename [] = $file ['name'];
                $_POST [$file ['key']] = $file ['savename'];
                // 回调接口
                if (method_exists($this, '_tigger_upload')) {
                    $this->_tigger_upload($file);
                }
                if ($uploadRecord) {
                    // 附件数据需要保存到数据库
                    // 记录模块信息
                    unset ($file ['key']);
                    $file ['module'] = $module;
                    $file ['record_id'] = $recordId ? $recordId : 0;
                    $file ['user_id'] = $userId;
                    $file ['verify'] = $verify ? $verify : '0';
                    $file ['remark'] = $remark [$key] ? $remark [$key] : ($remark ? $remark : '');
                    $file ['status'] = 1;
                    $file ['create_time'] = time();
                    if (empty ($file ['hash'])) {
                        unset ($file ['hash']);
                    }
                    // 保存附件信息到数据库
                    if ($upload->uploadReplace) {
                        if (!empty ($id)) {
                            $vo = $Attach->getById($id);
                        } else {
                            $vo = $Attach->where("module='" . $module . "' and record_id=" . $recordId)->find();
                        }
                        if ($vo) {
                            // 如果附件为覆盖方式 且已经存在记录，则进行替换
                            $id = $vo [$Attach->getPk()];
                            if ($uploadFileVersion) {
                                // 记录版本号
                                $file ['version'] = $vo ['version'] + 1;
                                // 备份旧版本文件
                                $oldfile = $vo ['savepath'] . $vo ['savename'];
                                if (is_file($oldfile)) {
                                    if (!is_dir(dirname($oldfile) . '/_version/')) {
                                        mkdir(dirname($oldfile) . '/_version/');
                                    }
                                    $bakfile = dirname($oldfile) . '/_version/' . $id . '_' . $vo ['version'] . '_' . $vo ['savename'];
                                    $result = rename($oldfile, $bakfile);
                                }
                            }
                            // 覆盖模式
                            $Attach->where("id=" . $id)->save($file);
                            $uploadId [] = $id;
                        } else {
                            $uploadId [] = $Attach->add($file);
                        }
                    } else {
                        // 保存附件信息到数据库
                        $uploadId [] = $Attach->add($file);
                    }
                }
            }
            if ($uploadRecord) {
                // 提交事务
                $Attach->commit();
            }
            $uploadSuccess = true;
            $ajaxMsg = '';
            if (!$recordId && $uploadId)
                $_POST ['_attachIds'] = implode(',', $uploadId);
        }

        // 判断是否有Ajax方式上传附件
        // 并且设置了结果显示Html元素
        if ($this->isAjax() && isset ($_POST ['_uploadFileResult'])) {
            // Ajax方式上传参数信息
            $info = Array();
            $info ['success'] = $uploadSuccess;
            $info ['message'] = $ajaxMsg;
            // 设置Ajax上传返回元素Id
            $info ['uploadResult'] = $_POST ['_uploadFileResult'];
            if (isset ($_POST ['_uploadFormId'])) {
                // 设置Ajax上传表单Id
                $info ['uploadFormId'] = $_POST ['_uploadFormId'];
            }
            if (isset ($_POST ['_uploadResponse'])) {
                // 设置Ajax上传响应方法名称
                $info ['uploadResponse'] = $_POST ['_uploadResponse'];
            }
            if (!empty ($uploadId)) {
                $info ['uploadId'] = implode(',', $uploadId);
            }
            $info ['savename'] = implode(',', $savename);
            $info ['name'] = implode(',', $sourcename);
            $this->ajaxUploadResult($info);
        }
        return;
    }

    /**
     * +----------------------------------------------------------
     * Ajax上传页面返回信息
     * +----------------------------------------------------------
     *
     * @access protected
     *         +----------------------------------------------------------
     * @param array $info
     *            附件信息
     *            +----------------------------------------------------------
     * @return void +----------------------------------------------------------
     * @throws ThinkExecption +----------------------------------------------------------
     */
    protected function ajaxUploadResult($info)
    {
        // Ajax方式附件上传提示信息设置
        // 默认使用mootools opacity效果
        $show = '<script language="JavaScript" src="' . WEB_PUBLIC_PATH . '/Js/mootools.js"></script><script language="JavaScript" type="text/javascript">' . "\n";
        $show .= ' var parDoc = window.parent.document;';
        $show .= ' var result = parDoc.getElementById("' . $info ['uploadResult'] . '");';
        if (isset ($info ['uploadFormId'])) {
            $show .= ' parDoc.getElementById("' . $info ['uploadFormId'] . '").reset();';
        }
        $show .= ' result.style.display = "block";';
        $show .= " var myFx = new Fx.Style(result, 'opacity',{duration:600}).custom(0.1,1);";
        if ($info ['success']) {
            // 提示上传成功
            $show .= 'result.innerHTML = "<div style=\"color:#3333FF\"><IMG SRC=\"' . APP_PUBLIC_PATH . '/images/ok.gif\" align=\"absmiddle\" BORDER=\"0\"> 文件上传成功！</div>";';
            // 如果定义了成功响应方法，执行客户端方法
            // 参数为上传的附件id，多个以逗号分割
            if (isset ($info ['uploadResponse'])) {
                $show .= 'window.parent.' . $info ['uploadResponse'] . '("' . $info ['uploadId'] . '","' . $info ['name'] . '","' . $info ['savename'] . '");';
            }
        } else {
            // 上传失败
            // 提示上传失败
            $show .= 'result.innerHTML = "<div style=\"color:#FF0000\"><IMG SRC=\"' . APP_PUBLIC_PATH . '/images/update.gif\" align=\"absmiddle\" BORDER=\"0\"> 上传失败：' . $info ['message'] . '</div>";';
        }
        $show .= "\n" . '</script>';
        // $this->assign('_ajax_upload_',$show);
        header("Content-Type:text/html; charset=utf-8");
        exit ($show);
        return;
    }

    /**
     * +----------------------------------------------------------
     * 下载附件
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return void +----------------------------------------------------------
     * @throws FcsException +----------------------------------------------------------
     */
    public function download()
    {
        $id = $_GET ['id'];
        $Attach = M("Attach");
        if ($Attach->getById($id)) {
            $filename = $Attach->savepath . $Attach->savename;
            if (is_file($filename)) {
                $showname = auto_charset($Attach->name, 'utf-8', 'gbk');
                $Attach->where('id=' . $id)->setInc('download_count');
                import("ORG.Net.Http");
                Http::download($filename, $showname);
            }
        }
    }

    /**
     * +----------------------------------------------------------
     * 默认删除附件操作
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return string +----------------------------------------------------------
     * @throws FcsException +----------------------------------------------------------
     */
    function delAttach()
    {
        // 删除指定记录
        $attach = M("Attach");
        $pk = $attach->getPk();
        $id = $_REQUEST [$pk];
        $condition = array(
            $pk => array(
                'in',
                $id
            )
        );
        if ($attach->where($condition)->delete()) {
            $this->ajaxReturn($id, L('_DELETE_SUCCESS_'), 1);
        } else {
            $this->error(L('_DELETE_FAIL_'));
        }
    }

    function saveTag($tags, $id, $module = MODULE_NAME)
    {
        if (!empty ($tags) && !empty ($id)) {
            $Tag = M("Tag");
            $Tagged = M("Tagged");
            // 记录已经存在的标签
            $exists_tags = $Tagged->where("module='{$module}' and record_id={$id}")->getField("id,tag_id");
            $Tagged->where("module='{$module}' and record_id={$id}")->delete();
            $tags = explode(' ', $tags);
            foreach ($tags as $key => $val) {
                $val = trim($val);
                if (!empty ($val)) {
                    $tag = $Tag->where("module='{$module}' and name='{$val}'")->find();
                    if ($tag) {
                        // 标签已经存在
                        if (!in_array($tag ['id'], $exists_tags)) {
                            $Tag->where('id=' . $tag ['id'])->setInc('count');
                        }
                    } else {
                        // 不存在则添加
                        $tag = array();
                        $tag ['name'] = $val;
                        $tag ['count'] = 1;
                        $tag ['module'] = $module;
                        $result = $Tag->add($tag);
                        $tag ['id'] = $result;
                    }
                    // 记录tag关联信息
                    $t = array();
                    $t ['user_id'] = session(C('USER_AUTH_KEY'));
                    $t ['module'] = $module;
                    $t ['record_id'] = $id;
                    $t ['create_time'] = time();
                    $t ['tag_id'] = $tag ['id'];
                    $Tagged->add($t);
                }
            }
        }
    }

    function saveSeokey($seokey, $id, $module = MODULE_NAME)
    {
        if (!empty ($id)) {
            $SeoModel = M("Seokey");
            if (!$seokey) {
                $SeoModel->where("module='{$module}' and modid='{$id}'")->delete();
                return false;
            }
            $seokey_list = explode(',', C('SEOKEY'));
            $exists_key = $SeoModel->where("title='{$seokey}'")->find();
            if (in_array($seokey, $seokey_list) || $exists_key && ($exists_key ["modid"] != $id && $exists_key ["module"] == $module || $exists_key ["module"] != $module)) {
                $modModel = M($module);
                $Pk = $modModel->getPk();
                $modModel->where($Pk . "='" . $id . "'")->data(array(
                    "seokey" => ""
                ))->save();
                return false;
            }
            $data = array(
                "title" => $seokey,
                "url" => $_POST ['url'] ? $_POST ['url'] : '-',
                "module" => $module,
                "modid" => $id,
                "times" => 1,
                "status" => 1,
                "sort" => 0,
                'is_title_in_url' => intval($_POST ['is_title_in_url']),
                'is_title_to_pinyin' => intval($_POST ['is_title_to_pinyin']),
                'urlwords' => $_POST ['urlwords'],
                "update_time" => NOW_TIME
            );
            if (!$exists_key)
                $exists_key = $SeoModel->where("module='{$module}' and modid='{$id}'")->find();
            if (!$exists_key) {
                $data ["create_time"] = NOW_TIME;
                $sid = $SeoModel->add($data);
            } else {
                $sid = $exists_key ["id"];
                $SeoModel->where("id='" . $exists_key ["id"] . "'")->save($data);
            }
            $sk_url = getReadUrl($id, $data, $module, 1);
            $temp_sk = array(
                'Key' => $seokey,
                'Href' => '<a href="' . $sk_url . '" target="_blank">' . $seokey . '</a>',
                'id' => intval($sid),
                'ReplaceNumber' => 1
            );
            $seokey_file = DATA_PATH . '~seokey.php';
            $seokeylist = include($seokey_file);
            $seokeylist [$sid] = $temp_sk;
            $content = "<?php\nreturn " . var_export(array_change_key_case($seokeylist, CASE_UPPER), true) . ";\n?>";
            file_put_contents($seokey_file, $content);
        }
    }

    function saveUrl($url, $id, $module = MODULE_NAME)
    {
        if (!empty ($url) && !empty ($id)) {
            $UrlModel = M("Urls");
            $_POST ['url'] = $url;
            $exists_key = $UrlModel->where("url='{$url}'")->find();
            if ($exists_key && ($exists_key ["modid"] != $id || $exists_key ["module"] != $module)) {
                $modModel = M($module);
                $Pk = $modModel->getPk();
                $modModel->where($Pk . "='" . $id . "'")->data(array(
                    "url" => ""
                ))->save();
                $_POST ['url'] = '';
                return false;
            }
            $data = array(
                "url" => $url,
                "module" => $module,
                "modid" => $id
            );
            if (!$exists_key)
                $exists_key = $UrlModel->where("module='{$module}' and modid='{$id}'")->find();
            if (!$exists_key) {
                $sid = $UrlModel->add($data);
            } else {
                $sid = $exists_key ["id"];
                $UrlModel->where("id='" . $exists_key ["id"] . "'")->save($data);
            }
        }
    }

    function saveNav($title, $pos, $id, $module = MODULE_NAME)
    {
        $MenuModel = M("Menu");
        if (!empty ($pos) && !empty ($title) && !empty ($id)) {
            $exists_key = $MenuModel->where("modid='{$id}' AND name='{$module}'")->find();
            if ($exists_key) {
                $data = array(
                    "description" => $title,
                    "position" => $pos
                );
                $MenuModel->where("id='" . $exists_key ["id"] . "'")->save($data);
            } else {
                $data = array(
                    "title" => $title,
                    "description" => $title,
                    "link" => "",
                    "name" => $module,
                    "modid" => $id,
                    "position" => $pos,
                    "level" => 1,
                    "status" => 1,
                    "pid" => 0,
                    "target" => '',
                    "sort" => 0
                );
                $MenuModel->add($data);
            }
        } elseif (!empty ($id)) {
            $exists_key = $MenuModel->where("modid='{$id}' AND name='{$module}'")->find();
            if ($exists_key)
                $MenuModel->where("modid='{$id}' AND name='{$module}'")->delete();
        }
    }

    /**
     * +----------------------------------------------------------
     * 生成树型列表XML文件
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return string +----------------------------------------------------------
     */
    public function tree()
    {
        $Model = M($this->getActionName());
        $title = $_REQUEST ['title'] ? $_REQUEST ['title'] : '选择';
        $caption = $_REQUEST ['caption'] ? $_REQUEST ['caption'] : 'name';
        $list = $Model->where('status=1')->order('sort')->findAll();
        $tree = toTree($list);
        header("Content-Type:text/xml; charset=utf-8");
        $xml = '<?xml version="1.0" encoding="utf-8" ?>' . "\n";
        $xml .= '<tree caption="' . $title . '" >' . "\n";
        $xml .= $this->_toTree($tree, $caption);
        $xml .= '</tree>';
        exit ($xml);
    }

    /**
     * +----------------------------------------------------------
     * 把树型列表数据转换为XML节点
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return string +----------------------------------------------------------
     */
    private function _toTree($list, $caption)
    {
        foreach ($list as $key => $val) {
            $tab = str_repeat("\t", $val ['level']);
            if (isset ($val ['_child'])) {
                // 有子节点
                $xml .= $tab . '<level' . $val ['level'] . ' id="' . $val ['id'] . '" level="' . $val ['level'] . '" parentId="' . $val ['pid'] . '" caption="' . $val [$caption] . '" >' . "\n";
                $xml .= $this->_toTree($val ['_child'], $caption);
                $xml .= $tab . '</level' . $val ['level'] . '>' . "\n";
            } else {
                $xml .= $tab . '<level' . $val ['level'] . ' id="' . $val ['id'] . '" level="' . $val ['level'] . '" parentId="' . $val ['pid'] . '" caption="' . $val [$caption] . '" />' . "\n";
            }
        }
        return $xml;
    }

    public function saveSort()
    {
        $seqNoList = $_POST ['seqNoList'];
        if (!empty ($seqNoList)) {
            // 更新数据对象
            $model = M($this->getActionName());
            $col = explode(',', $seqNoList);
            // 启动事务
            $model->startTrans();
            foreach ($col as $val) {
                $val = explode(':', $val);
                $model->id = $val [0];
                $model->sort = $val [1];
                $result = $model->save();
                if (false === $result) {
                    break;
                }
            }
            // 提交事务
            $model->commit();
            if ($result) {
                // 采用普通方式跳转刷新页面
                $this->success('更新成功');
            } else {
                $this->error($model->getError());
            }
        }
    }

    public function tag()
    {
        $Tag = M("Tag");
        $name = trim($_GET ['tag']);
        $Stat = $Tag->where('module="' . $this->getActionName() . '" and name="' . $name . '"')->field("id,count")->find();
        $tagId = $Stat ['id'];
        $count = $Stat ['count'];
        import("Think.Util.Page");
        $p = new Page ($count);
        $Model = M($this->getActionName());
        $Tagged = M("Tagged");
        $recordIds = $Tagged->where("module='" . $this->getActionName() . "' and tag_id=" . $tagId)->getField('id,record_id');
        if ($recordIds) {
            $map ['id'] = array(
                'IN',
                $recordIds
            );
            $this->_list($Model, $map);
            $this->display('index');
        } else {
            $this->error('标签没有对应的文章！');
        }
    }

    public function checkAdmin($return = 0)
    {
        if (!session(C('ADMIN_AUTH_KEY'))) {
            if ($return) {
                return false;
            } else {
                $this->error('您无权限进行此操作！');
            }
        } elseif ($return) {
            return true;
        }


    }

    // 查看某个模块的标签相关的记录

    public function import()
    {

        //传首页等链接在首页的顶端
        $modelname = $this->getActionName();
//        $transtring = model_trans_cn($modelname);
        $build_info = R('Admin/GetModule/checkmodel', array($modelname));
        if ($build_info) {
            $getmodelarray = R('Admin/GetModule/getmodule', array($build_info));
            $transtring = model_trans_cn($modelname, $getmodelarray);
        } else {
            $transtring = model_trans_cn($modelname);
        }
        $index_link = U("Admin/$modelname/index");
        $import_tb = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $modelname)); //正则:驼峰转换为下划线
        $import_link = U("Admin/Execl/import", array(
            'import_tb' => "$import_tb", // 要导入表的名称
            'starline' => '3', // 默认从第三行开始导入
            'distiing_string' => 'mark', // 数据库中区别的字段
            'excel_string_rownum' => '0' // excel中区别的列(从0开始)


        ));


//        dump($import_tb);die();

//        dump($import_link);die();
        $this->assign('transtring', $transtring);
        $this->assign('index_link', $index_link);
        $this->assign('import', $import_link);
        $this->display();
    }

    // 检查用户是否登录

    /**
     * +----------------------------------------------------------
     * 默认列表选择操作
     *
     * +----------------------------------------------------------
     *
     * @access public
     *         +----------------------------------------------------------
     * @return string +----------------------------------------------------------
     * @throws FcsException +----------------------------------------------------------
     */
    protected function select($fields = 'id,name', $title = '')
    {
        $map = $this->_search();
        // 创建数据对象
        $Model = M($this->getActionName());
        // 查找满足条件的列表数据
        $list = $Model->where($map)->getField($fields);
        $this->assign('selectName', $title);
        $this->assign('list', $list);
        $this->display();
        return;
    }

    protected function checkAuthor($name = '')
    {
        if ($_SESSION [C('USER_AUTH_KEY')] != 1) {
            $id = $_GET ['id'];
            $name = empty ($name) ? $this->getActionName() : $name;
            $Model = M($name);
            $Model->find(( int )$id);
            if ($Model->member_id != $_SESSION [C('USER_AUTH_KEY')]) {
                $this->error('没有权限！');
            }
        }
    }

    protected function getAttach($module = '')
    {
        $module = empty ($module) ? $this->getActionName() : $module;
        // 读取附件信息
        $id = $_REQUEST ['id'];
        $Attach = M('Attach');
        $attachs = $Attach->where("module='" . $module . "' and record_id=$id")->select();
        // 模板变量赋值
        $this->assign("attachs", $attachs);
    }


}

?>
