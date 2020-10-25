<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/

class contentFunc {
    public static $data    = null; //应用信息接口
    public static $apps    = null;//应用信息
    public static $tables  = null;//应用表信息
    public static $app     = null;
    public static $appid   = 0;
    public static $table   = null;
    public static $primary = null;
    /**
    * 已在 categoryApp contentApp 设置数据回调,
    * 在应用范围内可以不用设置 app="应用名/应用ID"
    **/
    public static function interfaced($data=null) {
        self::$data = $data;
    }
    private static function data($vars,$func='list'){
        if((empty($vars['app'])||$vars['app']=='content') && self::$data){
            $vars['app'] = self::$data['app'];
        }
        if(isset($vars['apps']) && is_array($vars['apps'])){
            $vars['app'] = $vars['apps']['app'];
        }
        $ap = isset($vars['appid'])?$vars['appid']:$vars['app'];

        if(empty($ap)||$ap=='content'){
            iUI::warning('iCMS&#x3a;content&#x3a;'.$func.' 标签出错! 缺少参数"app"或"app"值为空.');
        }

        self::$apps = apps::get_app($ap);

        empty(self::$apps) && iUI::warning('iCMS&#x3a;content&#x3a;'.$func.' 标签出错! 缺少参数"app"或"app"值为空.');

        self::$tables  = apps::get_table(self::$apps);
        self::$app     = self::$apps['app'];
        self::$appid   = self::$apps['id'];
        self::$table   = self::$tables['table'];
        self::$primary = self::$tables['primary'];
    }
    public static function content_category($vars) {
        $vars['apps'] = self::$data['app'];
        return categoryFunc::category_list($vars);
    }
    public static function content_list($vars) {
        self::data($vars,'list');

        if ($vars['loop'] === "rel" && empty($vars['id'])) {
            return false;
        }
        iMap::reset();

        $resource  = array();
        $map_where = array();
        $status    = '1';
        isset($vars['status']) && $status = (int) $vars['status'];
        $where_sql = "WHERE `status`='{$status}'";
        $vars['call'] == 'user' && $where_sql .= " AND `postype`='0'";
        $vars['call'] == 'admin' && $where_sql .= " AND `postype`='1'";
        $maxperpage = isset($vars['row']) ? (int) $vars['row'] : 10;
        $cache_time = isset($vars['time']) ? (int) $vars['time'] : -1;
        isset($vars['userid']) && $where_sql .= " AND `userid`='{$vars['userid']}'";
        isset($vars['weight']) && $where_sql .= " AND `weight`='{$vars['weight']}'";

        if (isset($vars['ucid']) && $vars['ucid'] != '') {
            $where_sql .= " AND `ucid`='{$vars['ucid']}'";
        }

		$hidden = categoryApp::get_cache('hidden');
		$hidden && $hidden_sql = true;
        if (isset($vars['cid!'])) {
            $ncids = explode(',', $vars['cid!']);
            $vars['sub'] && $ncids += categoryApp::get_cids($ncids, true);
            $where_sql .= iSQL::in($ncids, 'cid', 'not');
        }
        if ($vars['cid'] && !isset($vars['cids'])) {
            $cid = explode(',', $vars['cid']);
			$vars['sub'] && $cid += categoryApp::get_cids($cid, true,$hidden);
            $where_sql .= iSQL::in($cid, 'cid');
			$hidden_sql = false;
        }
        if (isset($vars['cids']) && !$vars['cid']) {
            $cids = explode(',', $vars['cids']);
			$vars['sub'] && $cids += categoryApp::get_cids($vars['cids'], true,$hidden);
			$hidden_sql = false;
            if ($cids) {
				iMap::init('category', self::$appid,'cid');
                $map_where += iMap::where($cids);
            }
        }
		$hidden_sql && $where_sql .= iSQL::in($hidden, 'cid', 'not');

        if (isset($vars['pid']) && !isset($vars['pids'])) {
            iSQL::$check_numeric = true;
            $where_sql .= iSQL::in($vars['pid'], 'pid');
        }
        if(isset($vars['pid!'])){
            iSQL::$check_numeric = true;
            $where_sql.= iSQL::in($vars['pid!'],'pid','not');
        }
        if (isset($vars['pids']) && !isset($vars['pid'])) {
			iMap::init('prop', self::$appid,'pid');
            $map_where += iMap::where($vars['pids']);
		}
        if (isset($vars['tag']) && is_array($vars['tag'])) {
            $tids  = $vars['tag']['id'];
            $field = $vars['tag']['field'];
            if(is_array($vars['tag'][0])){
                $tids  = array_column($vars['tag'], 'id');
                $field = $vars['tag'][0]['field'];
            }
            iMap::init('tag', self::$appid,$field);
            $map_where += iMap::where($tids);
        }
		if (isset($vars['tids'])) {
			iMap::init('tag', self::$appid,'tags');
			$map_where += iMap::where($vars['tids']);
		}
        if (isset($vars['keywords'])) {
			if (empty($vars['keywords'])) {
				return;
			}
            if (strpos($vars['keywords'], ',') === false) {
                $vars['keywords'] = str_replace(array('%', '_'), array('\%', '\_'), $vars['keywords']);
                $where_sql .= " AND CONCAT(title) like '%" . addslashes($vars['keywords']) . "%'";
            } else {
                $pieces   = explode(',', $vars['keywords']);
                $pieces   = array_filter ($pieces);
                $pieces   = array_map('addslashes', $pieces);
                $keywords = implode('|', $pieces);
                $where_sql.= " AND CONCAT(title) REGEXP '$keywords' ";
            }
        }
        $distinct_stack = -1;
        if (isset($vars['tag[]']) && is_array($vars['tag[]'])) {
            $tagArray = $vars['tag[]'];
            $fieldsArray = former::fields(self::$apps['fields']);
            foreach ($tagArray as $field => $tid) {
                $FA = $fieldsArray[$field];
                if($FA && $FA['type']=='tag'){
                    iMap::init('tag', self::$appid,$field);
                    $_map_where = (array)iMap::where($tid);
                    $map_where  = array_merge($map_where,$_map_where);
                    unset($_map_where);
                    $distinct_stack++;
                }
            }
        }

        if(isset($vars['prop[]']) && is_array($vars['prop[]'])){
            $propArray = $vars['prop[]'];
            isset($fieldsArray) OR $fieldsArray = former::fields(self::$apps['fields']);
            foreach ($propArray as $field => $value) {
                $FA = $fieldsArray[$field];
                if($FA && in_array($FA['type'], array('radio_prop','multi_prop','prop'))){
                    if($FA['multiple']){
                        $distinct_stack++;
                        iMap::init('prop', self::$appid,$field);
                        $_map_where = (array)iMap::where($value);
                        $map_where  = array_merge($map_where,$_map_where);
                        unset($_map_where);
                    }else{
                        $where_sql .= iSQL::in($value, $field);
                    }
                }
            }
        }
        $distinct = '';
        $distinct_stack>0  && $vars['distinct'] = true;
        $vars['distinct']&& $distinct = ' DISTINCT ';

        $vars['id'] && $where_sql .= iSQL::in($vars['id'], 'id');
        $vars['id!'] && $where_sql .= iSQL::in($vars['id!'], 'id', 'not');
		$by = strtoupper($vars['by']) == "ASC" ? "ASC" : "DESC";

        switch ($vars['orderby']) {
            case "id":       $order_sql = " ORDER BY `id` $by"; break;
            case "hot":      $order_sql = " ORDER BY `hits` $by"; break;
			case "today":    $order_sql = " ORDER BY `hits_today` $by"; break;
			case "yday":     $order_sql = " ORDER BY `hits_yday` $by"; break;
            case "week":     $order_sql = " ORDER BY `hits_week` $by"; break;
            case "month":    $order_sql = " ORDER BY `hits_month` $by"; break;
			case "good":  	 $order_sql = " ORDER BY `good` $by"; break;
            case "comment":  $order_sql = " ORDER BY `comments` $by"; break;
            case "pubdate":  $order_sql = " ORDER BY `pubdate` $by"; break;
            case "sort": $order_sql = " ORDER BY `sortnum` $by"; break;
            case "weight":   $order_sql = " ORDER BY `weight` $by"; break;
            default:$order_sql = " ORDER BY `id` $by";
        }
        isset($vars['startdate']) && $where_sql .= " AND `pubdate`>='" . strtotime($vars['startdate']) . "'";
        isset($vars['enddate']) && $where_sql .= " AND `pubdate`<='" . strtotime($vars['enddate']) . "'";
        isset($vars['where']) && $where_sql .= ' AND '.ltrim(trim($vars['where']),'AND');;
		isset($vars['where[]']) && $where_sql .= iSQL::where($vars['where[]'],true);

        if($map_where){
			$map_sql = iSQL::select_map($map_where, 'join');
			//join
			//empty($vars['cid']) && $map_order_sql = " ORDER BY map.`iid` $by";
			$map_table = 'map';
			$vars['map_order_table'] && $map_table = $vars['map_order_table'];
			$map_order_sql = " ORDER BY {$map_table}.`iid` $by";
			//$map_order_sql = " ORDER BY `".self::$table."`.`id` $by";
			//
			$where_sql .= ' AND ' . $map_sql['where'];
			$where_sql = ",{$map_sql['from']} {$where_sql} AND `".self::$table."`.`id` = {$map_table}.`iid`";
			//derived
			// $where_sql = ",({$map_sql}) map {$where_sql} AND `id` = map.`iid`";
		}
		$offset = (int)$vars['offset'];
		if ($vars['page']) {
            $countField = '*';
            $distinct && $countField = "DISTINCT `".self::$table."`.id";
			$total_type = $vars['total_cache'] ? 'G' : null;
			$total      = (int)$vars['total'];
			if(isset($vars['pageNum'])){
				$total = (int)$vars['pageNum']*$maxperpage;
			}
			if(!isset($vars['total']) && !isset($vars['pageNum'])){
				$total = iPagination::totalCache(
					"SELECT count(".$countField.") FROM `".self::$table."` {$where_sql}",
					$total_type,
					iCMS::$config['cache']['page_total']
				);
			}

			$pagenav    = isset($vars['pagenav']) ? $vars['pagenav'] : "pagenav";
			$pnstyle    = isset($vars['pnstyle']) ? $vars['pnstyle'] : 0;
			$multi      = iPagination::make(array(
				'total_type' => $total_type,
				'total'      => $total,
				'perpage'    => $maxperpage,
				'unit'       => iUI::lang('iCMS:page:list'),
				'nowindex'   => $GLOBALS['page']
			));
			$offset     = $multi->offset;
            iView::assign(self::$app."_list_total", $total);
		}
		$limit = "LIMIT {$offset},{$maxperpage}";
		//随机特别处理
		if ($vars['orderby'] == 'rand') {
			$ids_array = iSQL::get_rand_ids(self::$table, $where_sql, $maxperpage, 'id');
			if ($map_order_sql) {
				$map_order_sql = " ORDER BY `".self::$table."`.`id` $by";
			}
		}
        $map_order_sql && $order_sql = $map_order_sql;

		if ($vars['cache']) {
            $hash = md5(json_encode($vars) . $order_sql);
            $cache_name = iPHP_DEVICE . '/'.self::$app.'/'.$hash.'/'.$offset.'_'.$maxperpage;
            isset($vars['cache_name']) && $cache_name = $vars['cache_name'];
            $c_resource = iCache::get($cache_name);
            if(is_array($c_resource)) return $c_resource;
		}

		if (empty($ids_array)) {
			$sql = "SELECT ".$distinct." `".self::$table."`.`id` FROM `".self::$table."` {$where_sql} {$order_sql} {$limit}";
			if (!$vars['page'] && strpos($sql, '`cid` IN')!==false && empty($map_order_sql) && iCMS::$config['debug']['db_optimize_in']){
				$sql = iSQL::optimize_in($sql);
			}
			$ids_array = iDB::all($sql);
		}

		if ($ids_array) {
			$ids = iSQL::values($ids_array);
			$ids = $ids ? $ids : '0';
			$where_sql = "WHERE `".self::$table."`.`id` IN({$ids})";
			$order_sql = "ORDER BY FIELD(`id`,{$ids})";
			$limit = '';

            $resource = iDB::all("SELECT `".self::$table."`.* FROM `".self::$table."` {$where_sql} {$order_sql} {$limit}");
            $resource = contentFunc::content_array($vars, $resource);
		}
		$vars['cache'] && iCache::set($cache_name, $resource, $cache_time);
		return $resource;
	}
    public static function content_prev($vars) {
        $vars['order'] = 'p';
        return contentFunc::content_next($vars);
    }
    public static function content_next($vars) {
        self::data($vars,'next');

        empty($vars['order']) && $vars['order'] = 'n';

		$cache_time = isset($vars['time']) ? (int) $vars['time'] : -1;
		if (isset($vars['cid'])) {
			$sql = " AND `cid`='{$vars['cid']}' ";
		}
		$field = '`id`';
		if ($vars['order'] == 'p') {
			$field = 'max(id)'; //INNODB
			$sql .= " AND `id` < '{$vars['id']}'";
			// $sql .= " AND `id` < '{$vars['id']}' ORDER BY id DESC LIMIT 1";//MyISAM
		} else if ($vars['order'] == 'n') {
			$field = 'min(id)';//INNODB
			$sql .= " AND `id` > '{$vars['id']}'";
			// $sql .= " AND `id` > '{$vars['id']}' ORDER BY id ASC LIMIT 1";//MyISAM
		}
		$hash = md5($sql);
		if ($vars['cache']) {
            $cache_name = iPHP_DEVICE . '/'.self::$app.'/' . $hash;
            $c_resource = iCache::get($cache_name);
            if(is_array($c_resource)) return $c_resource;
		}

		$rs = iDB::row("
			SELECT * FROM `".self::$table."` WHERE `id` =(SELECT {$field} FROM `".self::$table."` WHERE `status`='1' {$sql})
		");
		if ($rs) {
			$category = categoryApp::get_cache_cid($rs->cid);
			$resource = array(
				'id'    => $rs->id,
				'title' => $rs->title,
				'pic'   => filesApp::get_pic($rs->pic),
				'url'   => iURL::get(self::$app, array((array) $rs, $category))->href,
			);
		}
		$vars['cache'] && iCache::set($cache_name, $resource, $cache_time);

		return $resource;
	}
    private static function content_array($vars, $variable) {
        $resource = array();
        if ($variable) {
            $contentApp = new contentApp(self::$app);
	        if($vars['data']||$vars['pics']){
                $idArray = iSQL::values($variable,'id','array',null);
                $idArray && $content_data = (array)$contentApp->data($idArray);
                unset($idArray);
            }
            if($vars['meta']){
                $idArray = iSQL::values($variable,'id','array',null);
                $idArray && $meta_data = (array)apps_meta::data(self::$app,$idArray);
                unset($idArray);
            }
            if($vars['tags']){
                $tagArray = iSQL::values($variable,'tags','array',null,'id');
                $tagArray && $tags_data = (array)tagApp::multi_tag($tagArray);
                unset($tagArray);
	            $vars['tag'] = false;
            }
            foreach ($variable as $key => $value) {
                $value = $contentApp->value($value,$vars);

                if ($value === false) {
                    continue;
                }
                if(($vars['data']||$vars['pics']) && $content_data){
                    $value['data']  = (array)$content_data[$value['id']];
	                if($vars['pics']){
						$value['pics'] = filesApp::get_content_pics($value['data']['body']);
						if(!$value['data']){
							unset($value['data']);
						}
	                }
                }

                if($vars['tags'] && $tags_data){
                    $value+= (array)$tags_data[$value['id']];
                }
                if($vars['meta'] && $meta_data){
                    $value+= (array)$meta_data[$value['id']];
                }

                if ($vars['page']) {
                    $value['page'] = $GLOBALS['page'] ? $GLOBALS['page'] : 1;
                    $value['total'] = $total;
                }
                $resource[$key] = $value;
            }
            $vars['keys'] && iSQL::pickup_keys($resource,$vars['keys'],$vars['is_remove_keys']);
        }
        return $resource;
    }
}
