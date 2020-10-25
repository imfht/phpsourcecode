<?php
namespace APKParser;
use \Exception;
require_once dirname(__FILE__).'/../util/DataInputStream.php';
require_once 'ReadUtil.php';
require_once 'TagAttribute.php';
require_once 'StringBlock.php';
require_once 'IntReader.php';

/**
 * 
 * AndroidManifest.xml解析
 * Parser for Android's binary xml files (axml).
 * ---------------------------------------------
 * @Author: 80056038
 * @Date: 2012-5-24 上午10:22:01
 * @path: 
 * @name: AXMLParser.php
 * @version v1.0
 *
 */
class AXMLParser{
	
	private $m_stream;//DataInputStream
	private $m_strings;//StringBlock 
	private $m_resourceIDs;//array

	private $m_tagType;
	private $m_tagSourceLine;
	private $m_tagName;
	private $m_tagAttributes;//TagAttribute[]
	
	private $m_nextException;
	
	const AXML_CHUNK_TYPE = 0x00080003;
	const RESOURCEIDS_CHUNK_TYPE = 0x00080180;
	
	/**
	 * Types of returned tags.
	 * Values are compatible to those in XmlPullParser.
	 */
	const START_DOCUMENT = 0,
			END_DOCUMENT = 1,
			   START_TAG = 2,
				 END_TAG = 3,
					TEXT = 4;
	
	/**
	 * Creates object and reads file info.
	 * Call next() to read first tag.
	 */
	public function __construct($stream) {
		$this->m_stream = $stream;
		$this->doStart();
	}
	
	public final function getApkInfos( $gets=null ){
		try {
			$info = array();
			while( $this->next() != self::END_DOCUMENT){
				$name = $this->getName();
				if(null == $name||$this->getAttributeCount()==0||(isset( $gets )&&!in_array($name,$gets))){
					continue;
				}
				if( isset( $gets ) && count($info)==count($gets) && !isset($info[$name]) ) {
					break;
				}
				$v=array();
				for( $i=0; $i< $this->getAttributeCount(); $i++ ) {
					$valueType = $this->getAttributeValueType($i);
					if( $valueType==3 ){
						$v[$this->getAttributeName($i)] = $this->getAttributeValueString($i);
					} else if( $valueType == 16 ) {
						$v[$this->getAttributeName($i)] = $this->getAttributeValue($i);
					}
				}
				!empty($v)&&$info[$name][] = $v;
			}
			return $info;
		} catch ( Exception $e ) {
			return array();
		}
	}
	
	private final function doStart() {
		ReadUtil::readCheckType($this->m_stream, self::AXML_CHUNK_TYPE);
		/* chunk size */ReadUtil::readInt($this->m_stream);
	
		$this->m_strings = StringBlock::read(new IntReader($this->m_stream, false));
	
		ReadUtil::readCheckType($this->m_stream, self::RESOURCEIDS_CHUNK_TYPE);
		$chunkSize = ReadUtil::readInt($this->m_stream);
		if ($chunkSize < 8 || ($chunkSize % 4) != 0) {
			throw new Exception("Invalid resource ids size (".$chunkSize.").");
		}
		$this->m_resourceIDs = ReadUtil::readIntArray($this->m_stream, $chunkSize / 4 - 2);
	
		$this->resetState();
	}
	
	private final function resetState() {
		$this->m_tagType = -1;
		$this->m_tagSourceLine = -1;
		$this->m_tagName = -1;
		$this->m_tagAttributes = null;
	}
	
	/**
	 * Closes parser:
	 *      * closes (and nulls) underlying stream
	 *      * nulls dynamic data
	 *      * moves object to 'closed' state, where methods
	 *        return invalid values and next() throws IOException.
	 */
	public final function close() {
		if ($this->m_stream == null) {
			return;
		}
		try {
			$this->m_stream->close();
		} catch (Exception $e) {
			
		}
		if ($this->m_nextException == null) {
			$this->m_nextException = new Exception("Closed.");
		}
		$this->m_stream = null;
		$this->resetState();
	}
	
	/**
	 * Advances to the next tag.
	 * Once method returns END_DOCUMENT, it always returns END_DOCUMENT.
	 * Once method throws an exception, it always throws the same exception.
	 *
	 */
	public final function next() {
		if ($this->m_nextException != null) {
			throw $this->m_nextException;
		}
		try {
			return $this->doNext();
		} catch (Exception $e) {
			$this->m_nextException = $e;
			$this->resetState();
			throw $e;
		}
	}
	
	/**
	 * Returns current tag type.
	 */
	public final function getType() {
		return $this->m_tagType;
	}
	
	/**
	 * Returns name for the current tag.
	 */
	public final function getName() {
		if ($this->m_tagName == -1) {
			return null;
		}
		return $this->getString($this->m_tagName);
	}
	
	/**
	 * Returns line number in the original XML where the current tag was.
	 */
	public final function getLineNumber() {
		return $this->m_tagSourceLine;
	}
	
