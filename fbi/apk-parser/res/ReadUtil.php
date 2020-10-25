<?php
namespace APKParser;
use \Exception;
require_once 'IntReader.php';

/**
 * 
 * Various read helpers.
 * AndroidManifest.xml解析相关
 * ---------------------------------------------
 * @Author: 80056038
 * @Date: 2012-5-24 上午10:35:33
 * @path: 
 * @name: ReadUtil.php
 * @version v1.0
 *
 */

class ReadUtil{
	
	public static final function readCheckType($stream, $expectedType){
		if ( $stream instanceof DataInputStream){
			$type = self::readInt($stream);
		}
		if ( $stream instanceof IntReader) {
			$type = $stream->readInt();
		}
		if ($type != $expectedType) {
			throw new Exception("Expected chunk of type 0x"
					.$expectedType.", read 0x"
					.$type.".");
		}
	}
	
	public static final function readIntArray( $stream, $elementCount){
// 		$result = new int[$elementCount];
		$result = array();
		for ($i = 0; $i != $elementCount; ++$i) {
			$result[$i] = self::readInt($stream);
		}
		return $result;
	}
	
// 	public static final function readInt($stream) {
// 		return readInt($stream, 4);
// 	}
	
	public static final function readShort($stream) {
		return self::readInt($stream, 2);
	}
	
	public static final function readString($stream){
		$length = self::readShort($stream);
		$builder = $length.'';
		for ($i = 0; $i != $length; ++$i) {
			$builder.self::readShort($stream);
		}
		self::readShort($stream);
		return $builder;
	}
	
	public static final function readInt($stream, $length=4){
		$result = 0;
		for ($i = 0; $i != $length; ++$i) {
			$b = $stream->readByte();
			if ($b == -1) {
				throw new Exception();
			}
			$result |= ($b << ($i * 8));
		}
		return $result;
	}
	
}

?>