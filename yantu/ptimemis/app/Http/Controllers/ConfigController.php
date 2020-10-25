<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Response;


class ConfigController extends BaseController
{
	public function init()
	{
		$notInListName  = ["id","updated_at","is_delete","is_deleted"];
		$notInShowName  = [];
		$notInStoreName = ["id","created_at","updated_at","is_delete","is_deleted"];
		$notInSortName  = ["id","is_deleted"];
		$inSearchName   = ["title","name","content","description"];

		$tables = DB::select("SHOW TABLE STATUS;");
		foreach ($tables as $table) {
			$tableConfig = [];
			$tableConfig["base"] = [
				"name"		=> $table->Name,
				"comment"	=> $table->Comment,
				"opera"		=> [
					"show"		=>1,
					"store"		=>1,
					"update"	=>1,
					"destroy"	=>1,
					"search"	=>1,
					"filter"	=>1,
					"page"		=>1
				]
			];

			$tableName = $table->Name;
			$tableDetail = DB::select("SHOW FULL COLUMNS FROM $tableName");

			foreach ($tableDetail as $item) {
				$typeConvetResult = $this->typeConvert($item->Type,$item->Field);	

				$field = [
					"name"		=> $item->Field,
					"null"		=> $item->Null,
					"key"		=> $item->Key,
					"default"	=> ($typeConvetResult["type"]=="boolean" && $item->Default=="1")?true:$item->Default,
					"comment"	=> $item->Comment,
					"type"		=> $typeConvetResult["type"],
					"length"	=> $typeConvetResult["length"],

					"in_list"	=> in_array($item->Field,$notInListName)?0:$typeConvetResult["in_list"],
					"in_show"	=> in_array($item->Field,$notInShowName)?0:1,
					"in_store"	=> in_array($item->Field,$notInStoreName)?0:1,
					"in_search"	=> in_array($item->Field,$inSearchName)?1:0,
					"in_filter"	=> 1,
					"in_sort"	=> in_array($item->Field,$notInSortName)?0:1,
				];
				$tableConfig["fields"][$item->Field] = $field;
			}





			$filePath = "config/$tableName.php";
			if (Storage::exists($filePath)) {
			    Storage::move($filePath, "config_back/$tableName".date("YmdHis").".php");
			}
			Storage::put("config/$tableName.php", "<?php return ".var_export($tableConfig,true).";");
		}

		echo "config init successfully! Edit in 'storage/app/config'.";
	}

	//type格式转换
	public function typeConvert($type,$name){
		$imgName      = ["imgurl","img_dir"];
		$fileName     = ["file","file_dir"];
		$fulltextName = ["content"];
		// number boolean string text datetime decimal float img file fulltext
		$result = array();
		switch (substr($type,0,3)) {
			case 'int':
				$result['type']   = "number";
		 		$result['length'] = substr($type,4,strlen($type)-5);
		 		$result['in_list']= 0;
				break;
			case 'sma':
				$result['type']   = "number";
		 		$result['length'] = substr($type,9,strlen($type)-10);
		 		$result['in_list']= 0;
				break;
			case 'tin':
				$result['type']   = "number";
				$result['length'] = substr($type,8,strlen($type)-9);
				if($result['length'] == "1") $result['type']="boolean";
		 		$result['in_list']= 1;
		 		break;	
		 	case 'var':
				$result['type']   = in_array($name,$imgName)?"img":(in_array($name,$fileName)?"file":"string");
		 		$result['length'] = substr($type,8,strlen($type)-9);
		 		$result['in_list']= ($result['length']>40 && $result['type'] !="img")?0:1;
				break;	
		 	case 'tex':
				$result['type']   = in_array($name,$fulltextName)?"fulltext":"text";
		 		$result['length'] = 0;
		 		$result['in_list']= 0;
				break;	
		 	case 'lon'://longtext
				$result['type']   = "fulltext";
		 		$result['length'] = 0;
		 		$result['in_list']= 0;
				break;	
		 	case 'tim':
				$result['type']   = "datetime";
		 		$result['length'] = 11;
		 		$result['in_list']= 1;
				break;	
		 	case 'dat':
				$result['type']   = "datetime";
		 		$result['length'] = 19;
		 		$result['in_list']= 1;
				break;	
		 	case 'dec':
				$result['type']   = "decimal";
		 		$result['length'] = 0;
		 		$result['in_list']= 1;
				break;	
			default:
				$result['type']   = $type;
				$result['length'] = 0;
		 		$result['in_list']= 1;
				break;
		}
		return $result;
	}
	
}