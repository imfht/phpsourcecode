<?php

/**
 * Description of collect
 *
 * @author joe
 */
include_once 'phpQuery.php';

class Query {

    private $html;
    private $book;
    private $site;

    public function __construct($site = null) {
        if ($site) {
            $this->site($site);
        }
    }

    function site($site) {
        $this->site = $site;
    }

    function bookInfo($book_id=0) {
        $this->html = phpQuery::newDocumentFile($this->site['book_url']);
        $this->book['book_title']  = $this->filter(get_encoding(pq($this->html)->find($this->site['book_title'])->text()));
        $this->book['book_img']    = pq($this->html)->find($this->site['book_img'])->attr('src');
        if (!preg_match('/^((http|https):\/\/)?[\w-_\.]+(\/[\w-_:\.\[\]]+)*\/?$/',$this->book['book_img'])) {
                $this->book['book_img'] = $this->site['site_url'] . $this->book['book_img'];
        }
        $this->book['book_author'] = $this->filter(get_encoding(pq($this->html)->find($this->site['book_author'])->text()));
        $this->book['book_desc']   = get_encoding(pq($this->html)->find($this->site['book_desc'])->text());
        //解析图书列表地址
        if (preg_match('/^((http|https):\/\/)?[\w-_\.]+(\/[\w-_:\.\[\]]+)*\/?$/', $this->site['book_list'])) {
            if (preg_match('/\[(\d+)\]/',$this->site['book_list'],$match)) {
                $this->book['book_list'] = preg_replace('/(:book_id\[(\d+)\])/', substr($book_id,0,(int)$match[1]), $this->site['book_list']);
                $this->book['book_list'] = preg_replace('/(:book_id)/', $book_id, $this->book['book_list']);
            } else {
                $this->book['book_list'] = preg_replace('/(:book_id)/', $book_id, $this->site['book_list']);
            }
        } else {
            $this->book['book_list'] = pq($this->html)->find($this->site['book_list'])->attr('href');
            if (!preg_match('/^((http|https):\/\/)?[\w-_\.]+(\/[\w-_:\.\[\]]+)*\/?$/',$this->book['book_list'])) {
                $this->book['book_list']=$this->site['site_url'].$this->book['book_list'];
            }
        }

        //解析章节地址
        $this->book['chapter_url']=str_ireplace(':site_url',$this->site['site_url'],$this->site['chapter_url']);
        $this->book['chapter_url']=str_ireplace(':book_url',$this->book['book_list'],$this->book['chapter_url']);
        $this->book['chapter_url']=str_ireplace('index.html','',$this->book['chapter_url']);
        $this->book['chapter_url']=str_ireplace('index.htm','',$this->book['chapter_url']);
        $this->book['chapter_url']=str_ireplace('index.php','',$this->book['chapter_url']);

        phpQuery::$documents = array();
        return $this->book;
    }

    function chapterList($url) {
        $html = phpQuery::newDocumentFile($url);
        $list = array();

        foreach (pq($html)->find($this->site['chapter_list']) as $lists) {
            if (pq($lists)->attr('href') != '42506963.html') {
                $list[] = array(
                    'url'   => pq($lists)->attr('href'),
                    'title' => pq($lists)->text()
                );
            }
        }

        phpQuery::$documents = array();
        return $list;
    }

    function chapter($url) {
        $chapter_html = phpQuery::newDocumentFile($url);
        $chapter = get_encoding(pq($chapter_html)->find($this->site['chapter_content'])->html());
        phpQuery::$documents = array();
        return $chapter;
    }

    private function filter($content) {
        $pattern = "/(全集|全文阅读|下载|作\s?者|：|:)/i";
        return preg_replace($pattern,'',$content);
    }

}