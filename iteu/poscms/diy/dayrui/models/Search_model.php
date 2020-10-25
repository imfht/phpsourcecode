<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class Search_model extends CI_Model {

    public $link;
    public $tablename;
    public $indexname;

    /**
     * 搜索模型类
     */
    public function __construct() {
        parent::__construct();

        $this->tablename = $this->db->dbprefix(SITE_ID.'_'.$this->dir.'_search');
        $this->indexname = $this->db->dbprefix(SITE_ID.'_'.$this->dir.'_search_index');
        $this->sys_field = array(
            'id' => '',
            'uid' => '',
            'hits' => '',
            'catid' => '',
            'author' => '',
            'inputtime' => '',
            'updatetime' => '',
            'displayorder' => ''
        );
    }

    /**
     * 清除过期缓存
     *
     * @param	intval	time	秒数
     * @return	array
     */
    public function clear($time) {

        if (!$time) {
            return;
        }

        $this->db->where('inputtime<', SYS_TIME - $time)->delete($this->tablename);
        $this->db->where('inputtime<', SYS_TIME - $time)->delete($this->indexname);
    }

    /**
     * 搜索缓存数据
     *
     * @param	intval	$id
     * @param	intval	$page
     * @return	array
     */
    public function get($id, $page = 1) {

        $data = $this->db->where('id', $id)->get($this->tablename)->row_array();
        if (!$data) {
            return array();
        }

        $data['params'] = dr_string2array($data['params']);

        return $data;
    }

    /**
     * 查询数据并设置缓存
     *
     * @param	array	$get
     * @return	array
     */
    public function set($get) {

        // 查询表名称
        $table = $this->db->dbprefix(SITE_ID.'_'.$this->dir);
        $table_more = $this->db->dbprefix(SITE_ID.'_'.$this->dir.'_category_data');

        // 条件数组
        ksort($get);
        $param = $get;
        
        // 存在分站时
        SITE_FID && $param['fid'] = isset($param['fid']) && $param['fid'] ? $param['fid'] : SITE_FID;
        
        $order = $get['order'];
        $keyword = dr_safe_replace($get['keyword']);
        if (strlen($keyword) > 150) {
            return;
        }
        unset($get['order'], $get['keyword']);
        $module = $this->ci->get_cache('module-'.SITE_ID.'-'.$this->dir);

        // 主表的字段
        $from = '`'.$table.'`';
        $mod_field = $module['field'] ? $module['field'] + $this->sys_field : $this->sys_field;

        // 搜索关键字条件
        $where = array();
		$where[] = '`'.$table.'`.`status` = 9';
        if ($keyword != '') {
            $sfield = explode(',', $module['setting']['search']['field'] ? $module['setting']['search']['field'] : 'title,keywords');
            if (!$sfield) {
                return;
            }
            $temp = array();
            foreach ($sfield as $t) {
                $t && $temp[] = '`'.$table.'`.`'.$t.'` LIKE "%'.$this->db->escape_str($keyword).'%"';
            }
            $where[] = '('.implode(' OR ', $temp).')';
        }

        // 排序条件
        $_order = $order ? explode(',', $order) : array('updatetime');
        $order_by = $_order_by = array();
        foreach ($_order as $i => $t) {
            $a = explode('_', $t);
            $b = end($a);
            if (in_array(strtolower($b), array('desc', 'asc'))) {
                $a = str_replace('_'.$b, '', $t);
            } else {
                $a = $t;
                $b = 'DESC';
            }
            isset($mod_field[$a]) && $_order_by[$a] = $b ? $b : "DESC";
        }
        !$_order_by && $_order_by['updatetime'] = 'DESC';
        unset($_order);

        // 字段过滤
        foreach ($mod_field as $name => $field) {
            if (isset($field['ismain']) && !$field['ismain']) {
                continue;
            }
            isset($get[$name]) && $get[$name] && $name != 'catid' && $where[] = $this->_where($table, $name, $get[$name], $field);
            // 地图坐标排序，这里不用它，默认id
            isset($_order_by[$name]) && $order_by[] = isset($field['fieldtype']) && $field['fieldtype'] == 'Baidumap' ? '`id` desc ' : '`'.$table.'`.`'.$name.'` '.$_order_by[$name];
        }

        // 栏目的字段
        if ($get['catid']) {
            $more = FALSE;
            $cat_field = $module['category'][$get['catid']]['field'];
            $where[0] = '`'.$table.'`.`catid`'.($module['category'][$get['catid']]['child'] ? 'IN ('.$module['category'][$get['catid']]['childids'].')' : '='.(int)$get['catid']);
            if ($cat_field) {
                foreach ($cat_field as $name => $field) {
                    if (isset($get[$name]) && $get[$name]) {
                        $more = TRUE;
                        $where[] = $this->_where($table_more, $name, $get[$name], $cat_field);
                    }
                    if (isset($_order_by[$name])) {
                        $more = TRUE;
                        $order_by[] = '`'.$table.'`.`'.$name.'` '.$_order_by[$name];
                    }
                }
            }
            $more && $from.= ' LEFT JOIN `'.$table_more.'` ON `'.$table.'`.`id`=`'.$table_more.'`.`id`';
        }
        // 筛选空值
        foreach ($where as $i => $t) {
            if (!$t) {
                unset($where[$i]);
            }
        }
        $where = $where ? 'WHERE '.implode(' AND ', $where) : '';
		
		// 商城模块的属性组合查询
		if ($this->dir == 'mall' && function_exists('dr_mall_property') && $get) {
			$pwhere = array();
			$property = dr_mall_property($get['catid']);
			if ($property) {
				foreach ($get as $i => $v) {
					if (strpos($i, 'mall_') === 0) {
						$n = substr($i, 5);
                        isset($property[$n]) && $property[$n] && strlen($v) && $pwhere[] = '(pid='.intval($property[$n]['id']).' and value = "'.dr_safe_replace($v).'")';
					}
				}
			}
            // 组合查询
            $pwhere && $where.= ' AND `id` IN (select `cid` from '.$this->db->dbprefix(SITE_ID.'_'.$this->dir.'_property_search').' where '.implode(' and ', $pwhere).')';
		}

        // 搜索商品有效期
        isset($mod_field['order_etime']) && $where.= isset($get['oot']) && $get['oot'] ? ' AND `order_etime` BETWEEN 1 AND '.SYS_TIME : ' AND NOT (`order_etime` BETWEEN 1 AND '.SYS_TIME.')';
         
        // 最大数据量
        $limit = (int)$module['setting']['search']['total'] ? ' LIMIT '.(int)$module['setting']['search']['total'] : '';

        // 组合sql查询结果
        $sql = "SELECT `{$table}`.`id` FROM {$from} {$where} ORDER BY ".implode(',', $order_by).$limit;
        $id = md5(str_replace(array($this->dir, $limit), array('{dirname}', ''), $sql));
        // 查询是否存在缓存
        $data = $this->get($id);
        if ($data) {
            $data['search_sql'] = $sql;
            return $data;
        }

        // 重新生成缓存文件
        $data = $this->db->query($sql)->result_array();
        $contentid = array();
        $get['order'] = $order;
        $get['keyword'] = $keyword;

        if ($data) {
            // 入库索引表
            foreach ($data as $t) {
                $this->db->insert($this->indexname, array(
                    'id' => $id,
                    'cid' => $t['id'],
                    'inputtime' => SYS_TIME
                ));
                $contentid[] = $t['id'];
            }
            // 缓存入库
            $this->db->replace($this->tablename, array(
                'id' => $id,
                'catid' => intval($get['catid']),
                'params' => dr_array2string($param),
                'keyword' => $keyword,
                'contentid' => implode(',', $contentid),
                'inputtime' => SYS_TIME
            ));
        } else {
            $id = 0;
        }

        return array(
            'id' => $id,
            'page' => 1,
            'params' => $param,
            'catid' => intval($get['catid']),
            'keyword' => $keyword,
            'contentid' => $contentid ? implode(',', $contentid) : '',
            'search_sql' => $sql,
        );
    }

    // 条件组合
    private function _where($table, $name, $value, $field) {
        if (strpos($value, '%') === 0
            && strrchr($value, '%') === '%') {
            // like 条件
            return '`'.$table.'`.`'.$name.'` LIKE "'.$this->db->escape_str($value).'"';
        } elseif (preg_match('/[0-9]+,[0-9]+/', $value)) {
            // BETWEEN 条件
            list($s, $e) = explode(',', $value);
            return '`'.$table.'`.`'.$name.'` BETWEEN '.(int)$s.' AND '.intval($e ? $e : SYS_TIME);
        } elseif (isset($field['fieldtype']) && $field['fieldtype'] == 'Baidumap') {
            // 百度地图
            if ($this->my_position) {
                // 获取Nkm内的数据
                $lat = '`'.$table.'`.`'.$name.'_lat`';
                $lng = '`'.$table.'`.`'.$name.'_lng`';
                $squares = dr_square_point($this->my_position['lng'], $this->my_position['lat'], $value);
                return "({$lat} between {$squares['right-bottom']['lat']} and {$squares['left-top']['lat']}) and ({$lng} between {$squares['left-top']['lng']} and {$squares['right-bottom']['lng']})";
            } else {
                $this->msg('没有定位到您的坐标');
            }
        } elseif (isset($field['fieldtype']) && $field['fieldtype'] == 'Linkage') {
            // 联动菜单
            $data = dr_linkage($field['setting']['option']['linkage'], $value);
            if ($data) {
                if ($data['child']) {
                    return '`'.$table.'`.`'.$name.'` IN ('.$data['childids'].')';
                } else {
                    return '`'.$table.'`.`'.$name.'`='.intval($data['ii']);
                }
            }
        } elseif (is_numeric($value)) {
            return '`'.$table.'`.`'.$name.'`='.$value;
        } else {
            return '`'.$table.'`.`'.$name.'`="'.$this->db->escape_str($value).'"';
        }
    }

}