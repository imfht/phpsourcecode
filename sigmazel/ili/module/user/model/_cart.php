<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

use product\model\_product;

/**
 * 购物车
 * @author sigmazel
 * @since v1.0.2
 */
class _cart{
	public function insert($cart){
		global $db;
		
		$db->insert('tbl_cart', $cart);
	}
	
	public function get_by_id($cartid){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_cart WHERE CARTID = '{$cartid}' LIMIT 0, 1");
	}
	
	public function get_by_auth($auth, $productid, $matchid){
		global $db, $_var;
		
		$wheresql = $_var['current']['USERID'] ? "USERID = '{$_var[current][USERID]}' " : "AUTH = '{$auth}' ";
		
		return $db->fetch_first("SELECT * FROM tbl_cart WHERE {$wheresql} AND PRODUCTID = '{$productid}' AND PRODUCT_MATCHID = '{$matchid}' LIMIT 0, 1");
	}
	
	public function get_count_by_auth($auth, $wheresql = ''){
		global $db, $_var;
		
		$wheresql .= $_var['current']['USERID'] ? " AND a.USERID = '{$_var[current][USERID]}' " : " AND a.AUTH = '{$auth}' ";
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_cart a, tbl_product b WHERE a.PRODUCTID = b.PRODUCTID AND b.ISAUDIT = 1 AND b.SELLING = 1 {$wheresql}") + 0;
	}
	
	public function get_list_by_auth($auth, $wheresql = ''){
		global $db, $setting, $_var;
		
		$_product = new _product();
		
		$wheresql .= $_var['current']['USERID'] ? " AND a.USERID = '{$_var[current][USERID]}' " : " AND a.AUTH = '{$auth}' ";
		
		$matchids = array();
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.NO, b.TITLE, b.NO, b.PRICE, b.OURPRICE, b.SCORE, b.NUM AS SRCNUM, b.SALEDNUM, b.FILE01, b.BOOKING, b.LIMITNUM, b.REBATE FROM tbl_cart a, tbl_product b WHERE a.PRODUCTID = b.PRODUCTID AND b.ISAUDIT = 1 AND b.SELLING = 1 {$wheresql} ORDER BY a.CREATETIME ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['_SCORE'] = $row['SCORE'];
			$row['SCORE'] = $row['SCORE'] >= 0 ? $row['SCORE'] : 0;
			
			$row = $_product->format_price($row);
			$row = format_row_files($row);
			
			$row['MATCH'] = $row['ABOUTTYPE'] == 'full_send' ? '0' : ($row['MATCH'] == 0 ? '' : $row['MATCH']);
			$row['PRICE'] = $row['ABOUTTYPE'] == 'full_send' ? '0' : $row['PRICE'];
			$row['OURPRICE'] = $row['ABOUTTYPE'] == 'full_send' ? '0' : $row['OURPRICE'];
			
			if($row['PRODUCT_MATCHID']) $matchids[] = $row['PRODUCT_MATCHID'];
			
			$rows[] = $row;
		}
		
		if(count($matchids) == 0) return $rows;
		
