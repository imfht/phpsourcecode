<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

use product\model\_product;
use cms\model\_article;
use bbs\model\_forum_topic;

/**
 * 收藏
 * @author sigmazel
 * @since v1.0.2
 */
class _favorite{
	//关注数
	public function user_list($start, $perpage, $where){
		global $db;
		
		$_user = new _user();
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.*, u.USERNAME AS NAME, u.USERID AS NAMEID, u.PHOTO, u.COMMENT FROM tbl_favorite a, tbl_user u WHERE a.ABOUTTYPE = 'user' {$where} ORDER BY a.CREATETIME DESC LIMIT $start, $perpage");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = $_user->format_photo($row);
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	//粉丝数
	public function fans_list($start, $perpage, $where){
		global $db;
	
		$rows = array();
	
		$temp_query = $db->query("SELECT a.*, u.PHOTO, u.REMARK FROM tbl_favorite a, tbl_user u WHERE a.ABOUTTYPE = 'user' AND a.USERID = b.USERID {$where} ORDER BY a.CREATETIME DESC LIMIT $start, $perpage");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
	
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	public function add($favorite){
		global $db;
		
		$db->insert('tbl_favorite', $favorite);
	}
	
	public function get($userid, $aboutid, $abouttype = ''){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_favorite WHERE USERID = '{$userid}' AND ABOUTID = '{$aboutid}' AND ABOUTTYPE = '{$abouttype}' LIMIT 0, 1");
	}
	
	public function article_by_id($id){
		global $db;
	
		return $db->fetch_first("SELECT  a.*, b.TITLE, b.SUBTITLE, b.AUTHOR, b.LINK, b.SUMMARY, b.ADDRESS, b.ISTOP, b.HITS, b.ISCOMMEND, b.FILE01, c.CNAME FROM tbl_favorite a, tbl_article b, tbl_category c WHERE a.ABOUTTYPE = 'article' AND a.ABOUTID = b.ARTICLEID AND b.CATEGORYID = c.CATEGORYID AND a.FAVORITEID = '{$id}' LIMIT 0, 1");
	}
	
	public function get_count($where){
		global $db;
		return $db->result_first("SELECT COUNT(1) FROM tbl_favorite a WHERE 1 {$where}") + 0;
	}
	
	public function get_list($start, $perpage, $wheresql, $ordersql){
		global $db;
	
		$_article = new _article();
		$_forum_topic = new _forum_topic();
		
		$rows = array();
		
		!$ordersql && $ordersql = 'ORDER BY a.EDITTIME DESC';
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_favorite a WHERE (a.ABOUTTYPE = 'article' OR a.ABOUTTYPE = 'post') {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			if($row['ABOUTTYPE'] == 'article'){
				$article = $_article->get_by_id($row['ABOUTID']);
				if($article){
					$row['TITLE'] = $article['TITLE'];
					$row['SUMMARY'] = $article['SUMMARY'];
					$row['CREATETIME'] = $article['EDITTIME'];
					$row['CNAME'] = $article['CNAME'];
					$row['IDENTITY'] = $article['IDENTITY'];
					$rows[] = $row;
				} else {
					$db->delete('tbl_favorite', " ABOUTID = '{$row[ABOUTID]}' AND ABOUTTYPE = 'article' ");
				}
				
			} elseif($row['ABOUTTYPE'] == 'post'){
				$post = $_forum_topic->get_by_id($row['ABOUTID']);
				if($post){
					$row['FORUMID'] = $post['FORUMID'];
					$row['FORUM_TOPICID'] = $post['FORUM_TOPICID'];
					$row['TITLE'] = $post['TITLE'];
					$row['SUMMARY'] = $post['SUMMARY'];
					$row['CREATETIME'] = $post['EDITTIME'];
					$row['CNAME'] = $post['FORUMNAME'];
					$rows[] = $row;
				} else {
					$db->delete('tbl_favorite', " ABOUTID = '{$row[ABOUTID]}' AND ABOUTTYPE = 'post' ");
				}
			}
			
			
		}
	
		return $rows;
	}
	
	public function article_count($where){
		global $db;
	
		return $db->result_first("SELECT COUNT(1) FROM tbl_favorite a, tbl_article b, tbl_category c WHERE a.ABOUTTYPE = 'article' AND a.ABOUTID = b.ARTICLEID AND b.CATEGORYID = c.CATEGORYID {$where}") + 0;
	}
	
	public function article_list($start, $perpage, $where){
		global $db;
	
		$rows = array();
	
		$temp_query = $db->query("SELECT a.*, b.TITLE, b.SUBTITLE, b.AUTHOR, b.LINK, b.SUMMARY, b.ADDRESS, b.ISTOP, b.HITS, b.ISCOMMEND, b.FILE01, c.CNAME FROM tbl_favorite a, tbl_article b, tbl_category c WHERE a.ABOUTTYPE = 'article' AND a.ABOUTID = b.ARTICLEID AND b.CATEGORYID = c.CATEGORYID {$where} ORDER BY a.CREATETIME DESC LIMIT $start, $perpage");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
	
			$rows[] = $row;
		}
	
		return $rows;
	}
	
	public function product_by_id($id){
		global $db;
	
		return $db->fetch_first("SELECT  a.*, b.NO, b.TITLE, b.SUBTITLE, b.PRICE, b.OURPRICE, b.VIPPRICE, b.SCORE, b.NUM, b.SALEDNUM FROM tbl_favorite a, tbl_product b WHERE a.ABOUTTYPE = 'product' AND a.ABOUTID = b.PRODUCTID AND a.FAVORITEID = '{$id}' LIMIT 0, 1");
	}
	
	public function product_count($where){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_favorite a, tbl_product b WHERE a.ABOUTTYPE = 'product' AND a.ABOUTID = b.PRODUCTID {$where}") + 0;
	}
	
	public function product_list($start, $perpage, $where){
		global $db, $setting;
		
		$_product = new _product();
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.PRODUCTID, b.NO, b.TITLE, b.SUBTITLE, b.PRICE, b.OURPRICE, b.VIPPRICE, b.SCORE, b.NUM, b.SALEDNUM, b.BOOKING, b.FILE01 FROM tbl_favorite a, tbl_product b WHERE a.ABOUTTYPE = 'product' AND a.ABOUTID = b.PRODUCTID {$where} ORDER BY a.CREATETIME DESC LIMIT $start, $perpage");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = $_product->format_price($row);
			$row = format_row_files($row);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	public function delete_by_id($id){
		global $db;
		
		$db->delete('tbl_favorite', "FAVORITEID = '{$id}'");
	}
	
	public function delete($wheresql){
		global $db;
	
		$db->delete('tbl_favorite', "$wheresql");
	}

}
?>