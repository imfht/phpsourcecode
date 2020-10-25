<?php
class FileTxt {

	var $file;
	var $index;
	public
	function set_file( $file ) {
		$this->file = $file;
	}
	//建立一个文件并写入输入    
	function null_write( $new ) {
		$f = fopen( $this->file, "w" );
		flock( $f, LOCK_EX );
		fputs( $f, $new );
		fclose( $f );
	}
	//  添加数据记录到文件末端    
	function add_write( $new ) {
		$f = fopen( $this->file, "a" );
		flock( $f, LOCK_EX );
		fputs( $f, $new );
		fclose( $f );
	}
	//  配合readfile()的返回一起使用,把一行数据转换为一维数组    
	function make_array( $line ) {
		$array = explode( "\x0E", $line );
		return $array;
	}

	//把为一维数组转换一行数据    
	function join_array( $line ) {
		$array = join( "\x0E", $line );
		return $array;
	}
	//  返回数据文件的总行数    
	function getlines() {
		$f = file( $this->file );
		return count( $f );
	}
	//  返回下一行的数据记录(备用)    
	function next_line() {
		$this->index = $this->index++;
		return $this->get();
	}

	//  返回上一行的数据记录(备用)    
	function prev_line() {
		$this->index = $this->index--;
		return $this->get();
	}
	//  返回当前行的数据记录数据较小    
	function get() {
		$f = fopen( $this->file, "r" );
		flock( $f, LOCK_SH );
		for ( $i = 0; $i <= $this->index; $i++ ) {
			$rec = fgets( $f, 1024 );
		}
		$line = explode( "\x0E", $rec );
		fclose( $f );
		return $line;
	}
	//  返回当前行的数据记录数据较大    
	function get_big_file() {
		$f = fopen( $this->file, "r" );
		flock( $f, LOCK_SH );
		for ( $i = 0; $i <= $this->index; $i++ ) {
			$rec = fgets( $f, 1024 * 5 );
		}
		$line = explode( "\x0E", $rec );
		fclose( $f );
		return $line;
	}
	//  打开数据文件---以一维数组返回文件内容    
	function read_file() {
		if ( file_exists( $this->file ) ) {
			$line = file( $this->file );
		}
		return $line;
	}
	//  打开数据文件---以二维数组返回文件内容    
	function openFile() {
		if ( file_exists( $this->file ) ) {
			$f = file( $this->file );
			$lines = array();
			foreach ( $f as $rawline ) {
				$tmpline = explode( "\x0E", $rawline );
				array_push( $lines, $tmpline );
			}
		}
		return $lines;
	}
	//  传入一个数组,合并成一行数据,重写整个文件    
	function overwrite( $array ) {
		$newline = implode( "\x0E", $array );
		$f = fopen( $this->file, "w" );
		flock( $f, LOCK_EX );
		fputs( $f, $newline );
		fclose( $f );
	}

	//  添加一行数据记录到文件末端    
	function add_line( $array, $check_n = 1 ) {
		$s = implode( "\x0E", $array );
		$f = fopen( $this->file, "a" );
		flock( $f, LOCK_EX );
		fputs( $f, $s );
		if ( $check_n == 1 )fputs( $f, "\n" );
		fclose( $f );
	}

	//  插入一行数据记录到文件最前面    
	function insert_line( $array ) {
		$newfile = implode( "\x0E", $array );
		$f = fopen( $this->file, "r" );
		flock( $f, LOCK_SH );
		while ( $line = fgets( $f, 1024 ) ) {
			$newfile .= $line;
		}
		fclose( $f );
		$f = fopen( $this->file, "w" );
		flock( $f, LOCK_EX );
		fputs( $f, $newfile );
		fclose( $f );
	}

	//  更新所有符合条件的数据记录,适用于每行字节数据较大的情况    
	function update( $column, $query_string, $update_array ) {
		$update_string = implode( "\x0E", $update_array );
		$newfile = "";
		$fc = file( $this->file );
		$f = fopen( $this->file, "r" );
		flock( $f, LOCK_SH );
		for ( $i = 0; $i < count( $fc ); $i++ ) {
			$list = explode( "\x0E", $fc[ $i ] );
			if ( $list[ $column ] != $query_string ) {
				$newfile = $newfile . chop( $fc[ $i ] ) . "\n";
			} else {
				$newfile = $newfile . $update_string;
			}
		}
		fclose( $f );
		$f = fopen( $this->file, "w" );
		flock( $f, LOCK_EX );
		fputs( $f, $newfile );
		fclose( $f );
	}

