<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\Users as validate;
use think\Db;
use Env;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 会员业务处理
 */
class Users extends Base{
	protected $pk = 'userId';
	/**
	 * 分页
	 */
	public function pageQuery(){
		/******************** 查询 ************************/
		$where = [];
		$where[] = ['u.dataFlag','=',1];
		$lName = input('loginName1');
		$phone = input('loginPhone');
		$email = input('loginEmail');
		$uType = input('userType');
		$uStatus = input('userStatus1');
		$sort = input('sort');
		if(!empty($lName))
			$where[] = ['loginName|s.shopName','like',"%$lName%"];
		if(!empty($phone))
			$where[] = ['userPhone','like',"%$phone%"];
		if(!empty($email))
			$where[] = ['userEmail','like',"%$email%"];
		if(is_numeric($uType))
			$where[] = ['userType','=',"$uType"];
		if(is_numeric($uStatus))
			$where[] = ['userStatus','=',"$uStatus"];
		$order = 'u.userId desc';
		if($sort){
			$sort =  str_replace('.',' ',$sort);
			$order = $sort;
		}
		/********************* 取数据 *************************/
		$rs = $this->alias('u')->join('__SHOPS__ s','u.userId=s.userId and s.dataFlag=1','left')->where($where)
					->field(['u.*,s.shopId'])
					->order($order)
					->paginate(input('limit/d'))
					->toArray();
	    foreach ($rs['data'] as $key => $v) {
	    	$r = WSTUserRank($v['userTotalScore']);
	    	$rs['data'][$key]['rank'] = $r['rankName'];
	    }
		return $rs;
	}
	public function getById($id){
		return $this->get(['userId'=>$id]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['createTime'] = date('Y-m-d H:i:s');
		$data["loginSecret"] = rand(1000,9999);
    	$data['loginPwd'] = md5($data['loginPwd'].$data['loginSecret']);
    	if($data['brithday']=='')unset($data['brithday']);
    	WSTUnset($data,'userId,userType,userScore,userTotalScore,lastIP,lastTime,userMoney,lockMoney,dataFlag,rechargeMoney');
    	Db::startTrans();
		try{
			$validate = new validate();
		    if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
			$result = $this->allowField(true)->save($data);
			$id = $this->userId;
	        if(false !== $result){
	        	hook("adminAfterAddUser",["userId"=>$id]);
	        	WSTUseResource(1, $id, $data['userPhoto']);
	        	Db::commit();
	        	return WSTReturn("新增成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败',-1);
        }	
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$Id = (int)input('post.userId');
		$data = input('post.');
		$u = $this->where('userId',$Id)->field('loginSecret')->find();
		if(empty($u))return WSTReturn('无效的用户');
		if(!isset($data['brithday']) || $data['brithday']=='')unset($data['brithday']);
		//判断是否需要修改密码
		if(empty($data['loginPwd'])){
			unset($data['loginPwd']);
		}else{
    		$data['loginPwd'] = md5($data['loginPwd'].$u['loginSecret']);
		}
		Db::startTrans();
		try{
			if(isset($data['userPhoto'])){
			    WSTUseResource(1, $Id, $data['userPhoto'], 'users', 'userPhoto');
			}
			
			WSTUnset($data,'loginName,createTime,userId,userType,userScore,userTotalScore,lastIP,lastTime,userMoney,lockMoney,dataFlag,rechargeMoney');
		    $result = $this->allowField(true)->save($data,['userId'=>$Id]);
	        if(false !== $result){
	        	hook("adminAfterEditUser",["userId"=>$Id]);
	        	Db::commit();
	        	return WSTReturn("编辑成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id');
	    if($id==1){
	    	return WSTReturn('无法删除自营店铺账号',-1);
	    }
	    Db::startTrans();
	    try{
		    $data = [];
			$data['dataFlag'] = -1;
		    $result = $this->update($data,['userId'=>$id]);
	        if(false !== $result){
	        	//删除店铺信息
	        	model('shops')->delByUserId($id);
	        	hook("adminAfterDelUser",["userId"=>$id]);
	        	WSTUnuseResource('users','userPhoto',$id);
	        	// 删除app端、小程序端对应用户登录凭证
	        	delAppToken($id);
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
	    }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('删除失败',-1);
        }
	}
	/**
	* 是否启用
	*/
	public function changeUserStatus($id, $status){
		$result = $this->update(['userStatus'=>(int)$status],['userId'=>(int)$id]);
		if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	* 根据用户名查找用户
	*/
	public function getByName($name){
		return $this->field(['userId','loginName'])->where([['loginName','like',"%$name%"],['dataFlag','=',1]])->select();
	}
	/**
	* 获取所有用户id
	*/
	public function getAllUserId()
	{
		return $this->where('dataFlag',1)->column('userId');
	}
	/**
	* 重置支付密码
	*/
	public function resetPayPwd(){
		$Id = (int)input('post.userId');
		$loginSecret = $this->where('userId',$Id)->value('loginSecret');
		// 重置支付密码为6个6
		$payPwd = md5('666666'.$loginSecret);
		$result = $this->where('userId',$Id)->setField('payPwd',$payPwd);
		if(false !== $result){
        	return WSTReturn("重置成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}

	/**
	 * 根据用户账号查找用户信息
	 */
	public function getUserByKey(){
		$key = input('key');
		$user = $this->where([['loginName|userPhone|userEmail','=',$key],['dataFlag','=',1]])->find();
        if(empty($user))return WSTReturn('找不到用户',-1);
        $shop = model('shops')->where([['userId','=',$user->userId],['dataFlag','=',1]])->find();
        if(!empty($shop))return WSTReturn('该用户已存在关联的店铺信息',-1);
        return WSTReturn('',1,['loginName'=>$user->loginName,'userId'=>$user->userId]);
	}

	/**
	 * 导出会员信息
	 */
	public function toExport(){
		$name ='users';
		/******************** 查询 ************************/
		$where = [];
		$where[] = ['u.dataFlag','=',1];
		$lName = input('loginName1');
		$phone = input('loginPhone');
		$email = input('loginEmail');
		$uType = input('userType');
		$uStatus = input('userStatus1');
		$sort = input('sort');
		if(!empty($lName))$where[] = ['loginName|s.shopName','like',"%$lName%"];
		if(!empty($phone))$where[] = ['userPhone','like',"%$phone%"];
		if(!empty($email))$where[] = ['userEmail','like',"%$email%"];
		if(is_numeric($uType))$where[] = ['userType','=',"$uType"];
		if(is_numeric($uStatus))$where[] = ['userStatus','=',"$uStatus"];
		$order = 'u.userId desc';
		if($sort){
			$sort =  str_replace('.',' ',$sort);
			$order = $sort;
		}
		/********************* 取数据 *************************/
		$rs = $this->alias('u')->join('__SHOPS__ s','u.userId=s.userId and s.dataFlag=1','left')->where($where)
					->field(['u.userId','u.rechargeMoney','loginName','userName','userType','userPhone','userEmail','userScore','u.createTime','userStatus','lastTime','s.shopId','userMoney','u.lockMoney','u.userName','u.trueName','u.brithday','u.lastIP','u.lastTime','u.userSex','u.userQQ'])
					->order($order)
					->select();
	    foreach ($rs as $key => $v) {
	    	$r = WSTUserRank($v['userScore']);
	    	$rs[$key]['rank'] = $r['rankName'];
	    }
		require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("会员信息");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array('font' => array('bold' => true,'color'=>array('argb' => 'ffffffff')));
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(16);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:Q1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', '账号')
        ->setCellValue('B1', '昵称')
        ->setCellValue('C1', '真实姓名')
        ->setCellValue('D1', '性别')
        ->setCellValue('E1', '出生日期')
        ->setCellValue('F1', '手机号')
        ->setCellValue('G1', '电子邮箱')
        ->setCellValue('H1', 'QQ')
        ->setCellValue('I1', '可用金额')
        ->setCellValue('J1', '冻结金额')
        ->setCellValue('K1', '充值送')
        ->setCellValue('L1', '积分')
        ->setCellValue('M1', '等级')
        ->setCellValue('N1', '注册时间')
        ->setCellValue('O1', '最后登录时间')
        ->setCellValue('P1', '最后登录IP')
        ->setCellValue('Q1', '状态');
        $objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs[$row]['loginName'])
            ->setCellValue('B'.$i, $rs[$row]['userName'])
            ->setCellValue('C'.$i, $rs[$row]['trueName'])
            ->setCellValue('D'.$i, ($rs[$row]['userSex']==1)?"男":(($rs[$row]['userSex']==2)?"女":"保密"))
            ->setCellValue('E'.$i, $rs[$row]['brithday'])
            ->setCellValue('F'.$i, " ".$rs[$row]['userPhone'])
            ->setCellValue('G'.$i, $rs[$row]['userEmail'])
            ->setCellValue('H'.$i, $rs[$row]['userQQ'])
            ->setCellValue('I'.$i, $rs[$row]['userMoney'])
            ->setCellValue('J'.$i, $rs[$row]['lockMoney'])
            ->setCellValue('K'.$i, $rs[$row]['rechargeMoney'])
            ->setCellValue('L'.$i, " ".$rs[$row]['userScore'])
            ->setCellValue('M'.$i, $rs[$row]['rank'])
            ->setCellValue('N'.$i, $rs[$row]['createTime'])
            ->setCellValue('O'.$i, $rs[$row]['lastTime'])
            ->setCellValue('P'.$i, $rs[$row]['lastIP'])
            ->setCellValue('P'.$i, ($rs[$row]['userStatus']==1)?"启用":"停用");
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:Q'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array ('style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array ('argb' => 'FF000000'))
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
	}
	
}
