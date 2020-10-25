<?php
// +----------------------------------------------------------------------
// | Loadphp Framework designed by www.loadphp.com
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.loadphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 亓斌 <qibin0506@gmail.com>
// +----------------------------------------------------------------------

/**
 +------------------------------------------------------------------------------
 * 分页类
 +------------------------------------------------------------------------------
 */
class Page {
    private $pagesize;      //每页显示条数
    private $pageval;       //当前页
    private $url;           //请求URL
    private $listNum;       //数字列表个数
    private $limit;         //limit SQL语句
    private $numq;          //共多少条
    private $pageTotal;     //共多少页
    
    public function __construct($pagesize,$numq) {
        $this->pagesize = $pagesize;
        $this->pageval = isset($_GET['page']) ? $_GET['page'] : 1;
        $this->url = $this->setUrl();
        $this->listNum = 5;
        $this->limit = $this->setLimit();
        $this->numq = $numq;
        $this->pageTotal = ceil($this->numq/$this->pagesize);
    }
    
    private function setUrl() {
        $url = rtrim($_SERVER['REQUEST_URI'],'/');
        
        if(empty($_SERVER['PATH_INFO'])) {
            $url .= (preg_match("/\b\.php\b$/i",$url) ? '' : '/index.php')."/Index/action";
        }else {
            $info = trim($_SERVER['PATH_INFO'],'/');
            $infoArr = explode('/',$info);
            $url .=  empty($infoArr[1]) ? '/action' : '';
        }

        $url = rtrim($url,'/');
        $url = str_replace("/page/".$this->pageval,"",$url);
        return $url;
    }
    
    private function setLimit() {
        return " limit ".($this->pageval-1)*$this->pagesize.",".$this->pagesize;
    }
    
    public function __get($args) {
        if($args == "limit") return $this->limit;
        else return;
    }
    private function first() {
        if($this->pageval>1)
            return " <a href={$this->url}/page/1><<</a> ";
    }
    
    private function prev() {
        if($this->pageval>1)
            return " <a href={$this->url}/page/".($this->pageval-1)."><</a> ";
    }
    
    private function next() {
        if($this->pageval<$this->pageTotal)
            return " <a href={$this->url}/page/".($this->pageval+1).">></a> ";
    }
    private function last() {
        if($this->pageval<$this->pageTotal)
            return " <a href={$this->url}/page/{$this->pageTotal}>>></a> ";
    }
    
    private function pageList() {
        $pagelist = "";
        $val = floor($this->listNum/2);
        for($i=$val;$i>=1;$i--) {
            $page = $this->pageval - $i;
            if($page<1) continue;
            $pagelist .= "<a href={$this->url}/page/{$page}>{$page}</a> ";
        }
        $pagelist .= " ".$this->pageval." ";
        for($i=1;$i<=$val;$i++) {
            $page = $this->pageval + $i;
            if($page>$this->pageTotal) break;
            $pagelist .= "<a href={$this->url}/page/{$page}>{$page}</a> ";
        }
        return $pagelist;
    }
    
    public function showPage() {
        $html = '';
        $html .= $this->first();
        $html .= $this->prev();
        $html .= $this->pageList();
        $html .= $this->next();
        $html .= $this->last();
        $html .= "(".$this->pageval."/".$this->pageTotal."页&nbsp;";
        $html .= "共".$this->numq."条)&nbsp;";
        return $html;
    }
}
?>