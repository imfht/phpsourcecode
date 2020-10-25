<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

/**
 * 收货地址
 * @author Administrator
 *
 */
class _address{
	public function check_exists($address){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_address WHERE USERID = '{$address[USERID]}' AND PROVINCEID = '{$address[PROVINCEID]}' AND CITYID = '{$address[CITYID]}' AND COUNTYID = '{$address[COUNTYID]}' AND MOBILE = '{$address[MOBILE]}' LIMIT 0, 1");
	}
	
	public function get_by_id($addressid){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_address WHERE ADDRESSID = '{$addressid}'");
	}
	
	public function get_count($userid){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_address WHERE USERID = '{$userid}'") + 0;
	}
	
	public function get_list($userid, $order = '', $province = 1){
		global $db;
		
		!$order && $order = 'ORDER BY a.ADDRESSID ASC';
		
		$rows = array();
		$district_ids = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_address a WHERE a.USERID = '{$userid}' {$order}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			if(!in_array($row['PROVINCEID'], $district_ids)) $district_ids[] = $row['PROVINCEID'];
			if(!in_array($row['CITYID'], $district_ids)) $district_ids[] = $row['CITYID'];
			if(!in_array($row['COUNTYID'], $district_ids)) $district_ids[] = $row['COUNTYID'];
			
			$rows[] = $row;
		}
		
		$district_list = array();
		$temp_query = $db->query("SELECT * FROM tbl_district WHERE DISTRICTID IN(".eimplode($district_ids).")");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$district_list[$row['DISTRICTID']] = $row;
		}
		
		foreach ($rows as $key => $address){
			$address['_PLACE'] = $address['PLACE'];
			
			if($district_list[$address['COUNTYID']]){
				$address['COUNTY'] = $district_list[$address['COUNTYID']];
				$address['PLACE'] = $district_list[$address['COUNTYID']]['CNAME'].'-'.$address['PLACE'];
			}
			
			if($district_list[$address['CITYID']]){
				$address['CITY'] = $district_list[$address['CITYID']];
				$address['PLACE'] = $district_list[$address['CITYID']]['CNAME'].'-'.$address['PLACE'];
			}
			
			if($district_list[$address['PROVINCEID']] && $province){
				$address['PROVINCE'] = $district_list[$address['PROVINCEID']];
				$address['PLACE'] = $district_list[$address['PROVINCEID']]['CNAME'].'-'.$address['PLACE'];
			}
			
			$rows[$key] = $address;
		}
	
		return $rows;
	}
	
	public function get_place($address){
		global $db;
		
		$district_ids = array();
		
		if($address['PROVINCEID']) $district_ids[] = $address['PROVINCEID'];
		if($address['CITYID']) $district_ids[] = $address['CITYID'];
		if($address['COUNTYID']) $district_ids[] = $address['COUNTYID'];
		
		$district_list = array();
		$temp_query = $db->query("SELECT * FROM tbl_district WHERE DISTRICTID IN(".eimplode($district_ids).")");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$district_list[$row['DISTRICTID']] = $row;
		}
		
		$address['_PLACE'] = $address['PLACE'];
		
		if($district_list[$address['COUNTYID']]){
			$address['COUNTY'] = $district_list[$address['COUNTYID']];
			$address['PLACE'] = $district_list[$address['COUNTYID']]['CNAME'].'-'.$address['PLACE'];
		}
		
		if($district_list[$address['CITYID']]){
			$address['CITY'] = $district_list[$address['CITYID']];
			$address['PLACE'] = $district_list[$address['CITYID']]['CNAME'].'-'.$address['PLACE'];
		}
		
		if($district_list[$address['PROVINCEID']]){
			$address['PROVINCE'] = $district_list[$address['PROVINCEID']];
			$address['PLACE'] = $district_list[$address['PROVINCEID']]['CNAME'].'-'.$address['PLACE'];
		}
		
		return $address;
	}

	//添加
	public function insert($address){
		global $db;
		
		$db->insert('tbl_address', $address);
		
		return $db->insert_id();
	}

	//修改
	public function update($addressid, $address){
		global $db;
		
		$db->update('tbl_address', $address, "ADDRESSID = '{$addressid}'");
	}

	//删除
	public function delete($addressid){
		global $db;
		
		$db->delete('tbl_address', "ADDRESSID = '{$addressid}'");
	}

	//批量删除
    public function delete_batch($wheresql){
        global $db;

        $db->delete('tbl_address', $wheresql);
    }

    //设置默认
	public function set_def($address){
		global $db;
		
		$db->update('tbl_address', array('DEF' => 0), " USERID = '{$address[USERID]}'");
		$db->update('tbl_address', array('DEF' => 1), " ADDRESSID = '{$address[ADDRESSID]}'");
	}

	//设置第一个收货地址为默认
	public function set_first_def($userid){
		global $db;
	
		$first = $db->fetch_first("SELECT * FROM tbl_address WHERE USERID = '{$userid}' ORDER BY DEF DESC, EDITTIME ASC");
		if($first) $db->update('tbl_address', array('DEF' => 1), "ADDRESSID = '{$first[ADDRESSID]}'");
	}
}
?>