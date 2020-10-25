<?php
namespace APKParser;
use \Exception;
require_once 'IntReader.php';

/**
 * 
 * Block of strings, used in binary xml and arsc.
 * AndroidManifest.xml解析相关
 * ---------------------------------------------
 * @Author: 80056038
 * @Date: 2012-5-24 上午10:30:03
 * @path: 
 * @name: StringBlock.php
 * @version v1.0
 *
 */

class StringBlock{
	private $m_stringOffsets;//array
	private $m_strings;//array
	private $m_styleOffsets;//array
	private $m_styles;//array

	const CHUNK_TYPE = 0x001C0001;
	
	public $s;//array
	
	/**
	 * Reads whole (including chunk type) string block from stream.
	 * Stream must be at the chunk type.
	 */
	public static function read($reader){
		ReadUtil::readCheckType($reader, self::CHUNK_TYPE);
		$chunkSize   		= $reader->readInt();
		$stringCount 		= $reader->readInt();
		$styleOffsetCount 	= $reader->readInt();
		/* ? */               $reader->readInt();
		$stringsOffset      = $reader->readInt();
		$stylesOffset       = $reader->readInt();

		$block = new StringBlock();
		$block->m_stringOffsets = $reader->readIntArray($stringCount);
		if ($styleOffsetCount != 0) {
			$block->m_styleOffsets = $reader->readIntArray($styleOffsetCount);
		}
		{
			$size = (($stylesOffset == 0) ? $chunkSize : $stylesOffset) - $stringsOffset;
			if (($size % 4) != 0) {
				throw new Exception("String data size is not multiple of 4 (".size.").");
			}
			$block->m_strings = $reader->readIntArray($size / 4);
		}
		if ($stylesOffset != 0) {
			$size = ($chunkSize - $stylesOffset);
			if (($size % 4) != 0) {
				throw new Exception("Style data size is not multiple of 4 (".size.").");
			}
			$block->m_styles = $reader->readIntArray($size / 4);
		}

		$block->s = array();
		for ($i = 0; $i != $block->getCount(); ++$i) {
			$block->s[$i] = $block->getRaw($i);
		}

		return $block;
	}
	
	/**
	 * Returns number of strings in block.
	 */
	public function getCount() {
		return $this->m_stringOffsets != null ? count($this->m_stringOffsets) : 0;
	}
	
	/**
	 * Returns raw string (without any styling information) at specified index.
	 * Returns null if index is invalid or object was not initialized.
	 */
	public function getRaw($index) {
		if ($index < 0 || $this->m_stringOffsets == null || $index >= count($this->m_stringOffsets)) {
			return null;
		}
		$offset = $this->m_stringOffsets[$index];
		$length = $this->getShort($this->m_strings, $offset);
		$result = '';
		for (; $length != 0; $length -= 1) {
			$offset += 2;
			$result = $result.chr($this->getShort($this->m_strings, $offset));
		}
		return $result;
	}
	
	/**
	 * Not yet implemented.
	 *
	 * Returns string with style information (if any).
	 * Returns null if index is invalid or object was not initialized.
	 */
	public function get($index) {
		return Cast::toCharSequence($this->getRaw($index));
	}
	
	/**
	 * Returns string with style tags (html-like).
	 */
	/**
	public function getHTML($index) {
		$raw = $this->getRaw(index);
		if ($raw == null) {
			return $raw;
		}
		$style = $this->getStyle(index);
		if ($style == null) {
			return $raw;
		}
// 		StringBuilder html = new StringBuilder(raw.length() + 32);
		$html = $raw.length().'32';
		$offset = 0;
		while (true) {
			$i = -1;
			for ($j = 0; $j != style.length; $j += 3) {
				if (style[$j + 1] == -1) {
					continue;
				}
				if ($i == -1 || style[$i + 1] > style[$j + 1]) {
					$i = $j;
				}
			}
			$start = (($i != -1) ? style[$i + 1] : raw.length());
			for ($j = 0; $j != style.length; $j += 3) {
				$end = style[$j + 2];
				if ($end == -1 || $end >= $start) {
					continue;
				}
				if ($offset <= $end) {
					$html.append(raw, offset, end + 1);
					$offset = $end + 1;
				}
				style[$j + 2] = -1;
				$html.append('<');
				$html.append('/');
				$html.append($this->getRaw(style[$j]));
				$html.append('>');
			}
			if ($offset < $start) {
				html.append($raw, $offset, $start);
				$offset = $start;
			}
			if ($i == -1) {
				break;
			}
			$html.append('<');
			$html.append(getRaw(style[i]));
			$html.append('>');
			style[$i + 1] = -1;
		}
		return $html;
	}
	*/
	
	/**
	 * Finds index of the string.
	 * Returns -1 if the string was not found.
	 */
	public function find($string) {
		if ($string == null) {
			return -1;
		}
		for ($i = 0; $i != count($this->m_stringOffsets); ++$i) {
			$offset = $this->m_stringOffsets[$i];
			$length = $this->getShort($this->$m_strings, $offset);
			if ($length != strlen($string)) {
				continue;
			}
			$j = 0;
			for (; $j != $length; ++$j) {
				$offset += 2;
				if ($string[j] != $this->getShort($this->m_strings, $offset)) {
					break;
				}
			}
			if ($j == $length) {
				return $i;
			}
		}
		return -1;
	}

		// 	// /////////////////////////////////////////// implementation
		
	private function StringBlock() {
	}
	
	/**
	 * Returns style information - array of int triplets,
	 * where in each triplet:
	 *      * first int is index of tag name ('b','i', etc.)
	 *      * second int is tag start index in string
	 *      * third int is tag end index in string
	 */
	private function getStyle($index) {
		if ($this->m_styleOffsets == null || $this->m_styles == null || $index >= $this->m_styleOffsets.length) {
			return null;
		}
		$offset = $this->m_styleOffsets[$index] / 4;
		$style = array();
		{
			$count = 0;
			for ($i = $offset; $i < $this->m_styles.length; ++$i) {
				if ($this->m_styles[$i] == -1) {
					break;
				}
				$count += 1;
			}
			if ($count == 0 || ($count % 3) != 0) {
				return null;
			}
			$style = array();
		}
		for ($i = $offset, $j = 0; $i < $this->m_styles.length;) {
			if ($this->m_styles[$i] == -1) {
				break;
			}
			$style[$j++] = $this->m_styles[$i++];
		}
		return $style;
	}

	private static final function getShort($array, $offset) {
		$value = $array[$offset / 4];
		if (($offset % 4) / 2 == 0) {
			return ($value & 0xFFFF);
		} else {
			return ($value >> 16);
		}
	}
}

?>