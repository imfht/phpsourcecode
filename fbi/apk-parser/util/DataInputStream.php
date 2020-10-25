<?php
/**
 * 流解析
 * ---------------------------------------------
 * @Author: Simon 夏向红
 * @Date: 2012-02-22
 * @package cn.com.nearme.gostore.theme
 * @name :DataInputStream.php
 * @version v1.0
 * 
*/
namespace APKParser;
use \Exception;
class DataInputStream {
	private $stream = null;
	
	public function __construct( $in ){
		$this->stream = $in;
	}
	
	public function __destruct(){
		@fclose( $this->stream );
	}
	
	private function read( $len=1 ){
		$len = intval( $len );
		if( $len>0 ) {
			if( !feof( $this->stream ) ) {
				$read = @fread($this->stream,$len);
				if( strlen($read)!=$len ){
					throw new Exception('流文件已经到末尾，读取错误',222);
				} else {
					return $read;
				}
			} else {
				throw new Exception('流文件已经到末尾，读取错误',222);
			}
		} else {
			return '';
		}
	}
	
	public function readByte(){
		return hexdec(bin2hex($this->read(1)));
	}
	
	public function readShort(){
		return hexdec(bin2hex($this->read(2)));
	}
	
	public function readInt(){
		return hexdec(bin2hex($this->read(4)));
	}
	
	public function readUTF(){
		$len = $this->readShort();
		return $this->read($len);
	}
	
	public function readFully( $len ) {
		return $this->read( $len );
	}
	
	public function readString(){
		return $this->readUTF();
	}
	
	public function readLong(){
		return hexdec(bin2hex($this->read(8)));
	}
}
?>