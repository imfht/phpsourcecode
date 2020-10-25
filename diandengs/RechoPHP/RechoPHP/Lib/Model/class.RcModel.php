<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

/**
 * RcModel class
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2012-08-19 01:41
 * $last update time: 2012-08-19 01:41 Recho $
 */
class RcModel{
	
	public function T( $table){
		return T( $table);
	}
	
	public function getCache( $c){
		$c = self::getKeyName( $c);
		$list = cache( $c['type'])->get( $c['key'].$c['key_']);
		return $list;
	}
	
	public function setCache( $c){
		$c = self::getKeyName( $c);
		self::saveKey( $c);
		$t = cache( $c['type'])->set($c['key'].$c['key_'], $c['value'], $c['expire']);
		return $t;
	}
	
	public function delCache( $c){
		if( !isset($c['type'])){
			$cList = $c;$t=0;
			if( empty( $c)) return false;
			foreach( $cList as $c){
				$c = self::getKeyName( $c);
				$sql = "SELECT * FROM {$this->T('www_cache_key')} WHERE `key`='{$c['key']}'";
				$list = odb::dbslave()->getAll( $sql, MYSQL_ASSOC);
				if( !empty( $list)){
					foreach( $list as $key=>$value){
						$t = cache($value['type'])->delete($value['key'].$value['key_']);
					}
					$sql = "DELETE FROM {$this->T('www_cache_key')} WHERE `key`='{$c['key']}'";
					odb::db()->query( $sql);
					$t += odb::db()->affectedRows();
				}
			}
			return $t;
		}else{
			if( empty( $c)) return false;
			$c = self::getKeyName( $c);
			$sql = "SELECT * FROM {$this->T('www_cache_key')} WHERE `key`='{$c['key']}'";
			$list = odb::dbslave()->getAll( $sql, MYSQL_ASSOC);
			if( !empty( $list)){
				foreach( $list as $key=>$value){
					cache($c['type'])->delete($value['key'].$value['key_']);
				}
				$sql = "DELETE FROM {$this->T('www_cache_key')} WHERE `key`='{$c['key']}'";
				odb::db()->query( $sql);
				return odb::db()->affectedRows();
			}
		}
		return false;
	}
	
	public function getAll( $sql, $pageCondition=false){
		if( is_array( $pageCondition)){
			$pageCondition['page'] = ($page=functions::uint( $pageCondition['page'])) ? $page:1;
			$pageCondition['pagesize'] = ($pagesize=functions::uint( $pageCondition['pagesize'])) ? $pagesize:20;
			$resource = odb::db()->getAll( $sql);
			$rows = count( $resource);
			$pageInfo = functions::pageInfo( $rows, $pageCondition['pagesize'], $pageCondition['page']);
		}
		$startRow = ($pageCondition['page']-1)*$pageCondition['pagesize'];
		$sql = is_array( $pageCondition) ? $sql." LIMIT $startRow, {$pageCondition['pagesize']}":$sql;
		$list = odb::db()->getAll( $sql, MYSQL_ASSOC);
		if( is_array( $pageCondition)){
			$response = array('list'=>$list, 'pageInfo'=>$pageInfo);
			return $response;
		}
		return $list;
	}
	
	private function saveKey( $c){
		$c = self::getKeyName( $c);
		$sql = "INSERT INTO {$this->T('www_cache_key')} SET `type`='{$c['type']}', `key`='{$c['key']}', `key_`='{$c['key_']}', `expire`='{$c['expire']}' ON DUPLICATE KEY UPDATE `type`='{$c['type']}', `expire`='{$c['expire']}'";
		odb::db()->query( $sql);
	}
	
	private function getKeyName( $c){
		$c['key'] = $c['key'].'';
		return $c;
	}
}