		$product_matchs = array();
		$temp_query = $db->query("SELECT * FROM tbl_product_match WHERE PRODUCT_MATCHID IN(".eimplode($matchids).")");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$product_matchs[$row['PRODUCT_MATCHID']] = $row;
		}
		
		foreach($rows as $key => $item){
			$tempmatch = $product_matchs[$item['PRODUCT_MATCHID']];
			if($tempmatch){
				$tempmatch['SCORE'] = $tempmatch['SCORE'] >= 0 ? $tempmatch['SCORE'] : 0;
				
				$rows[$key]['TITLE'] = $rows[$key]['TITLE'].' ['.$tempmatch['REMARK'].']';
				
				if($item['ABOUTTYPE'] == 'full_send') $rows[$key]['OURPRICE'] = 0;
				else $rows[$key]['OURPRICE'] = format_price($tempmatch['PRICE']);
				
				$rows[$key]['PRODUCT_MATCHID'] = $tempmatch['PRODUCT_MATCHID'];
				
				$rows[$key]['SCORE'] = $tempmatch['SCORE'];
				$rows[$key]['SRCNUM'] = $tempmatch['NUM'];
				$rows[$key]['SALEDNUM'] = $tempmatch['SALEDNUM'];
				$rows[$key]['WEIGHT'] = $item['PRICE'] == 0 ? 0 : $tempmatch['WEIGHT'];
			}
			
			unset($tempmatch);
		}
		
		return $rows;
	}
	
	public function get_list_by_query($query){
		global $db;
		
		$_product = new _product();
		
		$carts = array();
		$productids = array();
		$matchids = array();
		$product_array = array();
		$rows = array();
		
		$tmparr = explode(',', $query);
		foreach ($tmparr as $key => $tmp){
			if(!$tmp) continue;
			
			$arr1 = explode('|', $tmp);
			$arr2 = explode('_', $arr1[0]);
			$arr3 = count($arr2) > 2 ? explode('-', substr($arr1[0], strlen($arr2[0].'_'.$arr2[1].'_'))) : array();
			
			$carts[] = array(
			'PRODUCTID' => $arr2[0] + 0, 
			'PRODUCT_MATCHID' => $arr2[1] + 0, 
			'NUM' => $arr1[1] + 0,
			'ABOUTTYPE' => $arr3[0],  
			'ABOUTID' => $arr3[1]
			);
			
			$productids[] = $arr2[0] + 0;
			$matchids[] = $arr2[1] + 0;
			
			unset($arr1);
			unset($arr2);
			unset($arr3);
		}
		
		if(count($productids) > 0){
			$temp_query = $db->query("SELECT a.PRODUCTID, a.NO, a.TITLE, a.PRICE, a.OURPRICE, a.SCORE, a.NUM AS SRCNUM, a.SALEDNUM, a.FILE01, a.BOOKING, a.LIMITNUM, a.REBATE, b.IDENTITY AS MOLD FROM tbl_product a LEFT JOIN tbl_mold b ON a.MOLDID = b.MOLDID WHERE a.ISAUDIT = 1 AND a.SELLING = 1 AND a.PRODUCTID IN(".eimplode($productids).")");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$row['ORDERED'] = 1;
				$row['NUM'] = 0;
				$row['PRODUCT_MATCHID'] = 0;
				$row['DISCOUNTPRICE'] = format_price($row['OURPRICE']);
				$row['_SCORE'] = $row['SCORE'];
				$row['SCORE'] = $row['SCORE'] >= 0 ? $row['SCORE'] : 0;
				
				$row = $_product->format_price($row);
				$row = format_row_files($row);
				
				$product_array[] = $row;
			}
		}
		
		foreach ($carts as $ckey => $cart){
			foreach($product_array as $key => $item){
				if($item['PRODUCTID'] == $cart['PRODUCTID']){
					$item['PRODUCT_MATCHID'] = $cart['PRODUCT_MATCHID'];
					$item['NUM'] = $cart['NUM'] + 0;
					$item['ABOUTTYPE'] = $cart['ABOUTTYPE'];
					$item['ABOUTID'] = $cart['ABOUTID'];
					
					$rows[] = $item;
					break;
				}
			}
		}
		
		if(count($matchids) > 0){
			$product_matchs = array();
			$temp_query = $db->query("SELECT * FROM tbl_product_match WHERE PRODUCT_MATCHID IN(".eimplode($matchids).")");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$product_matchs[$row['PRODUCT_MATCHID']] = $row;
			}
			
			foreach($rows as $key => $item){
				$tempmatch = $product_matchs[$item['PRODUCT_MATCHID']];
				if($tempmatch){
					$tempmatch['SCORE'] = $tempmatch['SCORE'] >= 0 ? $tempmatch['SCORE'] : 0;
					
					$rows[$key]['TITLE'] = $rows[$key]['TITLE'].' ['.$tempmatch['REMARK'].']';
					$rows[$key]['OURPRICE'] = format_price($tempmatch['PRICE']);
					$rows[$key]['DISCOUNTPRICE'] = format_price($tempmatch['PRICE']);
					$rows[$key]['PRODUCT_MATCHID'] = $tempmatch['PRODUCT_MATCHID'];
					$rows[$key]['SCORE'] = $tempmatch['SCORE'];
					$rows[$key]['SRCNUM'] = $tempmatch['NUM'];
					$rows[$key]['SALEDNUM'] = $tempmatch['SALEDNUM'];
					$rows[$key]['WEIGHT'] = $item['PRICE'] == 0 ? 0 : $tempmatch['WEIGHT'];
				}
				
				unset($tempmatch);
			}
		}
		
		return $rows;
	}
	
	public function get_num_by_auth($auth){
		global $db, $_var;
		
		$wheresql = $_var['current']['USERID'] ? " AND a.USERID = '{$_var[current][USERID]}' " : " AND a.AUTH = '{$auth}' ";
		
		return $db->result_first("SELECT SUM(a.NUM) FROM tbl_cart a, tbl_product b WHERE a.PRODUCTID = b.PRODUCTID AND b.ISAUDIT = 1 AND b.SELLING = 1 {$wheresql} AND a.ABOUTTYPE <> 'full_send'") + 0;
	}
	
	public function get_discounts($carts){
		global $db, $_var;
		
		$temparr = array();
		$productids = array();
		
		foreach($carts as $key => $cart){
			$carts[$key]['DISCOUNTDESC'] = $GLOBALS['lang']['product.discount.no'];
			$carts[$key]['DISCOUNTPRICE'] = is_cnumber($cart['MATCH']) ? $cart['MATCH'] + 0.00 : $cart['OURPRICE'];
			$cart['ABOUTTYPE'] && $temparr[$cart['ABOUTTYPE']][] = $cart['ABOUTID'];
			
			$productids[] = $cart['PRODUCTID'];
		}
		
		if(count($temparr) == 0) return $carts;
		
		$discount = array();
		$nowdate = date('Y-m-d H:i:s');
		
		foreach($temparr as $key => $val){
			if($key == 'time_buy'){
				$temp_query = $db->query("SELECT * FROM tbl_time_buy WHERE STATUS = 1 AND (BEGINDATE IS NULL OR BEGINDATE = '0000-00-00' OR BEGINDATE <= '{$nowdate}') AND (ENDDATE IS NULL OR ENDDATE = '0000-00-00' OR ENDDATE >= '{$nowdate}') AND TIME_BUYID IN(".eimplode($val).")");
				while(($row = $db->fetch_array($temp_query)) !== false){
					$row['ABOUTTYPE'] = 'time_buy';
					$row['ABOUTTITLE'] = $row['TITLE'];
					$row['ABOUTID'] = $row['TIME_BUYID'];
					$discount['time_buy_'.$row['TIME_BUYID']] = $row;
				}
			}elseif($key == 'full_send'){
				$temp_query = $db->query("SELECT * FROM tbl_full_send WHERE STATUS = 1 AND (BEGINDATE IS NULL OR BEGINDATE = '0000-00-00' OR BEGINDATE <= '{$nowdate}') AND (ENDDATE IS NULL OR ENDDATE = '0000-00-00' OR ENDDATE >= '{$nowdate}') AND FULL_SENDID IN(".eimplode($val).")");
				while(($row = $db->fetch_array($temp_query)) !== false){
					$row['ABOUTTYPE'] = 'full_send';
					$row['ABOUTTITLE'] = $row['TITLE'];
					$row['ABOUTID'] = $row['FULL_SENDID'];
					$discount['full_send_'.$row['FULL_SENDID']] = $row;
				}
			}elseif($key == 'chain'){
				$temp_query = $db->query("SELECT * FROM tbl_product_chain WHERE PRODUCT_CHAINID IN(".eimplode($val).")");
				while(($row = $db->fetch_array($temp_query)) !== false){
					$row['ABOUTTYPE'] = 'chain';
					$row['ABOUTTITLE'] = $GLOBALS['lang']['product.chain'];
					$row['ABOUTID'] = $row['PRODUCTID'];
					$discount['chain_'.$row['PRODUCT_CHAINID']] = $row;
				}
			}
			
			unset($temp_query);
		}
		
		foreach($carts as $key => $cart){
			if($discount[$cart['ABOUTTYPE'].'_'.$cart['ABOUTID']]){
				$tempdc = $discount[$cart['ABOUTTYPE'].'_'.$cart['ABOUTID']];
				$carts[$key]['DISCOUNT'] = $tempdc;
				
				if($cart['ABOUTTYPE'] == 'time_buy'){
					$carts[$key]['DISCOUNTDESC'] = $tempdc['TITLE'].' '.format_discount($tempdc['DISCOUNT']).$GLOBALS['lang']['product.discount'];
					$carts[$key]['PRICE'] = $carts[$key]['DISCOUNTPRICE'] = format_price(round($carts[$key]['OURPRICE'] * $tempdc['DISCOUNT'] * 0.01, 2));
				}elseif($cart['ABOUTTYPE'] == 'full_send'){
					$carts[$key]['DISCOUNTDESC'] = $tempdc['TITLE'];
				}elseif($cart['ABOUTTYPE'] == 'chain'){
					$carts[$key]['DISCOUNTDESC'] = $tempdc['ABOUTTITLE'];
				}
			}
			
			unset($tempdc);
		}
		
		$price_sum = 0;
		foreach($carts as $key => $cart) $price_sum += $cart['ABOUTTYPE'] == 'full_send' ? 0 : $cart['DISCOUNTPRICE'] * $cart['NUM'];
		
		$nowtimer = time();
		foreach($carts as $key => $cart){
			if($discount[$cart['ABOUTTYPE'].'_'.$cart['ABOUTID']]){
				$tempdc = $discount[$cart['ABOUTTYPE'].'_'.$cart['ABOUTID']];
				$tempvalid = true;
				
				if($cart['ABOUTTYPE'] == 'full_send'){
					$begintimer = strtotime($tempdc['BEGINDATE']);
					$entimer = strtotime($tempdc['ENDDATE']);
					
					if($tempdc['STATUS'] != 1) $tempvalid = false; //未开启
					elseif($price_sum < $tempdc['ORDERPRICE']) $tempvalid = false; //未达到订单金额
					elseif($tempdc['BEGINDATE'] + 0 > 0 && $nowtimer < $begintimer) $tempvalid = false; //未到开始时间
					elseif($tempdc['ENDDATE'] + 0 > 0 && $entimer < $nowtimer) $tempvalid = false; //已结束
					
					if(!$tempvalid){
						$this->delete_by_id($_var['auth'], $cart['CARTID']);
						unset($carts[$key]);
					}
				}elseif($cart['ABOUTTYPE'] == 'chain'){
					if(!in_array($tempdc['ABOUTID'], $productids)){
						//如果主商品不存在，商品搭配优惠删除
						$carts[$key]['DISCOUNTDESC'] = '';
						$carts[$key]['DISCOUNTPRICE'] = $cart['OURPRICE'];
						$carts[$key]['MATCH'] = '';
						
						unset($carts[$key]['DISCOUNT']);
						unset($carts[$key]['DISCOUNTDESC']);
					}
				}
			}
			
			unset($tempdc);
			unset($tempvalid);
			unset($begintimer);
			unset($entimer);
		}
		
		return $carts;
	}
	
	public function get_full_send($auth){
		global $db, $_var;
		
		$wheresql = $_var['current']['USERID'] ? "AND b.USERID = '{$_var[current][USERID]}' " : "AND b.AUTH = '{$auth}' ";
		
		return $db->fetch_first("SELECT a.* FROM tbl_full_send a, tbl_cart b WHERE a.FULL_SENDID = b.ABOUTID AND b.ABOUTTYPE = 'full_send' {$wheresql} LIMIT 0, 1");
	}
	
	public function delete_by_id($auth, $cartid){
		global $db, $_var;
		
		$wheresql = $_var['current']['USERID'] ? "USERID = '{$_var[current][USERID]}' " : "AUTH = '{$auth}' ";
		
		$db->delete('tbl_cart', "{$wheresql} AND CARTID = '{$cartid}'");
	}
	
	public function delete_by_ids($auth, $ids){
		global $db;
		
		$db->delete('tbl_cart', "CARTID IN(".eimplode($ids).")");
	}
	
	public function delete_by_auth($auth, $wheresql = ''){
		global $db, $_var;
		
		$prevsql = $_var['current']['USERID'] ? "USERID = '{$_var[current][USERID]}' " : "AUTH = '{$auth}' ";
		
		$db->delete('tbl_cart', "{$prevsql} {$wheresql}");
	}
	
	public function delete_full_send($auth){
		global $db, $_var;
		
		$wheresql = $_var['current']['USERID'] ? "USERID = '{$_var[current][USERID]}' " : "AUTH = '{$auth}' ";
		
		$db->delete('tbl_cart', "{$wheresql} AND ABOUTTYPE = 'full_send'");
	}
	
	public function update($cartid, $data){
		global $db;
		
		$db->update('tbl_cart', $data, " CARTID = '{$cartid}'");
	}
	
	public function update_num($cartid, $num){
		global $db;
		
		$db->update('tbl_cart', array('NUM' => $num), " CARTID = '{$cartid}'");
	}
	
	public function update_all_orered($auth, $ordered){
		global $db, $_var;
		
		$wheresql = $_var['current']['USERID'] ? "USERID = '{$_var[current][USERID]}' " : "AUTH = '{$auth}' ";
		
		$db->update('tbl_cart', array('ORDERED' => $ordered), "{$wheresql} AND ABOUTTYPE <> 'full_send'");
	}
	
	public function update_all_userid($auth, $userid){
		global $db;
		
		$db->update('tbl_cart', array('USERID' => $userid), "AUTH = '{$auth}'");
	}
	
}
?>