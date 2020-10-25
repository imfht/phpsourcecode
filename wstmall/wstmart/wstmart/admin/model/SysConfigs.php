<?php
namespace wstmart\admin\model;
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
 * 商城配置业务处理
 */
use think\Db;
use Env;
class SysConfigs extends Base{
	/**
	 * 获取商城配置
	 */
	public function getSysConfigs(){
		$rs = $this->field('fieldCode,fieldValue')->select();
		$rv = [];
		$split = [
		    'submitOrderTipUsers','payOrderTipUsers','cancelOrderTipUsers','rejectOrderTipUsers','refundOrderTipUsers','complaintOrderTipUsers','cashDrawsTipUsers'
		];
		foreach ($rs as $v){
			if(in_array($v['fieldCode'],$split)){
                $rv[$v['fieldCode']] = ($v['fieldValue']=='')?[]:explode(',',$v['fieldValue']);
			}else{
                $rv[$v['fieldCode']] = $v['fieldValue'];
			}
		}
		$signScore = explode(",",$rv['signScore']);
		for($i=0;$i<31;++$i){
			$rv['signScore'.$i] = ($signScore[0]==0)?0:$signScore[$i];
		}
		return $rv;
	}

	/**
	 * 获取商城设置
	 */
	public function getSysConfigsByType($type = 0){
		$rs = $this->field('fieldCode,fieldValue')->where('fieldType','=',$type)->select();
		$rv = [];
		$split = [
		    'submitOrderTipUsers','payOrderTipUsers','cancelOrderTipUsers','rejectOrderTipUsers','refundOrderTipUsers','complaintOrderTipUsers','cashDrawsTipUsers'
		];
		foreach ($rs as $v){
			if(in_array($v['fieldCode'],$split)){
                $rv[$v['fieldCode']] = ($v['fieldValue']=='')?[]:explode(',',$v['fieldValue']);
			}else{
                $rv[$v['fieldCode']] = $v['fieldValue'];
			}
		}

		if(isset($rv['signScore'])){
			$signScore = explode(",",$rv['signScore']);
			for($i=0;$i<31;++$i){
				$rv['signScore'.$i] = ($signScore[0]==0)?0:$signScore[$i];
			}
		}
		return $rv;
	}

	
    /**
	 * 编辑
	 */
	public function edit($fieldType = 0){
		$list = $this->where('fieldType',$fieldType)->field('configId,fieldCode,fieldValue')->select();
		if($fieldType == 0){
			$isDebug =  input('post.isDebug');
			$pat[0] = 'app_debug';
			if($isDebug==1){
				$rep[0] = true;
			}else{
				$rep[0] = false;
			}
			$this->setconfig($pat,$rep);
		}
		$commissionRate = (int)input('post.drawCashCommission');
		if($commissionRate<0 || $commissionRate>100)return WSTReturn("余额提现手续费范围为0至100", -1);
		Db::startTrans();
        try{
			foreach ($list as $key =>$v){
				$code = trim($v['fieldCode']);
				if(in_array($code,['wstVersion','wstMd5','wstMobileImgSuffix','mallLicense']))continue;
				$val = input('post.'.trim($v['fieldCode']));
			    //启用图片
				if(substr($val,0,7)=='upload/' && strpos($val,'.')!==false){
					WSTUseResource(1, $v['configId'],$val, 'sys_configs','fieldValue');
				}
				$this->update(['fieldValue'=>$val],['fieldCode'=>$code]);
				//如果是关闭会员充值的话就对禁用菜单
				if($v['fieldCode']=='isOpenRecharge'){
					Db::name('home_menus')->where('menuUrl','shop/logmoneys/torecharge')->update(['isShow'=>((int)$val==1)?1:0]);
					Db::name('home_menus')->where('menuUrl','home/logmoneys/toUserRecharge')->update(['isShow'=>((int)$val==1)?1:0]);
					cache('WST_HOME_MENUS',null);
				}
			}
			Db::commit(); 
			cache('WST_CONF',null);
			return WSTReturn("操作成功", 1);
        }catch (\Exception $e) {
		    Db::rollback();
		}
		return WSTReturn("操作失败", 1);
	}
	/**
	 * 修改config
	 */
	function setconfig($pat, $rep){
		if (is_array($pat) and is_array($rep)) {
			for ($i = 0; $i < count($pat); $i++) {
				$pats[$i] = '/\'' . $pat[$i] . '\'(.*?),/';
				$reps[$i] = "'". $pat[$i]. "'". "              => " . "'".$rep[$i] ."',";
			}
			$fileurl = Env::get('root_path')."config/app.php";
			$string = file_get_contents($fileurl); //加载配置文件
			$string = preg_replace($pats, $reps, $string); // 正则查找然后替换
			file_put_contents($fileurl, $string); // 写入配置文件
			return true;
		} else {
			return false;
		}
	}

}
