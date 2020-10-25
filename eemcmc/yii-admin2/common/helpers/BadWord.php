<?php

namespace common\helpers;

/**
 * 脏词过滤
 */
class BadWord
{

	/**
	 * 匹配出来的脏词列表
	 * @var array
	 */
	private $badwords;

	/**
	 * 脏词字典
	 * @var array 
	 */
	private $dict;

	/**
	 * 结果
	 * @var string 
	 */
	private $result;

	/**
	 * 返回Push唯一实例
	 * @staticvar \common\helpers\BadWord $instance
	 * @param array $config
	 * @return \common\helpers\BadWord
	 */
	public static function instance()
	{
		static $instance;
		if (is_null($instance))
		{
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->dict = $this->_loadDict();
	}

	/**
	 * 获取关键字列表
	 * @return array
	 */
	private function getKeywords()
	{
		$file = dirname(\Yii::$app->basePath) . '/common/config/badword.php';
		$keywords = require_once($file);
		return $keywords;
	}

	/**
	 * 
	 * @return type
	 */
	public function getDict()
	{
		return $this->dict;
	}

	/**
	 * 获取匹配的脏词
	 * 
	 * @return string
	 */
	public function getBadwords()
	{
		return $this->badwords;
	}

	/**
	 * 加载脏词字典
	 * @return array
	 */
	private function _loadDict()
	{
		$keywords = $this->getKeywords();
		$dict = array();
		foreach ($keywords as $keyword)
		{
			if (empty($keyword))
			{
				continue;
			}
			$key = iconv_substr($keyword, 0, 2, 'UTF-8');
			$dict[$key]['list'][] = $keyword;
			if (!isset($dict[$key]['max']))
			{
				$dict[$key]['max'] = 0;
			}
			$dict[$key]['max'] = max($dict[$key]['max'], iconv_strlen($keyword, 'UTF-8'));
		}
		return $dict;
	}

	/**
	 * 检查文本中是否有脏词
	 * @param string $content
	 * @return boolean
	 */
	public function hasBadWord($content)
	{
		$len = iconv_strlen($content, 'UTF-8');
		for ($i = 0; $i < $len; ++$i)
		{
			$key = iconv_substr($content, $i, 2, 'UTF-8');
			if (array_key_exists($key, $this->dict))
			{
				$badword = iconv_substr($content, $i, $this->dict[$key]['max'], 'UTF-8');
				if ($this->_deal($badword, $key, $af))
				{
					return true;
				}
				$i += $af;
			}
			else
			{
				$this->result .= iconv_substr($content, $i, 1, 'UTF-8');
			}
		}
		return false;
	}

	/**
	 * 匹配到了关键字时的处理
	 * @param string $res 源字符串
	 * @param string $key 脏词key
	 * @param type $af
	 * @return boolean
	 */
	private function _deal($res, $key, &$af)
	{
		$af = 0;
		foreach ($this->dict[$key]['list'] as $keyword)
		{

			if (iconv_strpos($res, $keyword, 0, 'UTF-8') !== false)
			{
				$this->badwords[] = $keyword;
				$len = iconv_strlen($keyword, 'UTF-8');
				$af = $len - 1;
				$this->result .=str_repeat("*", $len);
				return true;
			}
		}
		$this->result .= iconv_substr($res, 0, 1, 'UTF-8');
		return false;
	}

	/**
	 * 将文本中的敏感词替换成一个*号，并替换指定的符号
	 * 
	 * @param string $content
	 * @return string
	 */
	public function filter($content)
	{

		$len = iconv_strlen($content, 'UTF-8');
		for ($i = 0; $i < $len; ++$i)
		{
			$key = iconv_substr($content, $i, 2, 'UTF-8');
			if (array_key_exists($key, $this->dict))
			{
				$badword = iconv_substr($content, $i, $this->dict[$key]['max'], 'UTF-8');
				$this->_deal($badword, $key, $af);
				$i += $af;
			}
			else
			{
				$this->result .=iconv_substr($content, $i, 1, 'UTF-8');
			}
		}
		return $this->result;
	}

}
