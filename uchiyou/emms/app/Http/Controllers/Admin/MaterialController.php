<?php

namespace App\Http\Controllers\Admin;

use App\Appointment;
use App\Http\Controllers\Controller;
use App\Material;
use App\TreeTrunk;
use App\User;
use App\UsingRecord;
use App\Util\DateUtil;
use App\Util\ModelUtil;
use App\Util\PictureUtil;
use App\Util\StringUtil;
use App\Util\TreeUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MaterialController extends Controller {
	/*
	 * 根据物资 id 显示物资的详细信息
	 */
	public function showDetailInfo($materialId){
		$material = Material::findOrFail($materialId);
		$material->departmentName = TreeUtil::getParentTrunkNames($material->company_id, $material->tree_trunk_id);
		if($material->delete >= ModelUtil::getDeleteLevel()){
			return view('exception/wasDelete');
		}
		
		$user = Auth::user();
		if(!is_array($user->address)){
			$user->address = StringUtil::recordToRequestAddress($user->address);
		}
		// 判断该物资被预约的情况（被多少人预约，以及是否自己有预约）
		$appoints = Appointment::where('material_id',$material->id)
		->where('status',Appointment::APPOINTMENT_STATUS_APPOINTED)
		->get();
		$appointNumbers = 0;
		$hasAppoint = false;
		if(empty($appoints) === false && $appoints->count()>0){
			$appointNumbers = $appoints->count();
			$myAppoint = $appoints->filter(
					function ($item) use ($user) {
						return $item->user_id == $user->id;
					});
			if(empty($myAppoint) === false && $myAppoint->count()>0){
				$hasAppoint = true;
			}
		}
		// 如果是自己已经借了的，则不能够再预约.前提是物资是被租用状态。
		if($material->status == Material::MATERIAL_STATUS_WASRENT){
			$now = date('y-m-d h:i:s',time());
			$isSelfRent = UsingRecord::where('user_id',$user->id)
			->where('material_id','=',$material->id)
			->where('deadline','>',$now)
			->where('startTime','<',$now)
			->where('delete','<',ModelUtil::getDeleteLevel())
			->get();
			if(empty($isSelfRent) === false && $isSelfRent->count()>0){
				$material->status = Material::MATERIAL_STATUS_WASAPPOINTMENT_SELF;
			}
		}
		//  MaterialLifecycle/showMaterialDetail
		return view('MaterialLifecycle/showMaterial',
				['material'=>$material,'place'=>'showNodeInfo',
				'appointNumbers'=>$appointNumbers,'hasAppoint'=>$hasAppoint,
				'deadline'=>date('Y-m-d',strtotime('+'.DateUtil::RENT_MAX_TIME_DAYS.' '.DateUtil::RENT_TIME_TYPE)),
				'user'=>$user
				]);
	}
	/*
	 * 存储物资信息
	 */
	public function store(Request $request) {
		// 数据验证
		$this->validate ( $request, [ 
				'type' => 'required', // 必填
				'number' => 'required'  // 必填
		] );
		$user = Auth::user (); // $user 一定存在，因为当前方法在 路由中设为，需要经过 Auth 系统验证
		// 确保物资编号唯一
		$materials = Material::where('company_id',$user->company_id)
		->where('material_number',$request->get('number'))
		->get();
		if($materials->count()>0){
			return $this->jsonResult(1,[],'物资编号已存在，请选择新的编号');
		}
		// 更新数据方法
		if ($request->get ( 'saveOrUpdate' ) == 'update') {
			return $this->update ( $request, $user->company_id );
		}
		$material = new Material ();
		$material->name = $request->get ( 'name' );
		$material->main_type = $request->get ( 'mainType' );
		$material->type = $request->get ( 'type' );
		$material->material_number = $request->get ( 'number' );
		$material->price = $request->get ( 'price' );
		$material->description = $request->get ( 'description' );
		$material->picture_url = $request->get('pictureUrl');
		
		$material->sort = 0;
		$material->tree_trunk_id = TreeUtil::nodeidToRecordid ( $request->get ( 'parentId' ) );
		$treeTrunk = TreeTrunk::findOrFail($material->tree_trunk_id);
		$material->status = 1;
		$material->company_id = $user->company_id;
		
		// 将数据保存到数据库，通过判断保存结果，控制页面进行不同跳转
		if ($material->save ()) {
			$material = Material::where ( 'name', $material->name )->where ( 'company_id', $material->company_id )->where ( 'tree_trunk_id', $material->tree_trunk_id )->first ();
			
			if (empty ( $material ) == false) {
				return $this->jsonResult ( 0, [ 
						'id' => TreeUtil::recordidToNodeid ( $material->id, TreeUtil::LEAF ),
						'parent_id' => TreeUtil::recordidToNodeid ( $material->tree_trunk_id, TreeUtil::TRUNK ),
						'type' => 'material',
						'name' => $material->name 
				] );
			}
		}
		// 保存失败，跳回来路页面，保留用户的输入，并给出提示
		return $this->jsonResult ( 1, [ ], '保存失败' );
	}
	
	/*
	 * 更新物资信息
	 */
	public function update(Request $request, $companyId) {
		$nodeId = TreeUtil::nodeidToRecordid ( $request->get ( 'id' ) );
		$result = Material::where ( 'id', $nodeId )->update ( [ 
				'name' => $request->get ( 'name' ),
				'main_type' => $request->get ( 'mainType' ),
				'type' => $request->get ( 'type' ),
				'material_number' => $request->get ( 'number' ),
				'price' => $request->get ( 'price' ),
				'picture_url' => $request->get ( 'pictureUrl' ),
				'description' => $request->get ( 'description' ) 
		] );
		// 将数据保存到数据库，通过判断保存结果，控制页面进行不同跳转
		if ($result) {
			$material = Material::where ( 'id', $nodeId )->first ();
			if (empty ( $material ) == false) {
				return $this->jsonResult ( 0, [ 
						'id' => TreeUtil::recordidToNodeid ( $nodeId, TreeUtil::LEAF ),
						'parent_id' => TreeUtil::recordidToNodeid ( $material->tree_trunk_id, TreeUtil::TRUNK ),
						'type' => 'material',
						'name' => $material->name 
				] );
			}
		} else {
			// 保存失败，跳回来路页面，保留用户的输入，并给出提示
			return $this->jsonResult ( 1, [ ], '更新物资信息失败' );
		}
	}
	
	// 图片上传
	public function uploadPicture(Request $request){
		//判断请求中是否包含name=file的上传文件
		if(!$request->hasFile('picture')){
			exit('上传文件为空！');
		}
		$file = $request->file('file');
		$path = PictureUtil::uploadPicture($file);
		if( $path == false){
			exit('保存文件失败');
		}
	}
	
}