	//  更新所有符合条件的数据记录,适用于每行字节数据较小的情况    
	function update2( $column, $query_string, $update_array ) {
		$newline = implode( "\x0E", $update_array );
		$newfile = "";
		$f = fopen( $this->file, "r" );
		flock( $f, LOCK_SH );
		while ( $line = fgets( $f, 1024 ) ) {
			$tmpLine = explode( "\x0E", $line );
			if ( $tmpLine[ $column ] == $query_string ) {
				$newfile .= $newline;
			} else {
				$newfile .= $line;
			}
		}
		fclose( $f );
		$f = fopen( $this->file, "w" );
		flock( $f, LOCK_EX );
		fputs( $f, $newfile );
		fclose( $f );
	}

	//  删除所有符合条件的数据记录,适用于每行字节数据较大的情况    
	function delete( $column, $query_string ) {
		$newfile = "";
		$fc = file( $this->file );
		$f = fopen( $this->file, "r" );
		flock( $f, LOCK_SH );
		for ( $i = 0; $i < count( $fc ); $i++ ) {
			$list = explode( "\x0E", $fc[ $i ] );
			if ( $list[ $column ] != $query_string ) {
				$newfile = $newfile . chop( $fc[ $i ] ) . "\n";
			}
		}
		fclose( $f );
		$f = fopen( $this->file, "w" );
		flock( $f, LOCK_EX );
		fputs( $f, $newfile );
		fclose( $f );
	}

	//  删除所有符合条件的数据记录,适用于每行字节数据较小的情况    
	function delete2( $column, $query_string ) {
		$newfile = "";
		$f = fopen( $this->file, "r" );
		flock( $f, LOCK_SH );
		while ( $line = fgets( $f, 1024 ) ) {
			$tmpLine = explode( "\x0E", $line );
			if ( $tmpLine[ $column ] != $query_string ) {
				$newfile .= $line;
			}
		}
		fclose( $f );
		$f = fopen( $this->file, "w" );
		flock( $f, LOCK_EX );
		fputs( $f, $newfile );
		fclose( $f );
	}

	//取得一个文件里某个字段的最大值    
	function get_max_value( $column ) {
		$tlines = file( $this->file );
		for ( $i = 0; $i <= count( $tlines ); $i++ ) {
			$line = explode( "\x0E", $tlines[ $i ] );
			$get_value[] = $line[ $column ];
		}
		$get_max_value = max( $get_value );
		return $get_max_value;
	}


	//  根据数据文件的某个字段是否包含$query_string进行查询,以二维数组返回所有符合条件的数据    
	function select( $column, $query_string ) {
		$tline = $this->openfile();
		$lines = array();
		foreach ( $tline as $line ) {
			if ( $line[ $column ] == $query_string ) {
				array_push( $lines, $line );
			}
		}

		return $lines;
	}

	//  功能与function  select()一样,速度可能略有提升    
	function select2( $column, $query_string ) {
		if ( file_exists( $this->file ) ) {
			$tline = $this->read_file();
			foreach ( $tline as $tmpLine ) {
				$line = $this->make_array( $tmpLine );
				if ( $line[ $column ] == $query_string ) {
					$lines[] = $tmpLine;
				}
			}
		}

		return $lines;
	}

	//  根据数据文件的某个字段是否包含$query_string进行查询,以一维数组返回第一个符合条件的数据    
	function select_line( $column, $query_string ) {
		$tline = $this->read_file();
		foreach ( $tline as $tmpLine ) {
			$line = $this->make_array( $tmpLine );
			if ( $line[ $column ] == $query_string ) {
				return $line;
				break;
			}
		}
	}
	//  select  next/prev  line(next_prev  ==>  1/next,  2/prev)  by  cx    
	function select_next_prev_line( $column, $query_string, $next_prev ) {
		$tline = $this->read_file();
		$line_key_end = count( $tline ) - 1;
		$line_key = -1;
		foreach ( $tline as $tmpLine ) {
			$line_key++;
			$line = $this->make_array( $tmpLine );
			if ( $next_prev == 1 ) { //  next?    
				if ( $line[ $column ] == $query_string ) {
					if ( $line_key == 0 ) {
						return 0;
					} else {
						$line_key_up = $line_key - 1;
						return $up_line;
					}
				} else {
					$up_line = $line;
				}
			} elseif ( $next_prev == 2 ) { //  prev?    
				if ( $line[ $column ] == $query_string ) {
					if ( $line_key == $line_key_end ) {
						return 0;
					} else {
						$line_key_down = $line_key + 1;
						break;
					}
				}
			} else {
				return 0;
			}
		}
		$down_line = $this->make_array( $tline[ $line_key_down ] );
		return $down_line;
	}
}
?>