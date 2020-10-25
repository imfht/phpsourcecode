<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

use user\model\_user;

class _menu{
	//检查当前调度权限 @update
	public function check_dispatches($dispatches){
		global $db, $_var;

		if(!$_var['current'] && $_var['auth_member']){
		    $_user = new _user();
		    
		    $_var['current'] = $_user->get_by_auth($_var['auth_member']);
		    $_SESSION['_current'] = serialize($_var['current']);
		}
        
		//misc不检查
		if($dispatches['module'] == 'misc') return $dispatches;
		elseif($dispatches['module'] == 'admin' && in_array($dispatches['control'], array('', 'index', 'main', 'frame', 'desktop', 'account', 'preview'))) return $dispatches;
		
		//站内用户凭证无法保存时
		if(!$_var['current'] && $_var['gp__SALT']){
			$tmparr = explode(',', $_var['gp__SALT']);
			
			if(count($tmparr) == 2 && is_cint($tmparr[0]) && strlen($tmparr[1]) == 64){
				if($tmparr[0] == -1) $manager = $db->fetch_first("SELECT * FROM tbl_setting WHERE SKEY = 'SALT' AND SVALUE = '-1,{$tmparr[1]}'");
				else $manager = $db->fetch_first("SELECT * FROM tbl_user WHERE USERID = '{$tmparr[0]}' AND ISMANAGER = 1 AND SALT = '{$tmparr[1]}'");
				
				if($manager) $_var['current'] = array('ROLEID' => $manager['ROLEID'] + 0, 'USERID' => $tmparr[0], 'USERNAME' => '站外');
			}
		}
		
		$operations = $this->get_operations();
		foreach ($operations as $key => $val) $operations[$key] = 'style="display:none;"';
		
		$requesturl = "{\$ADMIN_SCRIPT}/{$dispatches[module]}";
		
		if(!empty($dispatches['control']) && substr($dispatches['control'], 0, 1) != '_'){
			$requesturl .= "/{$dispatches[control]}";
			if(!empty($dispatches['method']) && substr($dispatches['method'], 0, 1) != '_') $requesturl .= "/{$dispatches[method]}";
		}
		
		$requesturl = trim($requesturl);
		
		$menuid = -1;
		
		if($_var['current'] && $_var['current']['ROLEID'] > 0){
			$menuid = $db->result_first("SELECT m.MENUID FROM tbl_menu m , tbl_role_menu rm WHERE m.MENUID = rm.MENUID AND rm.ROLEID = '{$_var[current][ROLEID]}' AND m.URL = '{$requesturl}'");
			$menuid = !$menuid ? -1 : $menuid;
		}elseif($_var['current'] && $_var['current']['ROLEID'] < 1) {
			$menuid = 0;
		}
		
		if($menuid < 0) return null;
		elseif($menuid == 0) {
			foreach ($operations as $key => $val) $operations[$key] = '';
		}elseif($menuid > 0){
			$rolemenu = $db->fetch_first("SELECT * FROM tbl_role_menu WHERE MENUID = '{$menuid}' AND ROLEID = '{$_var[current][ROLEID]}'");
			
			$menu_operations = unserialize($rolemenu['OPERATIONS']);
			if($menu_operations) {
				foreach ($operations as $key => $val) $operations[$key] = $menu_operations[$key] ? '' : 'style="display:none;"';
			}
		}
		
		$dispatches['operations'] = $operations;
		
		return $dispatches;
	}
	
