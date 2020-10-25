<?php
/**
 * @desc
 * 游戏链接操作类
 *
 */

class MyGameInterface extends CComponent
{
	// 游戏公共key
	public static $arrGameKey = array(
						1	=> '{4b313d93-67f7-7fcc-2328-83257f0b5192}',	//神仙道
				  ); 

	//=========================================== 游戏公共入口
	// 获取登录地址
	public static function getGameLoginLink(Gp_game_server $objServer, Gp_user $objUser){
		// 合区
		while($objServer->merge_to){
			$objServer = $objServer->mergeto;
		}

		// 记录登录日记
		Gl_game_login_log::model()->newlog($objUser->id, $objServer->id, $objServer->game_id);
		Gl_game_login_log_detail::model()->newlog($objUser->id, $objServer->id, $objServer->game_id);

		// 获取真正的游戏登录地址
		$strLoginFunction = "getLoginUrl_{$objServer->game_id}";
		return self::$strLoginFunction($objServer, $objUser);
	}
	
	// 获取游戏角色
	public static function getGameRole(Gp_game_server $objServer, Gp_user $objUser){
		// 合区
		while($objServer->merge_to){
			$objServer = $objServer->mergeto;
		}
		
		// 获取真正的角色
		$strGelRoleFunction = "getGameRole_{$objServer->game_id}";
		return self::$strGelRoleFunction($objServer, $objUser);
	}

	// 游戏充值接口
	public static function sendGameMoney(Gp_game_server $objServer, Gp_user $objUser, $order_amount,$order_type, $order_id, $role_id=''){
		// 合区
		while($objServer->merge_to){
			$objServer = $objServer->mergeto;
		}
		
		// 具体的游戏充值接口
		$strGamePayFunction = "sendGameMoney_{$objServer->game_id}";
		return self::$strGamePayFunction($objServer, $objUser, $order_amount,$order_type, $order_id, $role_id);
	}
	
	// 新手卡
	public static function getGiftCard(Gp_game_server $objServer, Gp_user $objUser){
		// 合区
		while($objServer->merge_to){
			$objServer = $objServer->mergeto;
		}
		
		// 具体的游戏新手卡
		$strGiftCardFunction = "getGiftCard_{$objServer->game_id}";
		return self::$strGiftCardFunction($objServer, $objUser);
	}
	//============================================ 游戏登录接口
	// 神仙道
	public static function getLoginUrl_1(Gp_game_server $objServer, Gp_user $objUser){
		$key	= self::$arrGameKey['1'];
		$time	= time();
		$link	= '';

		$link	= 'http://'.$objServer->code.'.sxd.05wan.com/login_api.php';
		$link  .= '?user='.$objUser->id;
		$link  .= '&time='.$time;
		$link  .= '&hash='.md5($objUser->id.'_'.$time.'_'.$key);

		return $link;
	}

	//============================================ 游戏角色
	// 神仙道
	public static function getGameRole_1(Gp_game_server $objServer, Gp_user $objUser){
		$arrRole = array();

		$sign = md5($objUser->id . "_" . $objServer->code . '.sxd.05wan.com' . '_' . self::$arrGameKey['1']);
		$url = "http://api.sxd.xd.com/api/check_user.php?user={$objUser->id}&domain=".$objServer->code.".sxd.05wan.com&sign={$sign}";
		$data = MyFunction::get_url($url);
		if($data['code'] == 200 && $data['content'] == 1){
			$arrRole['role_id']		= 1;
			$arrRole['role_name']	= 1;
		}

		return $arrRole;
	}

	//============================================= 游戏充值
	// 神仙道 
	public static function sendGameMoney_1(Gp_game_server $objServer, Gp_user $objUser, $order_amount,$order_type, $order_id, $role_id=''){
		// 游戏币
		$gold = $order_amount * 10;
		$sign = strtolower(md5($objUser->id . "_" . $gold . "_" . $order_id . "_" . $objServer->code.".sxd.05wan.com" . "_" . self::$arrGameKey['1']));
		$url = "http://api.sxd.xd.com/api/buygold.php?user={$objUser->id}&gold={$gold}&order={$order_id}&domain=".$objServer->code.".sxd.05wan.com&sign={$sign}";
		$data = MyFunction::get_url($url);
		if($data['code'] == 200 && ($data['content'] == 1 || $data['content'] == 6)){
			return 1;
		}else{
			return $data['content'];
		}	
	}

	//============================================= 新手卡
	// 神仙道
	public static function getGiftCard_1(Gp_game_server $objServer, Gp_user $objUser){
		$strCode	= '';
		$strCode	= md5($objUser->id.'_'.$objServer->code.'.sxd.05wan.com');
		return $strCode;
	}
}