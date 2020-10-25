<?php
namespace Lib\Tool;

//Jquery响应式DataTables表格所需的服务类
class DataTables{
	static function server ($request,$columns,$table)
	{
		$field = implode(",", self::pluck($columns, 'db'));

		$where = self::where($request,$columns );
		$order = self::order($request,$columns);
		$limit = self::limit($request);

		$data  = M($table)->field($field)->where($where)->order($order)->limit($limit)->select();
		$conut = M($table)->where($where)->count();

		$result = array();
		$result['draw'] = intval($request['draw']);
        $result['recordsTotal'] = intval($conut);
        $result['recordsFiltered'] = intval($conut);
		$result['data'] = self::dataOutput($columns,$data);
		return $result;
	}

	static function where ( $request, $columns)
	{
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = self::pluck( $columns, 'dt' );
		$binding='';

		if ( isset($request['search']) && $request['search']['value'] != '' ) {
			$str = $request['search']['value'];

			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ( $requestColumn['searchable'] == 'true' ) {
					//$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
					$binding = '%'.$str.'%';
					$globalSearch[] = "`".$column['db']."` LIKE '".$binding."'";
				}
			}
		}

		// Individual column filtering
		for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $request['columns'][$i];
			$columnIdx = array_search( $requestColumn['data'], $dtColumns );
			$column = $columns[ $columnIdx ];

			$str = $requestColumn['search']['value'];

			if ( $requestColumn['searchable'] == 'true' &&
			 $str != '' ) {
				//$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
				$binding = '%'.$str.'%';
				$columnSearch[] = "`".$column['db']."` LIKE '".$binding."'";
			}
		}

		// Combine the filters into a single string
		$where = '';

		if ( count( $globalSearch ) ) {
			//$where = '('.implode(' OR ', $globalSearch).')';
			$where = implode(' OR ', $globalSearch);
		}

		if ( count( $columnSearch ) ) {
			$where = $where === '' ?
				implode(' AND ', $columnSearch) :
				$where .' AND '. implode(' AND ', $columnSearch);
		}

		// if ( $where !== '' ) {
		// 	$where = 'WHERE '.$where;
		// }

		return $where;
	}

	static function limit ( $request )
	{
		$limit = '';

		if ( isset($request['start']) && $request['length'] != -1 ) {
			$limit = intval($request['start']) .",".intval($request['length']);
		}

		return $limit;
	}

	static function order ( $request, $columns )
	{
		$order = '';

		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
			$dtColumns = self::pluck( $columns, 'dt' );

			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
				// Convert the column index into the column data property
				$columnIdx = intval($request['order'][$i]['column']);
				$requestColumn = $request['columns'][$columnIdx];

				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];

				if ( $requestColumn['orderable'] == 'true' ) {
					$dir = $request['order'][$i]['dir'] === 'asc' ?
						'ASC' :
						'DESC';

					$orderBy[] = '`'.$column['db'].'` '.$dir;
				}
			}

			$order = implode(', ', $orderBy);
		}

		return $order;
	}

	static function dataOutput ( $columns, $data )
	{
		$out = array();

		for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
			$row = array();

			for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
				$column = $columns[$j];

				// Is there a formatter?
				if ( isset( $column['formatter'] ) ) {
					$row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
				}
				else {
					$row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
				}
			}

			$out[] = $row;
		}

		return $out;
	}

	static function pluck ( $a, $prop )
	{
		$out = array();

		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
			if(empty($a[$i][$prop])) continue;
			$out[] = $a[$i][$prop];
		}

		return array_unique($out);
	}

	// static function bind ( &$a, $val, $type )
	// {
	// 	$key = ':binding_'.count( $a );

	// 	$a[] = array(
	// 		'key' => $key,
	// 		'val' => $val,
	// 		'type' => $type
	// 	);

	// 	return $key;
	// }

	// static function _flatten ( $a, $join = ' AND ' )
	// {
	// 	if ( ! $a ) {
	// 		return '';
	// 	}
	// 	else if ( $a && is_array($a) ) {
	// 		return implode( $join, $a );
	// 	}
	// 	return $a;
	// }

}