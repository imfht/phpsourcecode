<?php

/**
 * Feed
 * 
 * @author ShuangYa
 * @package Blog
 * @category Controller
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\controller;
use \Sy;
use \XmlWriter;
use \sy\base\Controller;
use \sy\base\Router;
use \blog\libs\Html;
use \blog\libs\Common;
use \blog\model\Article as ArticleModel;

class Feed extends Controller {
	protected $xml;
	protected $output;
	public function __construct($output = TRUE, $outpath = '') {
		$this->xml = new XmlWriter();
		if ($output) {
			Sy::setMimeType('xml');
			$this->output = TRUE;
			$this->xml->openMemory();
		} else {
			$this->output = FALSE;
			$this->xml->openURI($outpath);
		}
		$this->xml->setIndent(TRUE);
		$this->xml->setIndentString(' ');
		$this->xml->startDocument('1.0', 'UTF-8');
	}
	protected function end() {
		$this->xml->endDocument();
		if ($this->output) {
			//输出
			echo $this->xml->outputMemory(TRUE);
		} else {
			//写入
			$this->xml->flush();
		}
	}
	/**
	 * RSS 2.0
	 */
	public function actionRss() {
		$this->xml->startElement('rss');
		$this->xml->writeAttribute('version', '2.0');
		$this->xml->writeAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
		$this->xml->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
		$this->xml->writeAttribute('xmlns:slash', 'http://purl.org/rss/1.0/modules/slash/');
		$this->xml->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
		$this->xml->writeAttribute('xmlns:wfw', 'http://wellformedweb.org/CommentAPI/');
		$this->xml->startElement('channel');
		$this->xml->writeElement('title', Common::option('sitename'));
		$this->xml->startElement('atom:link');
		$this->xml->writeAttribute('href', Router::createUrl('index/feed/rss', 'xml'));
		$this->xml->writeAttribute('rel', 'self');
		$this->xml->writeAttribute('type', Sy::getMimeType('rss'));
		$this->xml->endElement(); //atom:link
		$this->xml->writeElement('link', Router::createUrl());
		$this->xml->writeElement('description', Html::encode(Common::option('description')));
		$last = ArticleModel::getList(['limit' => '0,1'])->next();
		$this->xml->writeElement('lastBuildDate', $last->getDate('r'));
		$this->xml->writeElement('pubDate', date('r', $last->publish));
		$this->xml->writeElement('language', 'zh-CN');
		$this->xml->startElement('generator');
		$this->xml->text('http://blog.sylingd.com/?from=syblog_' . Common::VERSION);
		$this->xml->endElement(); //generator
		$article = ArticleModel::getList(['body' => TRUE, 'limit' => '0,' . Common::option('pagesize')]);
		while ($row = $article->next()) {
			$this->xml->startElement('item');
			$this->xml->writeElement('title', $row->title);
			$this->xml->writeElement('link', Router::createUrl(['index/article/view', 'id' => $row->id]));
			$this->xml->writeElement('guid', Router::createUrl(['index/article/view', 'id' => $row->id]));
			$this->xml->writeElement('pubDate', date('r', $row->publish));
			$this->xml->startElement('description');
			ob_start();
			$row->excerpt(100, '...');
			$excerpt = ob_get_clean();
			$this->xml->writeCData($excerpt);
			$this->xml->endElement(); //description
			$this->xml->startElement('content:encoded');
			$this->xml->writeCData($row->body);
			$this->xml->endElement(); //content:encoded
			$this->xml->endElement(); //item
		}
		$this->xml->endElement(); //channel
		$this->xml->endElement(); //rss
		$this->end();
	}
	/**
	 * RDF
	 */
	public function actionRdf() {
		$this->xml->startElement('rdf:RDF');
		$this->xml->writeAttribute('xmlns', 'http://purl.org/rss/1.0/');
		$this->xml->writeAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns');
		$this->xml->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
		$this->xml->writeAttribute('xmlns:sy', 'http://purl.org/rss/1.0/modules/syndication/');
		$this->xml->writeAttribute('xmlns:admin', 'http://webns.net/mvcb/');
		$this->xml->writeAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
		$last = ArticleModel::getList(['limit' => '0,1'])->next();
		$this->xml->startElement('channel');
		$this->xml->writeAttribute('rdf:about', Router::createUrl('index/feed/rss1'));
		$this->xml->writeElement('title', Common::option('sitename'));
		$this->xml->writeElement('link', Router::createUrl());
		$this->xml->writeElement('description', Html::encode(Common::option('description')));
		$last = ArticleModel::getList(['limit' => '0,1'])->next();
		$this->xml->writeElement('dc:date', $last->getDate('c'));
		$this->xml->startElement('admin:generatorAgent');
		$this->xml->writeAttribute('rdf:resource', 'http://blog.sylingd.com/?from=syblog_' . Common::VERSION);
		$this->xml->endElement(); //admin:generatorAgent
		$this->xml->startElement('items');
		$this->xml->startElement('rdf:Seq');
		$article = ArticleModel::getList(['body' => TRUE, 'limit' => '0,' . Common::option('pagesize')]);
		while ($row = $article->next()) {
			$this->xml->startElement('rdf:li');
			$this->xml->writeAttribute('rdf:resource', Router::createUrl(['index/article/view', 'id' => $row->id]));
			$this->xml->endElement();
		}
		$this->xml->endElement(); //rdf:Seq
		$this->xml->endElement(); //items
		$this->xml->endElement(); //channel
		$article->reset();
		while ($row = $article->next()) {
			$this->xml->startElement('item');
			$this->xml->writeAttribute('rdf:about', Router::createUrl(['index/article/view', 'id' => $row->id]));
			$this->xml->writeElement('link', Router::createUrl(['index/article/view', 'id' => $row->id]));
			$this->xml->writeElement('dc:date', $row->getDate('c'));
			$this->xml->startElement('description');
			ob_start();
			$row->excerpt(100, '...');
			$excerpt = ob_get_clean();
			$this->xml->writeCData($excerpt);
			$this->xml->endElement(); //description
			$this->xml->startElement('content:encoded');
			$this->xml->writeCData($row->body);
			$this->xml->endElement(); //content:encoded
			$this->xml->endElement(); //item
		}
		$this->xml->endElement(); //rdf:RDF
		$this->end();
	}
	/**
	 * Atom
	 */
	public function actionAtom() {
		$this->xml->startElement('feed');
		$this->xml->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
		$this->xml->writeAttribute('xmlns:thr', 'http://purl.org/syndication/thread/1.0');
		$this->xml->writeAttribute('xml:lang', 'zh-CN');
		$this->xml->writeAttribute('xml:base', Router::createUrl('index/feed/atom', 'xml'));
		$this->xml->startElement('title');
		$this->xml->writeAttribute('type', 'text');
		$this->xml->text(Common::option('sitename'));
		$this->xml->endElement(); //title
		$this->xml->startElement('subtitle');
		$this->xml->writeAttribute('type', 'text');
		$this->xml->text(Common::option('description'));
		$this->xml->endElement(); //subtitle
		$last = ArticleModel::getList(['limit' => '0,1'])->next();
		$this->xml->writeElement('updated', $last->getDate('c'));
		$this->xml->startElement('link');
		$this->xml->writeAttribute('rel', Sy::getMimeType('html'));
		$this->xml->writeAttribute('href', Router::createUrl());
		$this->xml->endElement(); //link
		$this->xml->writeElement('id', Router::createUrl('index/feed/atom', 'xml'));
		$this->xml->startElement('link');
		$this->xml->writeAttribute('rel', 'self');
		$this->xml->writeAttribute('type', Sy::getMimeType('atom'));
		$this->xml->writeAttribute('href', Router::createUrl('index/feed/atom', 'xml'));
		$this->xml->endElement(); //link
		$this->xml->startElement('generator');
		$this->xml->writeAttribute('url', 'http://blog.sylingd.com');
		$this->xml->writeAttribute('version', Common::VERSION);
		$this->xml->text('SYBlog');
		$this->xml->endElement(); //generator
		$article = ArticleModel::getList(['body' => TRUE, 'limit' => '0,' . Common::option('pagesize')]);
		while ($row = $article->next()) {
			$this->xml->startElement('entry');
			$this->xml->startElement('author');
			$this->xml->writeElement('name', Common::option('author'));
			$this->xml->endElement(); //author
			$this->xml->startElement('title');
			$this->xml->writeAttribute('type', Sy::getMimeType('html'));
			$this->xml->writeCData($row->title);
			$this->xml->endElement(); //title
			$this->xml->startElement('link');
			$this->xml->writeAttribute('rel', 'alternate');
			$this->xml->writeAttribute('type', Sy::getMimeType('html'));
			$this->xml->writeAttribute('href',  Router::createUrl(['index/article/view', 'id' => $row->id]));
			$this->xml->endElement(); //link
			$this->xml->writeElement('id', Router::createUrl(['index/article/view', 'id' => $row->id]));
			$this->xml->writeElement('updated', $row->getDate('c'));
			$this->xml->writeElement('published', date('c', $row->publish));
			$this->xml->startElement('summary');
			$this->xml->writeAttribute('type', 'html');
			ob_start();
			$row->excerpt(100, '...');
			$excerpt = ob_get_clean();
			$this->xml->writeCData($excerpt);
			$this->xml->endElement(); //summary
			$this->xml->startElement('content');
			$this->xml->writeAttribute('type', 'html');
			$this->xml->writeCData($row->body);
			$this->xml->endElement(); //content
			$this->xml->endElement(); //entry
		}
		$this->xml->endElement(); //feed
		$this->end();
	}
}