	//获取可用图标
	public function get_micons(){
		return array(
		'micon-1', 
		'micon-2', 
		'micon-3', 
		'micon-4', 
		'micon-5', 
		'micon-6', 
		'micon-7', 
		'micon-8', 
		'micon-9', 
		'micon-10', 
		'micon-11', 
		'micon-12', 
		'micon-13', 
		'micon-14', 
		'micon-15', 
		'micon-16', 
		'micon-17', 
		'micon-18', 
		'micon-19', 
		'micon-20', 
		'micon-21', 
		'micon-22', 
		'micon-23', 
		'micon-24', 
		'micon-25', 
		'micon-26', 
		'micon-27', 
		'micon-28', 
		'micon-29', 
		'micon-30', 
		'micon-31', 
		'micon-32', 
		'micon-33', 
		'micon-34', 
		'micon-35', 
		'micon-36', 
		'micon-37', 
		'micon-38', 
		'micon-39', 
		'micon-40', 
		'micon-41', 
		'micon-42', 
		'micon-43', 
		'micon-44', 
		'micon-45', 
		'micon-46', 
		'micon-47', 
		'micon-48', 
		'micon-49', 
		'micon-50', 
		'micon-51', 
		'micon-52', 
		'micon-53', 
		'micon-54', 
		'micon-55', 
		'micon-56', 
		'micon-57', 
		'micon-58', 
		'micon-59', 
		'micon-60', 
		'micon-61', 
		'micon-62', 
		'micon-63', 
		'micon-64', 
		'micon-65', 
		'micon-66', 
		'micon-67', 
		'micon-68', 
		'micon-69', 
		'micon-70', 
		'micon-71', 
		'micon-72', 
		'micon-73', 
		'micon-74', 
		'micon-75', 
		'micon-76', 
		'micon-77', 
		'micon-78', 
		'micon-79', 
		'micon-80', 
		'micon-81', 
		'micon-82', 
		'micon-83', 
		'micon-84', 
		'micon-85', 
		'micon-86', 
		'micon-87', 
		'micon-88', 
		'micon-89', 
		'micon-90', 
		'micon-91', 
		'micon-92', 
		'micon-93', 
		'micon-94', 
		'micon-95', 
		'micon-96', 
		'micon-97', 
		'micon-98', 
		'micon-99', 
		'micon-100', 
		'micon-101', 
		'micon-102', 
		'micon-103', 
		'micon-104', 
		'micon-105', 
		'micon-106', 
		'micon-107', 
		'micon-108', 
		'micon-109', 
		'micon-110', 
		'micon-111', 
		'micon-112', 
		'micon-113', 
		'micon-114', 
		'micon-115', 
		'micon-116', 
		'micon-117', 
		'micon-118', 
		'micon-119', 
		'micon-120', 
		'micon-121', 
		'micon-122', 
		'micon-123', 
		'micon-124', 
		'micon-125', 
		'micon-126', 
		'micon-127', 
		'micon-128', 
		'micon-129', 
		'micon-130', 
		'micon-131', 
		'micon-132', 
		'micon-133', 
		'micon-134', 
		'micon-135', 
		'micon-136', 
		'micon-137', 
		'micon-138', 
		'micon-139', 
		'micon-140', 
		'micon-141', 
		'micon-142', 
		'micon-143', 
		'micon-144', 
		'micon-145', 
		'micon-146', 
		'micon-147', 
		'micon-148', 
		'micon-149', 
		'micon-150', 
		'micon-151', 
		'micon-152', 
		'micon-153', 
		'micon-154', 
		'micon-155', 
		'micon-156', 
		'micon-157', 
		'micon-158', 
		'micon-159', 
		'micon-160', 
		'micon-161', 
		'micon-162', 
		'micon-163', 
		'micon-164', 
		'micon-165', 
		'micon-166', 
		'micon-167', 
		'micon-168', 
		'micon-169', 
		'micon-170', 
		'micon-171', 
		'micon-172', 
		'micon-173', 
		'micon-174', 
		'micon-175', 
		'micon-176', 
		'micon-177', 
		'micon-178', 
		'micon-179', 
		'micon-180', 
		'micon-181', 
		'micon-182', 
		'micon-183', 
		'micon-184', 
		'micon-185', 
		'micon-186', 
		'micon-187', 
		'micon-188', 
		'micon-189', 
		'micon-190', 
		'micon-191', 
		'micon-192', 
		'micon-193', 
		'micon-194', 
		'micon-195', 
		'micon-196', 
		'micon-197', 
		'micon-198', 
		'micon-199', 
		'micon-200', 
		'micon-201', 
		'micon-202', 
		'micon-203', 
		'micon-204', 
		'micon-205', 
		'micon-206', 
		'micon-207', 
		'micon-208', 
		'micon-209', 
		'micon-210', 
		'micon-211', 
		'micon-212', 
		'micon-213', 
		'micon-214', 
		'micon-215', 
		'micon-216', 
		'micon-217', 
		'micon-218', 
		'micon-219', 
		'micon-220', 
		'micon-221', 
		'micon-222', 
		'micon-223', 
		'micon-224', 
		'micon-225', 
		'micon-226', 
		'micon-227', 
		'micon-228', 
		'micon-229', 
		'micon-230', 
		'micon-231', 
		'micon-232', 
		'micon-233', 
		'micon-234', 
		'micon-235', 
		'micon-236', 
		'micon-237', 
		'micon-238', 
		'micon-239', 
		'micon-240', 
		'micon-241', 
		'micon-242', 
		'micon-243', 
		'micon-244', 
		'micon-245', 
		'micon-246', 
		'micon-247', 
		'micon-248', 
		'micon-249', 
		'micon-250', 
		'micon-251', 
		'micon-252', 
		'micon-253', 
		'micon-254', 
		'micon-255', 
		'micon-256', 
		'micon-257', 
		'micon-258', 
		'micon-259', 
		'micon-260', 
		'micon-261', 
		'micon-262', 
		'micon-263', 
		'micon-264', 
		'micon-265', 
		'micon-266', 
		'micon-267', 
		'micon-268', 
		'micon-269', 
		'micon-270', 
		'micon-271', 
		'micon-272', 
		'micon-273', 
		'micon-274', 
		'micon-275', 
		'micon-276', 
		'micon-277', 
		'micon-278', 
		'micon-279', 
		'micon-280', 
		'micon-281', 
		'micon-282', 
		'micon-283', 
		'micon-284', 
		'micon-285', 
		'micon-286', 
		'micon-287', 
		'micon-288', 
		'micon-289', 
		'micon-290', 
		'micon-291', 
		'micon-292', 
		'micon-293', 
		'micon-294', 
		'micon-295', 
		'micon-296', 
		'micon-297', 
		'micon-298', 
		'micon-299', 
		'micon-300', 
		'micon-301', 
		'micon-302', 
		'micon-303', 
		'micon-304', 
		'micon-305', 
		'micon-306', 
		'micon-307', 
		'micon-308', 
		'micon-309', 
		'micon-310', 
		'micon-311', 
		'micon-312', 
		'micon-313', 
		'micon-314', 
		'micon-315', 
		'micon-316', 
		'micon-317', 
		'micon-318', 
		'micon-319', 
		'micon-320', 
		'micon-321', 
		'micon-322', 
		'micon-323', 
		'micon-324', 
		'micon-325', 
		'micon-326', 
		'micon-327', 
		'micon-328', 
		'micon-329', 
		'micon-330', 
		'micon-331', 
		'micon-332', 
		'micon-333', 
		'micon-334', 
		'micon-335', 
		'micon-336', 
		'micon-337', 
		'micon-338', 
		'micon-339', 
		'micon-340', 
		'micon-341', 
		'micon-342', 
		'micon-343', 
		'micon-344', 
		'micon-345', 
		'micon-346', 
		'micon-347', 
		'micon-348', 
		'micon-349', 
		'micon-350', 
		'micon-351', 
		'micon-352', 
		'micon-353', 
		'micon-354', 
		'micon-355', 
		'micon-356', 
		'micon-357', 
		'micon-358', 
		'micon-359', 
		'micon-360', 
		'micon-361', 
		'micon-362', 
		'micon-363', 
		'micon-364', 
		'micon-365', 
		'micon-366', 
		'micon-367', 
		'micon-368', 
		'micon-369', 
		'micon-370', 
		'micon-371', 
		'micon-372', 
		'micon-373', 
		'micon-374', 
		'micon-375', 
		'micon-376', 
		'micon-377', 
		'micon-378', 
		'micon-379', 
		'micon-380', 
		'micon-381', 
		'micon-382', 
		'micon-383', 
		'micon-384', 
		'micon-385', 
		'micon-386', 
		'micon-387', 
		'micon-388', 
		'micon-389', 
		'micon-390', 
		'micon-391', 
		'micon-392', 
		'micon-393', 
		'micon-394', 
		'micon-395', 
		'micon-396', 
		'micon-397', 
		'micon-398', 
		'micon-399', 
		'micon-400', 
		'micon-401', 
		'micon-402', 
		'micon-403', 
		'micon-404', 
		'micon-405', 
		'micon-406', 
		'micon-407', 
		'micon-408', 
		'micon-409', 
		'micon-410', 
		'micon-411', 
		'micon-412', 
		'micon-413', 
		'micon-414', 
		'micon-415', 
		'micon-416', 
		'micon-417', 
		'micon-418', 
		'micon-419', 
		'micon-420', 
		'micon-421', 
		'micon-422', 
		'micon-423', 
		'micon-424', 
		'micon-425', 
		'micon-426', 
		'micon-427', 
		'micon-428', 
		'micon-429', 
		'micon-430', 
		'micon-431', 
		'micon-432', 
		'micon-433', 
		'micon-434', 
		'micon-435', 
		'micon-436', 
		'micon-437', 
		'micon-438', 
		'micon-439', 
		'micon-440', 
		'micon-441', 
		'micon-442', 
		'micon-443', 
		'micon-444', 
		'micon-445', 
		'micon-446', 
		'micon-447', 
		'micon-448', 
		'micon-449', 
		'micon-450', 
		'micon-451');
	}