	/**
	 * Returns count of attributes for the current tag.
	 */
	public final function getAttributeCount() {
		if ($this->m_tagAttributes == null) {
			return -1;
		}
		return count($this->m_tagAttributes);
	}
	
	/**
	 * Returns attribute namespace.
	 */
	public final function getAttributeNamespace($index) {
		return $this->getString($this->getAttribute($index)->namespace);
	}
	
	/**
	 * Returns attribute name.
	 */
	public final function getAttributeName($index) {
		return $this->getString($this->getAttribute($index)->name);
	}
	
	/**
	 * Returns attribute resource ID.
	 */
	public final function getAttributeResourceID($index) {
		$resourceIndex = $this->getAttribute($index)->name;
		if ($this->m_resourceIDs == null || $resourceIndex < 0 
				|| $resourceIndex >= count($this->m_resourceIDs)) {
			return 0;
		}
		return $this->m_resourceIDs[$resourceIndex];
	}
	
	/**
	 * Returns type of attribute value.
	 * See TypedValue.TYPE_ values.
	 */
	public final function getAttributeValueType($index) {
		return $this->getAttribute($index)->valueType;
	}
	
	/**
	 * For attributes of type TypedValue.TYPE_STRING returns
	 *  string value. For other types returns empty string.
	 */
	public final function getAttributeValueString($index) {
		return $this->getString($this->getAttribute($index)->valueString);
	}
	
	/**
	 * Returns integer attribute value.
	 * This integer interpreted according to attribute type.
	 */
	public final function getAttributeValue($index) {
		return $this->getAttribute($index)->value;
	}
	
	// /////////////////////////////////////////// implementation
	
	private final function doNext() {
		if ($this->m_tagType == self::END_DOCUMENT) {
			return self::END_DOCUMENT;
		}
	
		$this->m_tagType = (ReadUtil::readInt($this->m_stream) & 0xFF);/* other 3 bytes? */
		/* some source length */ReadUtil::readInt($this->m_stream);
		$this->m_tagSourceLine = ReadUtil::readInt($this->m_stream);
		/* 0xFFFFFFFF */ReadUtil::readInt($this->m_stream);
	
		$this->m_tagName = -1;
		$this->m_tagAttributes = null;
	
		switch ($this->m_tagType) {
			case self::START_DOCUMENT: {
				/* namespace? */ReadUtil::readInt($this->m_stream);
				/* name? */ReadUtil::readInt($this->m_stream);
				break;
			}
			case self::START_TAG: {
				/* 0xFFFFFFFF */ReadUtil::readInt($this->m_stream);
				$this->m_tagName = ReadUtil::readInt($this->m_stream);
				/* flags? */ReadUtil::readInt($this->m_stream);
				$attributeCount = ReadUtil::readInt($this->m_stream);
				/* ? */ReadUtil::readInt($this->m_stream);
// 				$m_tagAttributes = new TagAttribute[$attributeCount];
				$m_tagAttributes = array();
				for ($i = 0; $i != $attributeCount; ++$i) {
					$attribute = new TagAttribute();
					$attribute->namespace = ReadUtil::readInt($this->m_stream);
					$attribute->name = ReadUtil::readInt($this->m_stream);
					$attribute->valueString = ReadUtil::readInt($this->m_stream);
					$attribute->valueType = (ReadUtil::readInt($this->m_stream) >> 24);/*
					* other
					* 3
					* bytes
					* ?
					*/
					$attribute->value = ReadUtil::readInt($this->m_stream);
					$this->m_tagAttributes[$i] = $attribute;
				}
				break;
			}
			case self::END_TAG: {
				/* 0xFFFFFFFF */ReadUtil::readInt($this->m_stream);
				$this->m_tagName = ReadUtil::readInt($this->m_stream);
				break;
			}
			case self::TEXT: {
				$this->m_tagName = ReadUtil::readInt($this->m_stream);
				/* ? */ReadUtil::readInt($this->m_stream);
				/* ? */ReadUtil::readInt($this->m_stream);
				break;
			}
			case self::END_DOCUMENT: {
				/* namespace? */ReadUtil::readInt($this->m_stream);
				/* name? */ReadUtil::readInt($this->m_stream);
				break;
			}
			default: {
				throw new Exception("Invalid tag type (".$this->m_tagType.").");
			}
		}
		return $this->m_tagType;
	}
	
	private final function getAttribute($index) {
		if ($this->m_tagAttributes == null) {
			throw new Exception("Attributes are not available.");
		}
		if ($index >= count( $this->m_tagAttributes ) ) {
			throw new Exception("Invalid attribute index (".$index.").");
		}
		return $this->m_tagAttributes[$index];
	}
	
	private final function getString($index) {
		if ($index == -1) {
			return "";
		}
		return $this->m_strings->getRaw($index);
	}
	
	// ///////////////////////////////// data
}

?>