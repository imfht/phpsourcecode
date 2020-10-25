<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Material;
use App\TreeTrunk;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Util\ModelUtil;
use App\Util\PaginateUtil;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller{
	
	public function search(Request $request){
		// 数据验证
		$this->validate($request, [
				'content' => 'required', // 必填
				'type' => 'required',
		]);
		$type = $request->get('type');
		$content = $request->get('content');
		$user = Auth::user();
		
		$materials ='';
		if($type == 'tree_trunk_name'){
			$trunks = TreeTrunk::where('company_id',$user->company_id)
			->where('name','like','%'.$content.'%')
			->where('delete','<',ModelUtil::getDeleteLevel())
			->get();
			foreach ( $trunks as $trunk){
				$materials = Material::where('company_id',$user->company_id)
				->whereIn('tree_trunk_id',ModelUtil::getUserSubtrunk($trunk->id))
				->where('delete','<',ModelUtil::getDeleteLevel())
				->paginate(PaginateUtil::SEARCH_PAGE_SIZE);//对搜索的分页目前没做好
			}
		}else{
			$materials = Material::where($type,'like','%'.$content.'%') 
			->whereIn('tree_trunk_id',ModelUtil::getUserSubtrunk($user->tree_trunk_id))
			->where('delete','<',ModelUtil::getDeleteLevel())
			->paginate(PaginateUtil::SEARCH_PAGE_SIZE);//对搜索的分页目前没做好
		//$materials = $this->getDepartmentRecords( $type, $content, $user->tree_trunk_id);
		}
		return view('search/result',['materials' => $materials]);
	}
	
	/*
	 * return 返回一个 有 Collection 的 toArray 方法获得的数组
	 * 递归不太好分页
	 */
	private function getDepartmentRecords($column,$value,$trunkId){
		//$tableName = '\\App\\'.$tableName;// 递归方法，必须从外部传入绝对类名
		$tables = Material::where('tree_trunk_id',$trunkId)
		->where($column,'like','%'.$value.'%')
		->where('delete','<',ModelUtil::getDeleteLevel())
		->get();
		$recordArray = [];
		if(empty($tables) === false){
			$recordArray = $tables->toArray();
		}
		$trunks = TreeTrunk::where('parent_id',$trunkId)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->get();
		if(empty($trunks) === false){
			foreach ($trunks as $trunk){
				$recordArray = array_merge($recordArray,
					$this->getDepartmentRecords($column, $value,
							$trunk->id));
			}
		}
		return $recordArray;
	}
}