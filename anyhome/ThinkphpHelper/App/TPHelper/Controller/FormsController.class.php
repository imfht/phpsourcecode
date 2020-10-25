<?php
namespace TPHelper\Controller;
use Think\Controller;
class FormsController extends CommonController {

    public function index($md = '',$tb = '',$tpl = '')
    {
        $APP_PATH = $this->apInfo['appdir'].'/';
        $DATA_PATH = $APP_PATH.'/Runtime/Data/';
        $Model = M();
        $tables = $Model->query("select * from information_schema.tables where table_schema='".C('DB_NAME')."' and table_type='base table';");
        // print_r($tables);
        // exit();
        $this->assign('tables',$tables);
        $this->assign('md',$md);

        if ($tb) {
            $sql = "select * from information_schema.COLUMNS where table_name = '".$tb."' and table_schema = '".C('DB_NAME')."'";
            $table_fields = $Model->query($sql);
            // print_r(parse_name($tb,1));
            // print_r($table_fields);
            // exit();
            $this->assign('table_fields',$table_fields);
            
            $this->assign('tb_name',$tb);
            $tb = str_replace(C('DB_PREFIX'),'',$tb);
            $tb = parse_name($tb,1);
            $tbfile = $tb;
            //
            if (!$tpl) $tpl = 'common';
            $field_forms = F($tbfile,'',$DATA_PATH);


            if (!$field_forms[$md]) {
                $field_forms['tb'] = $tb;
                $field_forms['mdName'] = parse_name($tb,1);
                $field_forms[$md]['layout'] = 'form-normal';
                F($tbfile,$field_forms,$DATA_PATH);
                $field_forms = F($tbfile,'',$DATA_PATH);
            }
            if ($tpl != 'common' && $field_forms[$md]['common'] && !$field_forms[$md][$tpl]){
                $common = $field_forms[$md]['common'];
                $field_forms[$md][$tpl]['tpl'] = $tpl;
                $field_forms[$md][$tpl]['fields'] = $common['fields'];
                F($tbfile,$field_forms,$DATA_PATH);
                $field_forms = F($tbfile,'',$DATA_PATH);
            }elseif(!$field_forms[$md][$tpl]) {
                $field_forms[$md][$tpl]['tpl'] = $tpl;
                foreach ($table_fields as $k) {
                    $f = array();
                    $f['pos'] = $k['ORDINAL_POSITION'];
                    $f['label'] = $k['COLUMN_NAME'];
                    $f['fname'] = $k['COLUMN_NAME'];
                    $f['type'] = 'input';
                    $f['isshow'] = '1';
                    $f['dtype'] = $k['COLUMN_TYPE'];
                    // $f['dtype'] = $k['DATA_TYPE'];
                    $field_forms[$md][$tpl]['fields'][$f['fname']] = $f;
                }
                F($tbfile,$field_forms,$DATA_PATH);
                $field_forms = F($tbfile,'',$DATA_PATH);
            }

            ///如果修改新增数据字段
            // foreach ($table_fields as $k) {
            //     if (!$field_forms[$md][$tpl]['fields'][$k['fname']]) {
            //         # code...
            //     }
            //     # code...
            // }
            $this->assign('layout',$field_forms[$md]['layout']);
            $this->assign('mdinfo',$field_forms);
            $this->assign('tb_fields',$field_forms[$md][$tpl]['fields']);
            $this->assign('tb_info',$field_forms[$md][$tpl]);
        }

        $this->display();
    }

    public function delete($md = '',$tb = '',$tpl = '')
    {
        $APP_PATH = $this->apInfo['appdir'].'/';
        $DATA_PATH = $APP_PATH.'/Runtime/Data/';
        $tb = str_replace(C('DB_PREFIX'),'',$tb);
        $tb = parse_name($tb,1);
        $tbfile = $tb;
        F($tbfile,NULL,$DATA_PATH);
    }

    public function cleanUp($md = '',$tb = '',$tpl = '')
    {
        $APP_PATH = $this->apInfo['appdir'].'/';
        $DATA_PATH = $APP_PATH.'/Runtime/Data/';
        $Model = M();
        $sql = "select * from information_schema.COLUMNS where table_name = '".$tb."' and table_schema = '".C('DB_NAME')."'";
        $table_fields = $Model->query($sql);


        $tb = str_replace(C('DB_PREFIX'),'',$tb);
        $tb = parse_name($tb,1);
        $tbfile = $tb;
        if (!$tpl) $tpl = 'common';
        $field_forms = F($tbfile);

        $filed_infos = $field_forms[$md][$tpl]['fields'];
        if (!$filed_infos) return;


        $f_data = array();
        foreach ($filed_infos as $k) {
            $f_data[$k['pos']] = $k;
            foreach ($table_fields as $key) {
                if ($k['fname'] == $key['fname']) {
                    $f_data[$k['pos']] = $key;
                    break;
                }
            }
        }
        $field_forms[$md][$tpl]['fields'] = $f_data;
        F($tbfile,$field_forms,$DATA_PATH);
    }





    public function updateField($md = '',$tb = '',$tpl = '',$pk = 0 ,$name = '',$value = '')
    {
        if (!$md) return;

        $APP_PATH = $this->apInfo['appdir'].'/';
        $DATA_PATH = $APP_PATH.'/Runtime/Data/';
        $tb = str_replace(C('DB_PREFIX'),'',$tb);
        $tb = parse_name($tb,1);
        $tbfile = $tb;
        $field_forms = F($tbfile,'',$DATA_PATH);

        if ($field_forms) {
            if ($name == 'mdName' ) {
                $field_forms[$name] = $value;
                F($tbfile,$field_forms,$DATA_PATH);
            }elseif ($name == 'layout' || $name == 'list' ) {
                $field_forms[$md][$name] = $value;
                F($tbfile,$field_forms,$DATA_PATH);
            }elseif($name == 'globalIptCols'){
                $field_forms[$md][$tpl][$name] = $value;
                F($tbfile,$field_forms,$DATA_PATH);
            }
        }

        if (!$field_forms || !$pk || !$name || !$tpl) return;
        $field_forms[$md][$tpl]['fields'][$pk][$name] = $value;
        F($tbfile,$field_forms,$DATA_PATH);
    }


    //查看模板
    public function viewTpl($md = '',$tb = '',$tpl='')
    {
        if (!$tpl) return;
        $this->display('Forms/tpl/'.$tpl);
    }


}