	//获取操作列表
	public function get_operations(){
		return array(
		'add' => $GLOBALS['lang']['admin.operation.add'],
		'edit' => $GLOBALS['lang']['admin.operation.edit'],
		'delete' => $GLOBALS['lang']['admin.operation.delete'],
		'search' => $GLOBALS['lang']['admin.operation.search'],
		'audit' => $GLOBALS['lang']['admin.operation.audit'],
		'setup' => $GLOBALS['lang']['admin.operation.setup'],
		'import' => $GLOBALS['lang']['admin.operation.import'],
		'export' => $GLOBALS['lang']['admin.operation.export'],
		'restore' => $GLOBALS['lang']['admin.operation.restore'],
		'back' => $GLOBALS['lang']['admin.operation.back'], 
		'cancel' => $GLOBALS['lang']['admin.operation.cancel'],
		'printer' => $GLOBALS['lang']['admin.operation.printer'],
		'selling' => $GLOBALS['lang']['admin.operation.selling'],
		'selled' => $GLOBALS['lang']['admin.operation.selled'],
		'move' => $GLOBALS['lang']['admin.operation.move'], 
		'send' => $GLOBALS['lang']['admin.operation.send'],
		'copy' => $GLOBALS['lang']['admin.operation.copy'], 
		'split' => $GLOBALS['lang']['admin.operation.split'], 
		'update' => $GLOBALS['lang']['admin.operation.update'], 
		'install' => $GLOBALS['lang']['admin.operation.install']
		);
	}

	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		$menu = $db->fetch_first("SELECT * FROM tbl_menu WHERE MENUID = '{$id}'");
		if($menu) $menu['OPERATIONS'] = unserialize($menu['OPERATIONS']);
		
