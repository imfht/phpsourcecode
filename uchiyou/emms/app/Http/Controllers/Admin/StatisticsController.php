<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\TreeTrunk;
use App\Util\DateUtil;
use App\Util\ModelUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Material;

class StatisticsController extends Controller{
	
	public function getBasics(Request $request,$type){
		$user = Auth::user();
		$tops = '';
		$title = '';
		switch($type){
			case 'rent':
				$title = '租用最多的10件物资';
				$tops = $this->getTopRentManterial(10);
				break;
			case 'appointment':
				$title = '预约最多的10件物资';
				$tops = $this->getTopAppointmentMaterials(10);
				break;
			default:
				abort(404,'目前没有提供您要的数据类型统计');
		}
		//Cache::
		return view('statistics/basic',
				[
				'title' => $title,
				'names'=> json_encode($tops['names'],JSON_UNESCAPED_UNICODE),
				'counts'=> json_encode($tops['counts'],JSON_UNESCAPED_UNICODE),
				]);
	}
	/*
	 * 获取被预约次数最多的物资
	 */
	private function getTopAppointmentMaterials($topNumber){
		$user = Auth::user();
		if($user->job_type !=User::USER_JOB_MANAGER){
			abort(403,'非管理员不能访问');
		}
		$topRecords = DB::table('mm_appointments as a')
		->leftJoin('mm_material as m','m.id','=','a.material_id')
		->select(DB::raw('m.name,count(a.material_id) as materialCounts'))
		->where('a.company_id','=',$user->company_id)
		->where('a.start_time','<',Date(DateUtil::FORMAT))
		->where('a.delete','<',ModelUtil::getDeleteLevel())
		->groupBy('a.material_id')
		->orderBy('materialCounts','desc')
		->limit($topNumber)
		->get();
		$nameResult = [];
		$countsResult = [];
		foreach ($topRecords as $topRecord){
			$nameResult[] = $topRecord->name;
			$countsResult[] = $topRecord->materialCounts;
		}
		$resultArray = ['names'=>$nameResult,'counts'=>$countsResult];
		return $resultArray;
	}
	private function getTopRentManterial($topNumber){
		$user = Auth::user();
		if($user->job_type !=\App\User::USER_JOB_MANAGER){// 目前限制只能是顶层的超级管理员
			abort(403,'非管理员不能访问');
		}
		$topRecords = DB::table('mm_using_record as u')
		->leftJoin('mm_material as m','m.id','=','u.material_id')
		->select(DB::raw('m.name,count(u.material_id) as materialCounts'))
		->where('u.company_id','=',$user->company_id)
		->where('u.startTime','<',Date(DateUtil::FORMAT))
		->where('u.delete','<',ModelUtil::getDeleteLevel())
		->orderBy('materialCounts','desc')
		->groupBy('u.material_id')
		->limit($topNumber)
		->get();
		$nameResult = [];
		$countsResult = [];
		foreach ($topRecords as $topRecord){
			$nameResult[] = $topRecord->name;
			$countsResult[] = $topRecord->materialCounts;
		}
		$resultArray = ['names'=>$nameResult,'counts'=>$countsResult];
		return $resultArray;
	}
	/*
	 * 获取一个节点（如部门或分类）的基本信息，
	 */
	public function getNodeTotal(){
		$user = Auth::user();
		$subDepartments = DB::table('mm_tree_trunk')
		->select(DB::raw('count(*) as counts'))
		->where('type', TreeTrunk::TYPE_DEPARTMENT)
		->where('parent_id',$user->tree_trunk_id)
		->where('mm_tree_trunk.delete','<',ModelUtil::getDeleteLevel())
		->first();
		$trunkSumResult = TreeTrunk::getSumOfATrunk($user->tree_trunk_id);
		$trunkSumResult = array_merge($trunkSumResult,['subDepartmentCounts'=>empty($subDepartments)?0:$subDepartments->counts]);
		$datas = array_values($trunkSumResult);
		return view('statistics/nodeTotal',['datas'=>$datas]);
	}
	
	public function getMaterialDepartment(){
		$user = Auth::user();
		if($user->job_type != \App\User::USER_JOB_MANAGER){
			abort(403,'非管理员不能查看统计信息');
		}
		$data =array();
		$trunks = TreeTrunk::where('company_id',$user->company_id)
		->where('parent_id',$user->tree_trunk_id)
		->where('delete','<',ModelUtil::getDeleteLevel())
		->get();
		foreach ($trunks as $trunk){
			$count = DB::table('mm_material')
			->select(DB::raw('count(*) as sum'))
			->where('company_id',$user->company_id)
			->whereIn('tree_trunk_id',ModelUtil::getUserSubtrunk($trunk->id))
			->where('delete','<',ModelUtil::getDeleteLevel())
			->get();
			$data[] = ['value'=>$count[0]->sum,'name'=>$trunk->name];
		}
		return view('statistics/materialDepartment',['datas'=>$data]);
	}
}