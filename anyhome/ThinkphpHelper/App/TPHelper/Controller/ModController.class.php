<?php
namespace TPHelper\Controller;
use Think\Controller;
class ModController extends CommonController {
    public function index(){
    	$dirs = $this->getDir($this->apInfo['appdir'].'/App/');
        $this->assign('dirs',$dirs);
    	$this->display();
    }

    public function insert()
    {
        $post = I('post.');
        extract($post);
        $dirs = $this->getDir($this->apInfo['appdir'].'/App');
        if (in_array($name, $dirs)) {
            $this->error($name.'模块已存在');
        }
        $index_file = $this->apInfo['appdir'].'/'.$index_file;
        if (file_exists($index_file)) {
            $this->error('入口文件'.$index_file.'已存在');
        }

        \TPHelper\Org\Build::buildAppDir($name,$this->apInfo['appdir'].'/');

        //入口文件
        \TPHelper\Org\Build::buildEntry($name,$index_file);

        //控制器列表
        $con_arr =  explode(",", $con_list);
        foreach ($con_arr as $k) {
            \TPHelper\Org\Build::buildController($name,ucwords($k),$this->apInfo['appdir'].'/App/');
        }
        //模型列表
        $mod_arr =  explode(",", $mod_list);
        foreach ($mod_arr as $k) {
            \TPHelper\Org\Build::buildModel($name,ucwords($k),$this->apInfo['appdir'].'/App/');
        }
        //视图文件夹
        $view_dir_arr =  explode(",", $view_dir);
        foreach ($view_dir_arr as $k) {
            \TPHelper\Org\Build::buildView($name,ucwords($k),$this->apInfo['appdir'].'/App/');
        }

    	$this->success('创建完毕');
    }

    //获取指定目录的目录列表 不遍历
    //返回目录全路径
    function getDir($dir = '')
    {
        $dirs = array();
        $files = scandir($dir);
        foreach ($files as $k) {
            if ($k == '.' || $k == '..') continue;
            if (is_dir($dir.$k)) {
                $dirs[] = $k;
            }
        }
        return $dirs;
    }

    public function create($md ='',$name = '',$type = '')
    {
        if (!$md || !$name || !$type) return;


        if ($type == 'Con') {
            \TPHelper\Org\Build::buildController($md,ucwords('common'),$this->apInfo['appdir'].'/App/');
            \TPHelper\Org\Build::buildController($md,ucwords($name),$this->apInfo['appdir'].'/App/');
        }elseif ($type == 'Model') {
            \TPHelper\Org\Build::buildModel($md,ucwords('common'),$this->apInfo['appdir'].'/App/');
            \TPHelper\Org\Build::buildModel($md,ucwords($name),$this->apInfo['appdir'].'/App/');
        }
    }

    public function delete($md ='',$name = '',$type = '')
    {
        if (!$md || !$name || !$type) return;

        $APP_PATH = $this->apInfo['appdir'].'/App/';
        if ($type == 'Con') {
            $confile = $name.'Controller.class.php';
            @unlink($APP_PATH."$md/Controller/".$confile);
        }elseif ($type == 'Model') {
            $modfile = $name.'Model.class.php';
            @unlink($APP_PATH."$md/Model/".$modfile);
        }
    }

    public function design($md = '')
    {
        $Model = M();
        $tables = $Model->query("select * from information_schema.tables where table_schema='".C('DB_NAME')."' and table_type='base table';");
        $data = array();
        $APP_PATH = $this->apInfo['appdir'].'/App/';
        foreach ($tables as $k) {
            $tb = str_replace(C('DB_PREFIX'),'',$k['TABLE_NAME']);
            $tb = parse_name($tb,1);
            $vo = array();
            $vo['tbName'] = $k['TABLE_NAME'];
            $vo['mdName'] = $tb;
            $vo['conName'] = $tb;

            $modfile = $tb.'Model.class.php';
            $vo['isModel'] = 0;
            if (file_exists($APP_PATH."$md/Model/".$modfile)) {
                $vo['isModel'] = 1;
            }
            $confile = $tb.'Controller.class.php';
            $vo['isCon'] = 0;
            if (file_exists($APP_PATH."$md/Controller/".$confile)) {
                $vo['isCon'] = 1;
            }
            $data[] = $vo;
        }
        $tmp_tb = array();
        foreach ($tables as $k) {
            $tb = str_replace(C('DB_PREFIX'),'',$k['TABLE_NAME']);
            $tb = parse_name($tb,1);
            $tmp_tb[] = $tb;
        }
        $cons = scandir($APP_PATH."$md/Controller/");
        $other_cons =  array();
        foreach ($cons as $k) {
            if ($k == '.' || $k == '..' || $k == 'index.html') continue;
            $tmk = str_replace("Controller.class.php", '', $k);
            if (!in_array( $tmk,$tmp_tb)) {
                $other_cons[] = $APP_PATH."$md/Controller/".$k;
            }
        }

        $models = scandir($APP_PATH."$md/Model/");
        $other_models =  array();
        foreach ($models as $k) {
            if ($k == '.' || $k == '..' || $k == 'index.html') continue;
            $tmk = str_replace("Model.class.php", '', $k);
            if (!in_array( $tmk,$tmp_tb)) {
                $other_models[] = $APP_PATH."$md/Model/".$k;
            }
        }

        $this->assign('other_models',$other_models);
        $this->assign('other_cons',$other_cons);

        $this->assign('data',$data);
        $this->assign('md',$md);
        $this->display();
    }

}