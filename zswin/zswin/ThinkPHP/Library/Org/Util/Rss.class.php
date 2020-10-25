<?php
namespace Org\Util;

class Rss {//类定义开始

	private $config =   array(
	    'encoding'          	=>  'UTF-8',					// 缩略图扩展名 
        'rssVer'           		=>  '2.0',    					// 上传文件的最大值
        'channelTitle'      	=>  '',    						// 网站标题
        'channelLink'         	=>  '',    						// 网站首页地址
        'channelDescrīption'    =>  '',    						// 描述
        'language'             	=>  'zh_CN',    				// 使用的语言（zh-cn表示简体中文）
        'copyright'    			=>  '',    						// 授权信息
        'webMaster'     		=>  '',							// 管理员邮箱
        'managingEditor'    	=>  '',							// 编辑的邮箱地址
        'docs'       			=>  '',							// rss地址
        'pubDate'      			=>  '',							// 最后发布的时间
        'lastBuildDate'         =>  '',							// 最后更新的时间
        'generator'         	=>  'zswinBlog RSS Generator',		// 生成器
		'category'				=>	'',
        );
	
	// 生成的原RSS
    private $content = '';
    // Items部分
    private $items = array();
	
    public function __get($name){
        if(isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }

    public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    public function __isset($name){
        return isset($this->config[$name]);
    }
	
	public function content($name){
        if (empty($this->content)) $this->BuildRSS();
		$this->content;
    }
	
    /**
     * 架构函数
     * @access public
     * @param array $config  上传参数
     */
    public function __construct($config=array()) {
		$this->config['pubDate'] = Date('Y-m-d H:i:s',time());
		$this->config['lastBuildDate'] = Date('Y-m-d H:i:s',time());
        if(is_array($config)) {
            $this->config   =   array_merge($this->config,$config);
        }
    }
	
	/**************************************************************************/
	// 函数名: AddItem
	// 功能: 添加一个节点
	// 参数: $title
	// $link
	// $descrīption $pubDate
	/**************************************************************************/
	function AddItem($title, $link, $descrīption ,$pubDate ,$guid ,$author = "zswin" ,$category ) {
		$this->items[] = array('title' => $title ,
								'link' => $link,
								'descrīption' => $descrīption,
								'pubDate' => $pubDate,
								'category' => $category,//实现分组
								'author' => $author,
								'guid' => $guid);
	}
	/**************************************************************************/
	// 函数名: BuildRSS
	// 功能: 生成rss xml文件内容
	/**************************************************************************/
	function BuildRSS() {
		$s = "<?xml version='1.0' encoding='{$this->encoding}'?>\r\n<rss version=\"{$this->rssVer}\">\n";
		$s .= "\t<channel>\r\n";
		$s .= "\t\t<title><![CDATA[{$this->channelTitle}]]></title>\r\n";
		$s .= "\t\t<link><![CDATA[{$this->channelLink}]]></link>\r\n";
		$s .= "\t\t<descrīption><![CDATA[{$this->channelDescrīption}]]></descrīption>\r\n";
		$s .= "\t\t<language>{$this->language}</language>\r\n";
		if (!empty($this->docs)) {
			$s .= "\t\t<docs><![CDATA[{$this->docs}]]></docs>\r\n";
		}
		if (!empty($this->copyright)) {
			$s .= "\t\t<copyright><![CDATA[{$this->copyright}]]></copyright>\r\n";
		}
		if (!empty($this->webMaster)) {
			$s .= "\t\t<webMaster><![CDATA[{$this->webMaster}]]></webMaster>\r\n";
		}
		if (!empty($this->managingEditor)) {
			$s .= "\t\t<managingEditor><![CDATA[{$this->managingEditor}]]></managingEditor>\r\n";
		}
		if (!empty($this->pubDate)) {
			$s .= "\t\t<pubDate>{$this->pubDate}</pubDate>\r\n";
		}
		if (!empty($this->lastBuildDate)) {
			$s .= "\t\t<lastBuildDate>{$this->lastBuildDate}</lastBuildDate>\r\n";
		}
		if (!empty($this->generator)) {
			$s .= "\t\t<generator>{$this->generator}</generator>\r\n";
		}
		// items
		for ($i=0;$i<count($this->items);$i++) {
			$s .= "\t\t<item>\n";
			$s .= "\t\t\t<title><![CDATA[{$this->items[$i]['title']}]]></title>\r\n";
			$s .= "\t\t\t<link><![CDATA[{$this->items[$i]['link']}]]></link>\r\n";
			$s .= "\t\t\t<descrīption><![CDATA[{$this->items[$i]['descrīption']}]]></descrīption>\r\n";
			$s .= "\t\t\t<pubDate>{$this->items[$i]['pubDate']}</pubDate>\r\n";
			if (!empty($this->items[$i]['category'])) {
				$s .= "\t\t\t<category>{$this->items[$i]['category']}</category>\r\n";
			}
			if (!empty($this->items[$i]['author'])) {
				$s .= "\t\t\t<author>{$this->items[$i]['author']}</author>\r\n";
			}
			if (!empty($this->items[$i]['guid'])) {
				$s .= "\t\t\t<guid>{$this->items[$i]['guid']}</guid>\r\n";
			}
			$s .= "\t\t</item>\n";
		}
		// close
		$s .= "\t</channel>\r\n</rss>";
		$this->content = $s;
	}
	
	/**************************************************************************/
	// 函数名: Show
	// 功能: 将产生的rss内容直接打印输出
	/**************************************************************************/
	function Show() {
		if (empty($this->content)) $this->BuildRSS();
		echo($this->content);
	}
	
	/**************************************************************************/
	// 函数名: SaveToFile
	// 功能: 将产生的rss内容保存到文件
	// 参数: $fname 要保存的文件名
	/**************************************************************************/
	function SaveToFile($fname) {
		if (empty($this->content)) $this->BuildRSS();
		$handle = fopen($fname, 'w+');
		if ($handle === false) return false;
		fwrite($handle, $this->content);
		fclose($handle);
	}
	
	/**************************************************************************/
	// 函数名: getFile
	// 功能: 从文件中获取输出
	// 参数: $fname 文件名
	/**************************************************************************/
	function getFile($fname) {
		$handle = fopen($fname, 'r');
		if ($handle === false) return false;
		while(!feof($handle)){
			echo fgets($handle);
		}
		fclose($handle);
		}
}