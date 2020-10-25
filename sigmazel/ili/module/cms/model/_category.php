<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\model;

/**
 * 分类
 * @author sigmazel
 * @since v1.0.2
 */
class _category{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_category WHERE CATEGORYID = '{$id}'");
	}
	
	//根据名称获取记录+类型
	public function get_by_cname($cname, $type = 'article'){
		global $db;
		
		$wheresql = $type ? " AND TYPE = '{$type}'" : '';
		
		return $db->fetch_first("SELECT * FROM tbl_category WHERE CNAME = '{$cname}' {$wheresql}");
	}
	
	//根据标识号获取记录+类型
	public function get_by_identity($identity, $type = 'article'){
		global $db;
		
		$wheresql = $type ? " AND TYPE = '{$type}'" : '';
		
		return $db->fetch_first("SELECT * FROM tbl_category WHERE IDENTITY = '{$identity}' {$wheresql}");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
	
		return $db->result_first("SELECT COUNT(1) FROM tbl_category a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.DISPLAYORDER ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.* FROM tbl_category a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['URL'] = str_replace('{ID}', $row['CATEGORYID'], $row['URL']);
			$row['URL'] = str_replace('{NO}', $row['IDENTITY'], $row['URL']);
			
			$row = format_row_files($row);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取用户分类列表
	public function get_list_of_user($wheresql = ''){
		global $db;
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_category a, tbl_user_category b WHERE a.CATEGORYID = b.CATEGORYID {$wheresql} ORDER BY a.PARENTID ASC, a.DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['URL'] = str_replace('{ID}', $row['CATEGORYID'], $row['URL']);
			$row['URL'] = str_replace('{NO}', $row['IDENTITY'], $row['URL']);
			
			$row = format_row_files($row);
			
			$rows[$row['CATEGORYID']] = $row;
		}
		
		return $rows;
	}
	
	//获取子分类列表
	public function get_children_by_id($parentid, $type = 'article'){
		global $db;
		
		$wheresql = $type ? " AND a.TYPE = '{$type}'" : '';
		$parentid != -1 && $wheresql .= " AND a.PARENTID = '{$parentid}'";
		
		return $this->get_list(0, 0, $wheresql);
	}
	
	//根据标识号获取子分类列表
	public function get_children_by_identity($identity, $type = 'article'){
		global $db;
		
		$wheresql = $type ? "AND b.TYPE = '{$type}'" : '';
		$wheresql .= "AND b.IDENTITY = '{$identity}'";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_category a, tbl_category b WHERE a.PARENTID = b.CATEGORYID {$wheresql} ORDER BY a.DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['URL'] = str_replace('{ID}', $row['CATEGORYID'], $row['URL']);
			$row['URL'] = str_replace('{NO}', $row['IDENTITY'], $row['URL']);
			
			$row = format_row_files($row);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取所有记录 $param 可以是分类ID或分类路径
	public function get_all($param, $type = 'article', $key = true){
		global $db;
		
		$wheresql = $type ? " AND a.TYPE = '{$type}'" : '';
		
		if(is_cint($param) && $param > 0) $wheresql .= "AND a.PATH LIKE ',{$param},,%'";
		elseif(strexists($param, ',')) $wheresql .= "AND a.PATH LIKE '{$param}%'";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT * FROM tbl_category a WHERE 1 {$wheresql} ORDER BY PARENTID ASC, DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['URL'] = str_replace('{ID}', $row['CATEGORYID'], $row['URL']);
			$row['URL'] = str_replace('{NO}', $row['IDENTITY'], $row['URL']);
			
			$row = format_row_files($row);
			
			if($key) $rows[$row['IDENTITY']] = $row;
			else $rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取路径 $param 可以是分类ID或分类
	public function get_crumbs($param){
		global $db;
		
		if(is_cint($param)) $category = $db->fetch_first("SELECT PATH FROM tbl_category WHERE CATEGORYID = '{$param}'");
		elseif(is_array($param)) $category = $param;
		
		$crumbs = array();
		$temp_query = $db->query("SELECT CATEGORYID, CNAME, IDENTITY, CHILDREN, FILE01, FILE02 FROM tbl_category WHERE INSTR('{$category[PATH]}', PATH) > 0 ORDER BY LENGTH(PATH) ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['URL'] = str_replace('{ID}', $row['CATEGORYID'], $row['URL']);
			$row['URL'] = str_replace('{NO}', $row['IDENTITY'], $row['URL']);
			
			$row = format_row_files($row);
			
			$crumbs[] = $row;
		}
		
		return $crumbs;
	}
	
	//获取二级分类树
	public function get_tree($parentid, $type = 'article'){
		global $db;
		
		$wheresql =  $type ? "AND a.TYPE = '{$type}'" : '';
		
		$ids = array();
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_category a WHERE a.PARENTID = '{$parentid}' {$wheresql} ORDER BY a.DISPLAYORDER ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['_COMMENT'] = $row['COMMENT'];
			$temp_arr = explode('|', $row['COMMENT']);
			$row['COMMENT'] = $temp_arr[0];
			
			$row['URL'] = str_replace('{ID}', $row['CATEGORYID'], $row['URL']);
			$row['URL'] = str_replace('{NO}', $row['IDENTITY'], $row['URL']);
			
			$row = format_row_files($row);
			
			$row['CATEGORIES'] = array();
			$rows['c_'.$row['CATEGORYID']] = $row;
			$ids[] = $row['CATEGORYID'];
			
			unset($temp_arr);
		}
		
		if(count($ids) > 0){
			$temp_query = $db->query("SELECT a.* FROM tbl_category a WHERE a.PARENTID IN(".eimplode($ids).") ORDER BY a.PARENTID ASC, a.DISPLAYORDER ASC");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$row['URL'] = str_replace('{ID}', $row['CATEGORYID'], $row['URL']);
				$row['URL'] = str_replace('{NO}', $row['IDENTITY'], $row['URL']);
				
				$row = format_row_files($row);
				
				$rows['c_'.$row['PARENTID']]['CATEGORIES'][] = $row;
			}
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_category', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_category', $data, "CATEGORYID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_category', "CATEGORYID = '{$id}'");
		$db->delete('tbl_category', "PARENTID = '{$id}'");
		$db->delete('tbl_user_category', "CATEGORYID = '{$id}'");
	}
	
	/**
	 * 清空子类楼层
	 * @param array $category
	 */
	public function clear_floor($category){
	    global $db;
	    
	    $temp_query = $db->query("SELECT * FROM tbl_category a WHERE a.PATH LIKE '{$category[PATH]},%'");
	    while(($row = $db->fetch_array($temp_query)) !== false){
	        $columns = unserialize($row['COLUMNS']);
	        $columns['FLOOR'] = 0;
	        $columns['TAG'] = $columns['TAG'] + 0;
	        $columns = serialize($columns);
	        
	        $db->update('tbl_category', array('COLUMNS' => $columns), "CATEGORYID = '{$row[CATEGORYID]}'");
	        
	        unset($columns);
	    }
	}
	
	//格式化
	public function format($category){
		$columns = array();
		
		if($category['COLUMNS']){
			$category['COLUMNS'] = substr($category['COLUMNS'], 0, 1) == '#' ? explode('#', $category['COLUMNS']) : unserialize($category['COLUMNS']);
		}else $category['COLUMNS'] = array();
		
		if($category['TYPE'] == 'article'){
    		foreach($category['COLUMNS'] as $key => $val){
    			$tmparr = explode('|', $val);
    			if(!$tmparr[0] || count($tmparr) != 3) continue;
    			if(!$tmparr[1] && $tmparr[2] == 0) continue;
    			
    			if($tmparr[0] == 'CONTENT') $columns[$tmparr[0]] = array('text' => $tmparr[1], 'type' => $tmparr[2]);
    			else $columns[$tmparr[0]] = array('text' => $tmparr[1], 'show' => $tmparr[2]);
    			
    			unset($tmparr);
    		}
    		
    		if($category['FILES']){
    			$files = explode('|', $category['FILES']);
    			
    			if(!$files[1] && $files[2] == 0) $category['FILES'] = array();
    			else $category['FILES'] = array('text' => $files[0], 'show' => $files[1]);
    		}
    		
    		$category['COLUMNS'] = $columns;
		}
		
		return $category;
	}

	//分类多条标签
    public function block_multi($json){
        $params = json_decode($json, 1);

        if(empty($params['identity'])) return $this->get_children_by_id(0);
        else{
            $identity = $params['identity'];

            if(substr($params['identity'], 0, 1) == '$'){
                if(strpos($params['identity'], '[') === false){
                    $identity = $GLOBALS[substr($params['identity'], 1)];
                }else{
                    $identity_var = substr($params['identity'], 1, strpos($params['identity'], '[') - 1);
                    $identity_key = substr($params['identity'], strpos($params['identity'], '['));
                    $identity_key = str_replace(array('[', ']'), '', $identity_key);

                    $identity = $GLOBALS[$identity_var][$identity_key];
                }
            }

            return $this->get_children_by_identity($identity);
        }
    }

    //分类路径标签
    public function block_crumbs($json){
        $params = json_decode($json, 1);

        if(empty($params['identity'])) return null;

        $identity = $params['identity'];

        if(substr($params['identity'], 0, 1) == '$'){
            if(strpos($params['identity'], '[') === false){
                $identity = $GLOBALS[substr($params['identity'], 1)];
            }else{
                $identity_var = substr($params['identity'], 1, strpos($params['identity'], '[') - 1);
                $identity_key = substr($params['identity'], strpos($params['identity'], '['));
                $identity_key = str_replace(array('[', ']'), '', $identity_key);

                $identity = $GLOBALS[$identity_var][$identity_key];
            }

            return $this->get_crumbs($identity);
        }

        $category = $this->get_by_identity($identity);
        return $this->get_crumbs($category);
    }

    //分类单条标签
    public function block_one($json){
        global $_var;

        $params = json_decode($json, 1);

        if(!is_ansi($params['param'])) return null;

        if($params['type'] == 'identity'){
            if(!is_ansi($_var['gp_'.$params['param']])) return null;
            $category = $this->get_by_identity($_var['gp_'.$params['param']]);
        }elseif($params['type'] == 'id'){
            $id = $_var['gp_'.$params['param']] + 0;
            if($id == 0) return null;

            $category = $this->get_by_id($id);
        }else{
            if(!is_ansi($params['param'])) return null;

            $category = $this->get_by_identity($params['param']);
        }

        if(!$category) return null;

        $category = format_row_files($category);
        $category = $this->format($category);

        return $category;
    }
}
?>