<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 内容抽取算法
*/

defined('INPOP') or exit('Access Denied');

class textextract {
	
	public $rawPageCode = '';
	public $textLines   = array();
	public $blksLen     = array();
	public $text        = '';
	public $blkSize;

	//初始化
	public function __construct() {}
	
	//启动
	public function init($_rawPageCode, $_blkSize = 3){
		$this->rawPageCode = $_rawPageCode;
		$this->blkSize     = $_blkSize;	
	}
	
	//清空
	public function clear(){
		$this->rawPageCode = '';
		$this->blkSize     = 0;
		$this->textLines   = array();
		$this->blksLen     = array();
		$this->text        = '';	
	}
	
	//处理页面
	public function preProcess() {
		$content = $this->rawPageCode;
		
		// 1. DTD information
		$pattern = '/<!DOCTYPE.*?>/si';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 2. HTML comment
		$pattern = '/<!--.*?-->/s';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 3. Java Script
		$pattern = '/<script.*?>.*?<\/script>/si';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 4. CSS
		$pattern = '/<style.*?>.*?<\/style>/si';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 5. HTML TAGs
		$pattern = '/<.*?>/s';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 6. some special charcaters
		$pattern = '/&.{1,5};|&#.{1,5};/';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		return $content;
	}
	
	//统计内容行数
	public function getTextLines( $rawText ) {
		// do some replacement
		$order = array( "\r\n", "\n", "\r" );
		$replace = '\n';
		$rawText = str_replace( $order, $replace, $rawText );
		
		$lines = explode( '\n', $rawText );
		
		foreach( $lines as $line ) {
			// remove the blanks in each line
			$tmp = preg_replace( '/\s+/s', '', $line );
			$this->textLines[] = $tmp;
		}
	}
	
	//统计内容块大小
	public function calBlocksLen() {
		$textLineNum = count( $this->textLines );
		
		// calculate the first block's length
		$blkLen = 0;
		for( $i = 0; $i < $this->blkSize; $i++ ) {
			$blkLen += strlen( $this->textLines[$i] );
		}
		$this->blksLen[] = $blkLen;
		
		// calculate the other block's length using Dynamic Programming method
		for( $i = 1; $i < ($textLineNum - $this->blkSize); $i++ ) {
			$blkLen = $this->blksLen[$i - 1] + strlen( $this->textLines[$i - 1 + $this->blkSize] ) - strlen( $this->textLines[$i - 1] );
			$this->blksLen[] = $blkLen;
		}
	}
	
	//获取内容
	public function getPlainText() {
		$preProcText = $this->preProcess();
		$this->getTextLines( $preProcText );
		$this->calBlocksLen();
		
		$start = $end = -1;
		$i = $maxTextLen = 0;
		
		$blkNum = count( $this->blksLen );
		
		while( $i < $blkNum ) {
			while( ($i < $blkNum) && ($this->blksLen[$i] == 0) ) $i++;
			if( $i >= $blkNum ) break;
			$tmp = $i;
			
			$curTextLen = 0;
			$portion = '';
			while( ($i < $blkNum) && ($this->blksLen[$i] != 0) ) {
				$portion .= "\n".$this->textLines[$i];
				$curTextLen += $this->blksLen[$i];
				$i++;
			}
			if( $curTextLen > $maxTextLen ) {
				$this->text = $portion;
				$maxTextLen = $curTextLen;
				$start = $tmp;
				$end = $i - 1;
			}
		}
		return $this->text;
	}
}
?>