		return $menu;
	}
	
	//根据URL获取记录
	public function get_by_url($url){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_menu WHERE URL = '{$url}'");
	}
	
	//获取数量 
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_menu WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($wheresql = ''){
		global $db;
		
		$rows = array();
		$temp_query = $db->query("SELECT * FROM tbl_menu WHERE 1 {$wheresql} ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['TYPE'] = $row['PARENTID'] ? '—' : $GLOBALS['lang']['admin.menu_edit.view.type.'.$row['TYPE']];
			$row['DEPTH'] = count(explode(',,', $row['PATH']));
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取用户获取菜单
	public function get_list_of_user($user, $where = ''){
		global $db;
		
		if(!$user) return array();
		
		$menus = array();
		if($user['USERID'] == -1) $temp_query = $db->query("SELECT * FROM tbl_menu m WHERE 1 {$where} ORDER BY DISPLAYORDER ASC");
		else $temp_query = $db->query("SELECT m.* FROM tbl_menu m ,tbl_role_menu rm WHERE m.MENUID = rm.MENUID AND rm.ROLEID = '{$user[ROLEID]}' {$where} ORDER BY DISPLAYORDER ASC");
		
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['URL'] = str_replace('{$ADMIN_SCRIPT}', ADMIN_SCRIPT, $row['URL']);
			$menus[$row['MENUID']] = $row;
		}
		
		return $menus;
	}
	
	//根据父级ID获取子菜单
	public function get_children($parentid){
		global $db;
		
		$rows = array();
		$temp_query = $db->query("SELECT * FROM tbl_menu WHERE PARENTID = '$parentid' ORDER BY DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//根据角色ID获取菜单树
	public function get_all($roleid = 0){
		global $db;
		
		$temp_menu_ids = array();
		$temp_child_menu_ids = array();
		$temp_child_child_menu_ids = array();
		
		$rows = array();
		
		$wheresql = $roleid > 0 ? " AND b.ROLEID = '{$roleid}'" : '';
		
		$temp_query = $db->query("SELECT a.* FROM tbl_menu a LEFT JOIN tbl_role_menu b ON a.MENUID = b.MENUID WHERE a.PARENTID = '0' {$wheresql} ORDER BY a.DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['OPERATIONS'] = unserialize($row['OPERATIONS']);
			$row['MENUS'] = array();
			$rows[$row['MENUID']] = $row;
			
			$temp_menu_ids[] = $row['MENUID'];
		}
		
		$temp_query = $db->query("SELECT a.* FROM tbl_menu a LEFT JOIN tbl_role_menu b ON a.MENUID = b.MENUID WHERE a.PARENTID IN(".eimplode($temp_menu_ids).") {$wheresql}  ORDER BY a.PARENTID ASC, a.DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['OPERATIONS'] = unserialize($row['OPERATIONS']);
			$row['MENUS'] = array();
			
			if($rows[$row['PARENTID']]) {
				$rows[$row['PARENTID']]['MENUS'][$row['MENUID']] = $row;
				
				$temp_child_menu_ids[] = $row['MENUID'];
			}
		}
		
		$temp_query = $db->query("SELECT a.* FROM tbl_menu a LEFT JOIN tbl_role_menu b ON a.MENUID = b.MENUID WHERE a.PARENTID IN(".eimplode($temp_child_menu_ids).") {$wheresql}  ORDER BY a.PARENTID ASC, a.DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['OPERATIONS'] = unserialize($row['OPERATIONS']);
			foreach ($rows as $key => $menu){
				if($menu['MENUS'][$row['PARENTID']]) {
					$rows[$key]['MENUS'][$row['PARENTID']]['MENUS'][$row['MENUID']] = $row;
					
					$temp_child_child_menu_ids[] = $row['MENUID'];
				}
			}
		}
		
		$temp_query = $db->query("SELECT a.* FROM tbl_menu a LEFT JOIN tbl_role_menu b ON a.MENUID = b.MENUID WHERE a.PARENTID IN(".eimplode($temp_child_child_menu_ids).") {$wheresql}  ORDER BY a.PARENTID ASC, a.DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['OPERATIONS'] = unserialize($row['OPERATIONS']);
			$crumbs = explode(',', str_replace(',,', ',', $row['PATH']));
			foreach ($rows as $key => $menu){
				if($menu['MENUS'][$crumbs[2]]['MENUS'][$row['PARENTID']]) {
					$rows[$key]['MENUS'][$crumbs[2]]['MENUS'][$crumbs[3]]['MENUS'][$row['MENUID']] = $row;
				}
			}
			
			unset($crumbs);
		}
		
		return $rows;
	}
	
	//获取路径
	public function get_crumbs($menu){
		global $db;
		
		$crumbs = array();
		if(!$menu) return $crumbs;
		
		$temp_query = $db->query("SELECT MENUID, CNAME, URL, ICON FROM tbl_menu WHERE INSTR('{$menu[PATH]}', PATH) > 0 ORDER BY MENUID ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$crumbs[] = $row;
		}
		
		return $crumbs;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_menu', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_menu', $data, "MENUID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_menu', "MENUID = '{$id}'");
		$db->delete('tbl_role_menu', "MENUID = '{$id}'");
	}
	
}
?>