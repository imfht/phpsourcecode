<?php

/**
 * Sitemap类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\model;
use \Sy;
use \XMLWriter;
use \sy\base\Router;
use \sy\base\SYException;
use \sy\lib\db\Mysql;
use \blog\libs\Common;

class Sitemap {
	protected $out_dir;
	protected $config;
	protected $changefreq;
	protected $indexWriter;
	protected $smWriter;
	protected $baseUri;
	protected $home;
	public function __construct() {
		$this->config = unserialize(Common::option('seoSitemap'));
		$this->changefreq = $this->config['changefreq'];
		$this->out_dir = Sy::$siteDir;
		$this->baseUri = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
		if (isset($_SERVER['SERVER_PORT'])) {
			if ((isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] != '443') || (!isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] != '80')) {
				$this->baseUri .= ':' . $_SERVER['SERVER_PORT'];
			}
		}
		$this->home = Router::createUrl();
	}
	public function make($out_dir = '') {
		if (!empty($out_dir)) {
			$this->out_dir = $out_dir;
		}
		if (!$this->config['enable']) {
			return FALSE;
		}
		if ($this->config['type'] === 'index') {
			$this->mkIndex();
		} else {
			$this->mkXml();
		}
	}
	/**
	 * 建立一个新XMLWriter类
	 */
	protected function newXMLWriter($output) {
		$xmlwriter = new XMLWriter();
		$xmlwriter->openURI($output);
		$xmlwriter->setIndent(TRUE);
		$xmlwriter->setIndentString(' ');
		$xmlwriter->startDocument('1.0', 'utf-8');
		$xmlwriter->writePI('xml-stylesheet', 'type="text/xsl" href="' . $this->home . 'assets/sitemap.xsl"');
		return $xmlwriter;
	}
	/**
	 * 以索引模式建立Sitemap
	 * @access public
	 */
	public function mkIndex() {
		$db = Mysql::i();
		if (!is_dir($this->out_dir)) {
			Common::mkdirs($this->out_dir);
		}
		$this->indexWriter = $this->newXMLWriter($this->out_dir . 'sitemap.xml');
		$this->indexWriter->startElement('sitemapindex');
		$this->indexWriter->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		//总数
		$article_max = $db->getOne('SELECT count(*) as num FROM `#@__article`');
		$article_max = $article_max['num'];
		$meta_max = $db->getOne('SELECT count(*) as num FROM `#@__meta`');
		$meta_max = $meta_max['num'];
		//单个Sitemap文件限制分别为2万个、1万个
		$article_sitemap_num = intval(ceil($article_max / 20000));
		$meta_sitemap_num = intval(ceil($meta_max / 10000));
		//Sitemap的列表
		$sm = [ 'article' => [], 'meta' => []];
		//生成索引文件
		//Article
		for ($i = 1;$i <= $article_sitemap_num; $i++) {
			$this->indexWriter->startElement('sitemap');
			$this->indexWriter->writeElement('loc', $this->baseUri . $this->home . 'sitemap/article_' .$i . '.xml');
			$limit = $i * 20000 -1;
			$start = isset($sm['article'][$i - 1])?($sm['article'][$i - 1]['max'] + 1):0;
			if ($article_sitemap_num === $i) {
				$last_id = $db->getOne('SELECT id FROM `#@__article` ORDER BY id DESC LIMIT 0,1');
				$last_id = $last_id['id'];
				$last = $db->getOne('SELECT MAX(modify) as modify FROM `#@__article` WHERE id BETWEEN ? AND ?', [$start, $last_id]);
			} else {
				$last_id = $db->getOne("SELECT id FROM `#@__article` ORDER BY id ASC LIMIT $limit,1");
				$last_id = $last_id['id'];
				$last = $db->getOne('SELECT MAX(modify) as modify FROM `#@__article` WHERE id BETWEEN ? AND ?', [$start, $last_id]);
			}
			$last_modify = date('Y-m-d', $last['modify']);
			$this->indexWriter->writeElement('lastmod', $last_modify);
			if (!is_file($this->out_dir . 'sitemap/article_' .$i . '.xml') || $last['modify'] > filemtime($this->out_dir . 'sitemap/article_' .$i . '.xml')) {
				//需要重新生成
				$remake = TRUE;
			} else {
				$remake = FALSE;
			}
			$sm['article'][$i] = ['remake' => $remake, 'max' => $last_id];
			$this->indexWriter->endElement(); //sitemap
		}
		//Meta
		for ($i = 1;$i <= $meta_sitemap_num; $i++) {
			$this->indexWriter->startElement('sitemap');
			$this->indexWriter->writeElement('loc', $this->baseUri . 'sitemap/meta_' . $i . '.xml');
			$limit = $i * 20000 -1;
			if ($meta_sitemap_num === $i) {
				$last = $db->getOne("SELECT id FROM `#@__meta` ORDER BY id DESC LIMIT 0,1");
			} else {
				$last = $db->getOne("SELECT id FROM `#@__meta` ORDER BY id ASC LIMIT $limit,1");
			}
			$last_modify = $db->getOne("SELECT a.id,a.modify FROM `#@__article` a,`#@__relation` b WHERE a.id = b.aid AND b.mid = ? ORDER BY a.modify DESC LIMIT 0,1", [$last['id']]);
			$lastmod = date('Y-m-d', $last_modify['modify']);
			$this->indexWriter->writeElement('lastmod', $lastmod);
			if (!is_file($this->out_dir . 'sitemap/meta_' .$i . '.xml') || $last_modify['modify'] > filemtime($this->out_dir . 'sitemap/meta_' .$i . '.xml')) {
				//需要重新生成
				$remake = TRUE;
			} else {
				$remake = FALSE;
			}
			$sm['meta'][$i] = ['remake' => $remake, 'max' => $last['id'], 'lastmod' => $last_modify['modify']];
			$this->indexWriter->endElement(); //sitemap
		}
		//生成索引
		$this->indexWriter->endElement(); //sitemapindex
		$this->indexWriter->endDocument();
		$this->indexWriter->flush();
		//更新Sitemap
		if (!is_dir($this->out_dir . 'sitemap')) {
			Common::mkdirs($this->out_dir . 'sitemap');
		}
		//文章
		foreach ($sm['article'] as $k => $v) {
			if (!$v['remake']) {
				continue;
			}
			$this->smWriter = $this->newXMLWriter($this->out_dir . 'sitemap/article_' . $k . '.xml');
			$this->smWriter->startElement('urlset');
			$this->smWriter->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			$start = isset($sm['article'][$k - 1])?($sm['article'][$k - 1]['max'] + 1):0;
			$rs = $db->query('SELECT id,modify FROM `#@__article` WHERE id BETWEEN ? AND ?', [$start, $v['max']]);
			foreach ($rs as $row) {
				$this->smWriter->startElement('url');
				$this->smWriter->writeElement('loc', $this->baseUri . Router::createUrl(['index/article/view', 'id' => $row['id']]));
				$this->smWriter->writeElement('lastmod', date('c', $row['modify']));
				$this->smWriter->writeElement('changefreq', $this->changefreq['article']);
				$this->smWriter->endElement(); //url
			}
			$this->smWriter->endElement(); //urlset
			$this->smWriter->endDocument();
			$this->smWriter->flush();
		}
		//Meta
		foreach ($sm['meta'] as $k => $v) {
			if (!$v['remake']) {
				continue;
			}
			$this->smWriter = $this->newXMLWriter($this->out_dir . 'sitemap/meta_' . $k . '.xml');
			$this->smWriter->startElement('urlset');
			$this->smWriter->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
			$start = isset($sm['meta'][$k - 1])?($sm['meta'][$k - 1]['max'] + 1):0;
			$rs = $db->query('SELECT id FROM `#@__meta` WHERE id BETWEEN ? AND ?', [$start, $v['max']]);
			$pagesize = intval(Common::option('pagesize'));
			foreach ($rs as $row) {
				$article_num = $db->getOne('SELECT count(*) as num FROM `#@__relation` WHERE mid = ?', [$row['id']]);
				$article_num = intval($article_num['num']);
				$page = ceil($article_num / $pagesize);
				for ($i = 1;$i <= $page;$i++) {
					$this->smWriter->startElement('url');
					$this->smWriter->writeElement('loc', Router::createUrl(['index/article/list', 'type' => 'id', 'val' => $row['id'], 'page' => $i]));
					$this->smWriter->writeElement('lastmod', date('c', $v['lastmod']));
					$this->smWriter->writeElement('changefreq', $this->changefreq['meta']);
					$this->smWriter->endElement(); //url
				}
			}
			$this->smWriter->endElement(); //urlset
			$this->smWriter->endDocument();
			$this->smWriter->flush();
		}
	}
	/**
	 * 以xml格式建立Sitemap
	 * @access public
	 */
	public function mkXml() {
		$db = Mysql::i();
		$this->smWriter = $this->newXMLWriter($this->out_dir . 'sitemap.xml');
		$this->smWriter->startElement('urlset');
		$this->smWriter->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$start = isset($sm['article'][$k - 1])?($sm['article'][$k - 1]['max'] + 1):0;
		$rs = $db->query('SELECT * FROM `#@__article` ORDER BY id ASC');
		foreach ($rs as $row) {
			$this->smWriter->startElement('url');
			$this->smWriter->writeElement('loc', $this->baseUri . Router::createUrl(['index/article/view', 'id' => $row['id']]));
			$this->smWriter->writeElement('lastmod', date('c', $row['modify']));
			$this->smWriter->writeElement('changefreq', $this->changefreq['article']);
			$this->smWriter->endElement(); //url
		}
		$rs = $db->query('SELECT * FROM `#@__meta` ORDER BY id ASC');
		$pagesize = intval(Common::option('pagesize'));
		foreach ($rs as $row) {
			$article_num = $db->getOne('SELECT count(*) as num FROM `#@__relation` WHERE mid = ?', [$row['id']]);
			$article_num = intval($article_num['num']);
			$page = ceil($article_num / $pagesize);
			$last_modify = $db->getOne("SELECT a.id,a.modify FROM `#@__article` a,`#@__relation` b WHERE a.id = b.aid AND b.mid = ? ORDER BY a.modify DESC LIMIT 0,1", [$row['id']]);
			for ($i = 1;$i <= $page;$i++) {
				$this->smWriter->startElement('url');
				$this->smWriter->writeElement('loc', $this->baseUri . Router::createUrl(['index/article/list', 'type' => 'id', 'val' => $row['id'], 'page' => $i]));
				$this->smWriter->writeElement('lastmod', date('c', $last_modify['modify']));
				$this->smWriter->writeElement('changefreq', $this->changefreq['meta']);
				$this->smWriter->endElement(); //url
			}
		}
		$this->smWriter->endElement(); //urlset
		$this->smWriter->endDocument();
		$this->smWriter->flush();
	}
}