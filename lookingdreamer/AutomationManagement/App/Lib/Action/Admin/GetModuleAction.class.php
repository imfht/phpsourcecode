<?php

class  GetModuleAction extends CommonAction
{
     function getmodule($build_info)
    {

        //取下拉框信息
        if ($build_info['drop_down_id']) {
            $drop_down = M('dropdown');
            $mapdown = array('id_key' => $build_info['drop_down_id']);
            $drop_down_info = $drop_down->where($mapdown)->select();
            $build_info['drop_down'] = $drop_down_info;
            //dump($drop_down_info);die();
        }
        //取表格的标题/位置等信息
        $getmodelarray["main_title"] = $build_info['main_title']; //首页显示第一个位置
        $getmodelarray["next_title"] = $build_info['next_title']; //首页显示第二个位置
        $getmodelarray["main_table_title"] = $build_info['main_table_title']; //列表的大标题
        $getmodelarray["main_table_tr"] = explode(',', $build_info['model_tr_string']); //缺省列表的显示内容字段
        //查询字段对应的注释->取得中文标题
        $Infocol = M("columns", "", "DB_INFO");
        $info_coloum = C('DB_PREFIX') . strtolower($build_info['model_name']);
        $map ['table_name'] = $info_coloum;
        $columval = $Infocol->where($map)->table("columns")->field("column_name, column_comment")->select();
        $cnarray = array();
        foreach ($columval as $valname) {
            if (in_array($valname["column_name"], $getmodelarray["main_table_tr"])) {
                $cnarray[] = $valname["column_comment"];
            }
        }

        $getmodelarray["main_table_th"] = $cnarray; //列表的第一行标题
        //构建下拉框数组
        $downarray = $build_info["drop_down"];
        $downbine = array();
        foreach ($downarray as $down) {
            $main_string = $down['main_string'];
            $main_drop_content = explode(',', $down['main_drop_content']);
            $trankeyval = array();
            foreach ($main_drop_content as $key => $val) {
                $trankeyval["$val"] = $val;
            }
            $downbine["$main_string"] = $trankeyval;

        }
//            dump($downbine);die();
        $getmodelarray["select_array"] = $downbine; //下拉框对应的字段和显示
//            dump($getmodelarray);die();
        return $getmodelarray;


    }


    function checkmodel($modelname){
        //验证该模块是否是自动构建的模块
        $module=M("add_module");
        $name= strtolower($modelname);
        $mapmodel['model_name'] =$name;
        $build_info = $module->where($mapmodel)->find();
//        dump($module->getLastSql());die();
        return $build_info;
    }



    function secheckmodel($module,$modelname){
        //验证该模块是否是自动构建的模块
//        $module=M("add_module");
        $name= strtolower($modelname);
        $mapmodel['model_name'] =$name;
        $build_info = $module->where($mapmodel)->find();
        return $build_info;
    }

}