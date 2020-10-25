<?php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Request;
use Closure;
use App\Lib\Api\AdminApi;

class Competence
{
	public function handle($request, Closure $next)
	{
		
		$result = $this->getCheck();
		if (!$result) {
			return \Redirect::to('error');
		} else {
			return $next($request);
		}
	}
	
	private function getCheck()
	{
		return true;
		$sys_api = new AdminApi;
		$user = $sys_api->getSystemUser(['user_name' => session('user_name'), 'enabled' => 1]);	
		isset($user['result']) && $user = $user['result'];
		!empty($user) && $user = $user[0];
		if (empty($user)) {
			return false;
		}
		$num = stripos($_SERVER['REQUEST_URI'], '?');
		$action = !$num ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 0, $num);
		$role = $sys_api->getSystemMenu(['fileds' => ['system_menu_id'], 'action_url' => $action]);
		isset($role['result']) && $role = $role['result'];
		!empty($role) && $role = $role[0];
		if (empty($role)) {
			return false;
		} else {
			$competence = $role['system_menu_id'];
			$competence2 = $this->getCompotence(session('sys_id'));
			array_push($competence2, $competence);
			return true;
		}
	}
	
	private function getCompotence($id)
	{
		$sys_api = new AdminApi;
		$role = $sys_api->getSystemUser(['fileds' => ['role_list'], 'system_user_id' => $id]);
		isset($role['result']) && $role = $role['result'];
		!empty($role) && $role = $role[0];
		if (empty($role)) {
			return false;
		}
		$role = $role['role_list'];
		$competence = '';
		$menu_list = $sys_api->getSystemRole(['fileds' => ['menu_list'], 'system_role_id' => $role]);
		isset($menu_list['result']) && $menu_list = $menu_list['result'];
		!empty($menu_list) && $menu_list = $menu_list[0];
		if (empty($menu_list)) {
			return false;
		}
		foreach ($menu_list as $v) {
			$competence .= $v.',';
		}
		$competence = substr($competence, 0 , strlen($competence) - 1);
		$competence = array_unique(explode(',', $competence));
		 
		return $competence;
	}
}