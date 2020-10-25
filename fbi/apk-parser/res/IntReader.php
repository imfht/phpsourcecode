<?php
namespace APKParser;
use \Exception;
/**
 * 
 * Simple helper class that allows reading of integers.
 * AndroidManifest.xml解析相关
 * ---------------------------------------------
 * @Author: 80056038
 * @Date: 2012-5-24 上午10:33:39
 * @path: 
 * @name: AXMLParser.php
 * @version v1.0
 *
 */
class IntReader{
	
	private $m_stream;//DataInputStream
	private $m_bigEndian;//boolean
	private $m_position;
	
	public function __construct($stream, $bigEndian) {
		$this->reset($stream, $bigEndian);
	}
	
	public final function reset($stream, $bigEndian) {
		$this->m_stream = $stream;
		$this->m_bigEndian = $bigEndian;
		$this->m_position = 0;
	}
	
	public final function close() {
		if ($this->m_stream == null) {
			return;
		}
		try {
			$this->m_stream->close();
		} catch (Exception $e) {
			echo 'Message: ' .$e->getMessage();
		}
		$this->reset(null, false);
	}
	
	public final function getStream() {
		return $this->m_stream;
	}
	
	public final function isBigEndian() {
		return $this->m_bigEndian;
	}
	
	public final function setBigEndian($bigEndian) {
		$this->m_bigEndian = $bigEndian;
	}
	
	public final function readByte() {
		return $this->readInt(1);
	}
	
	public final function readShort() {
		return $this->readInt(2);
	}
	
// 	public final function readInt() {
// 		return $this->readInt(4);
// 	}
	
	public final function readInt($length=4) {
		if ($length < 0 || $length > 4) {
			throw new Exception();
		}
		$result = 0;
		if ($this->m_bigEndian) {
			for ($i = ($length - 1) * 8; $i >= 0; $i -= 8) {
				$b = $this->m_stream->read();
				if ($b == -1) {
					throw new Exception();
				}
				$this->m_position += 1;
				$result |= ($b << $i);
			}
		} else {
			$length *= 8;
			for ($i = 0; $i != $length; $i += 8) {
				$b = $this->m_stream->readByte();
				if ($b == -1) {
					throw new Exception();
				}
				$this->m_position += 1;
				$result |= ($b << $i);
			}
		}
		return $result;
	}
	
// 	public final function readIntArray($length) {
// 		$array = new int[length];
// 		$this->readIntArray($array, 0, length);
// 		return $array;
// 	}
	
	public final function readIntArray($array, $offset=null, $length=null){
		if($offset === null && $length===null){
			$length = $array;
			$array = array();
			return $this->readIntArray($array, 0, $length);
		}else{
			for (; $length > 0; $length -= 1) {
				$array[$offset++] = $this->readInt();
			}
			return $array;
		}
	}
	
	public final function readByteArray($length) {
// 		$array = new byte[length];
		$array = array();
		$read = $this->m_stream->read($array);
		$this->m_position += $read;
		if ($read != $length) {
			throw new Exception();
		}
		return $array;
	}
	
	public final function skip($bytes) {
		if ($bytes <= 0) {
			return;
		}
		$skipped = $this->m_stream->skip($bytes);
		$this->m_position += $skipped;
		if ($skipped != $bytes) {
			throw new Exception();
		}
	}
	
	public final function skipInt() {
		$this->skip(4);
	}
	
	public final function available(){
		return $this->m_stream->available();
	}
	
	public final function getPosition() {
		return $this->m_position;
	}
}

?>