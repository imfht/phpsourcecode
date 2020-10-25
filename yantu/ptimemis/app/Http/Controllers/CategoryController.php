<?php

namespace App\Http\Controllers;

use DB;
use Request;
use Laravel\Lumen\Routing\Controller;

class CategoryController extends BaseController
{
	public function index(){
		$input = Request::all();
		$filterStr = isset($input["filter_str"])?$input["filter_str"]:"1";
        $categoryData = DB::select("
            SELECT id,name,father_id,module,weight,is_visible FROM category
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
					$value->name = "|————".$value->name;
					$categoryReturn[] = $value;
				}
			}
		}

		foreach ($categoryReturn as $key => $value) {
			$value->is_visible = $value->is_visible==1?true:false;
			if(isset($value->son)) unset($value->son);
		}
		
			$categoryReturn[] = ["id"=>"0","name"=>"顶级"];
		
		$count = count($categoryReturn);
		$return = [
			"count"       =>$count,
			"countPerPage"=>$count,
			"currentPage" =>1,
			"lastPage"    =>1,
			"data"        =>$categoryReturn
		];
		return $this::jsonResponse(false,$return);

	}

    public function show($id)
    {   
    	$table = "category";
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
        $result->father_id = (string)$result->father_id;
        
        return $this::jsonResponse(false,$result);
    }

    public function store()
    {
    	$table = "category";
        $data = $this->dataFileter($table,Request::all(),"store");
        if(empty($data)){
            return $this::jsonResponse(true,[],'参数错误');
        }

        if($data["father_id"] != 0){
        	$fatherCategory  	 = DB::table("category")->select("father_path","module")->where("id",$data["father_id"])->first();
        	$data["father_path"] = $fatherCategory->father_path.",".$data["father_id"];
        	$data["module"]      = $fatherCategory->module;
        }else{
        	$data["father_path"] = "0";
        }
        
        $id = DB::table($table)->insertGetId($data);
        return $this::jsonResponse(false,$id);
    }

    public function update($id)
    {
    	$table = "category";
        $data = $this->dataFileter($table,Request::all(),"update");
        if(empty($data)){
            return $this::jsonResponse(true,[],'参数错误');
        }

        if($data["father_id"] != 0){
        	$fatherCategory  	 = DB::table("category")->select("father_path","module")->where("id",$data["father_id"])->first();
        	$data["father_path"] = $fatherCategory->father_path.",".$data["father_id"];
        	$data["module"]      = $fatherCategory->module;
        }else{
        	$data["father_path"] = "0";
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