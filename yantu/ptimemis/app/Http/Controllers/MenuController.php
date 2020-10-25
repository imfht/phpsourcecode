<?php

namespace App\Http\Controllers;

use DB;
use Request;
use Laravel\Lumen\Routing\Controller;

class MenuController extends BaseController
{
	public function index(){
        $input = Request::all();
        $filterStr = isset($input["filter_str"])?$input["filter_str"]:"1";
        $categoryData = DB::select("
            SELECT id,title,object,object_id,father_id,weight,is_visible FROM menu
            WHERE $filterStr
            ORDER BY father_id ASC,weight DESC,id ASC      
        ");
    
        $categorys = [];
        foreach ($categoryData as $key => $value) {
            if($value->father_id == 0){
                $categorys[$value->id] = $value;
            }else{
                $categorys[$value->father_id]->son[] = $value;
            }
        }
        $categoryReturn = [];
        foreach ($categorys as $key => $category) {
            if(isset($category->id))
                $categoryReturn[] = $category;
            if(isset($category->son)){
                foreach ($category->son as $value) {
                    $value->title = "|————".$value->title;
                    $categoryReturn[] = $value;
                }
            }
        }

        foreach ($categoryReturn as $key => $value) {
            $value->is_visible = $value->is_visible==1?true:false;
            if(isset($value->son)) unset($value->son);
        }

        $count = count($categoryReturn);
        $return = [
            "count"       =>$count,
            "countPerPage"=>$count,
            "currentPage" =>1,
            "lastPage"    =>1,
            "data"        =>$categoryReturn
        ];
        return ["error"=>false,"result"=>$return];

	}

    public function show($id)
    {   
    	$table = "menu";
        //字段过滤
        $configResult = $this::tableConfig($table);
        $filedStr     = "";
        foreach ($configResult['fields'] as $field) {
            $fieldName = $field['name'];
            $filedStr .= "$fieldName,";
        }
        $filedStr  = substr($filedStr, 0, -1);

        $result = DB::select("SELECT $filedStr FROM $table WHERE id=$id");
        $result = empty($result)?[]:$result[0];

        //值转换
        foreach ($result as $key => $value) {
            if($configResult['fields'][$key]["type"] == "boolean") $result->$key = $value?true:false;
        }

        return $this::jsonResponse(false,$result);
    }

    public function store()
    {
    	$table = "menu";
        $data = $this->dataFileter($table,Request::all(),"store");
        if(empty($data)){
            return $this::jsonResponse(true,[],'参数错误');
        }

        $id = DB::table($table)->insertGetId($data);
        return $this::jsonResponse(false,$id);
    }

    public function update($id)
    {
    	$table = "menu";
        $data = $this->dataFileter($table,Request::all(),"update");
        if(empty($data)){
            return $this::jsonResponse(true,[],'参数错误');
        }

        $result = DB::table($table)->where("id",$id)->update($data);
        return $this::jsonResponse(false,$result);
    }

    function dataFileter($table,$input,$function){

        $inputKeys    = array_keys($input);
        $tableConfig  = $this::tableConfig($table);
        $data   = [];
        foreach ($tableConfig["fields"] as $key => $value) {
            $fieldName = $value["name"];
            
            if($function == "store" && !$value["in_store"]) continue;
            if($function == "updte" && !$value["in_show"])  continue;
            if(!in_array($fieldName,$inputKeys) || $input[$fieldName] == null){
                if($value["null"] == 'NO') return [];
                continue;
            }
            $data[$fieldName] = $input[$fieldName];
        }

        return $data;
    }

}