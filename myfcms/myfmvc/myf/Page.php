<?php

/*
 *  @author myf
 *  @date 2014-11-15 15:38:09
 *  @Description myfmvc 分页类
 *  @web http://www.minyifei.cn
 */
namespace Myf\Mvc;

class Page {
    
    //每页显示记录数
    private $preCount;
    //总记录数
    private $totalCount;
    //当前页码
    private $currentPage;
    //每次显示的页数
    private $diplayPageNum;
    //总页数
    private $totalPageCount;
    //分页的链接
    private $pageLink;
    
    /**
     * 分页控件构造函数
     * @param int $preCount 每页显示记录数
     * @param int $totalCount 总的记录数
     * @param int $currentPage 当前是第几页
     * @param String $pageLink 包含%d的url模板,%d代表要替换的页码，默认是获取url增加p=%d参数
     * @param int $displayPageNum 显示的页码个数，默认10个
     */
    function __construct($preCount,$totalCount,$currentPage,$pageLink=null,$displayPageNum=10){
        $this->preCount = intval($preCount);
        $this->totalCount = intval($totalCount);
        $this->currentPage = intval($currentPage);
        $this->diplayPageNum = intval($displayPageNum);
        $this->totalPageCount = ceil($totalCount/$preCount);
        if(is_null($pageLink)){
            $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?");
            $parse = parse_url($url);
            if(isset($parse['query'])) {
                parse_str($parse['query'],$params);
                unset($params["p"]);
                $url   =  $parse['path'].'?'.http_build_query($params);
            }
            $this->pageLink = $url."&p=%d";
        }else{
            $this->pageLink = $pageLink;
        }
    }
    
    /**
     * 获取分页html代码 
     * 当前第1/453页 [首页] [上页] 1 2 3 4 5 6 7 8 9 10 [下页] [末页]
     * @return string 分页代码
     */
    public function show(){
        $html = "";
        $html.=sprintf("当前第%d/%d页",$this->currentPage,$this->totalPageCount);
        if($this->currentPage>1){
            $firstPageUrl = sprintf($this->pageLink,1);
            $prePageUrl = sprintf($this->pageLink, ($this->currentPage-1));
            $html.=sprintf("<a href='%s'>首页</a>", $firstPageUrl);
            $html.=sprintf("<a href='%s'>上一页</a>",$prePageUrl);
        }else{
            $html.="<span>首页</span>";
            $html.="<span>上一页</span>";
        }
        
        $ca = $this->calculateCurrentPageNum();
        for($i=0;$i<count($ca);$i++){
            $page = $ca[$i];
            if($page==$this->currentPage){
                $html.=sprintf("<span class='current'>%d</span>",$page);
            }else{
                $url = sprintf($this->pageLink,$page);
                $html.=sprintf("<a href='%s'>%d</a>",$url,$page);
            }
        }
        
        if($this->currentPage<$this->totalPageCount){
            $lastPageUrl = sprintf($this->pageLink,$this->totalPageCount);
            $nextPageUrl = sprintf($this->pageLink,($this->currentPage+1));
            $html.=sprintf("<a href='%s'>下一页</a>", $nextPageUrl);
            $html.=sprintf("<a href='%s'>末页</a>",$lastPageUrl);
        }else{
            $html.="<span>下一页</span>";
            $html.="<span>末页</span>";
        }
        return $html;
    }
    
    /**
     * 计算当前显示的页面
     * @return array
     */
    private function calculateCurrentPageNum(){
        $currentArray = array();
        if($this->totalPageCount<$this->diplayPageNum){
            for($i=0;$i<$this->totalPageCount;$i++){
                $currentArray[$i]=$i+1;
            }
        }else{
            $currentArray = $this->initCurrentPageArray();
            if($this->currentPage<=3){
                for($i=0;$i<count($currentArray);$i++){
                    $currentArray[$i]=$i+1;
                }
            }elseif($this->currentPage<=$this->totalPageCount && $this->currentPage>($this->totalPageCount-$this->diplayPageNum+1)){
                for($i=0;$i<count($currentArray);$i++){
                    $currentArray[$i]=$this->totalPageCount-$this->diplayPageNum+1+$i;
                }
            }else{
                for($i=0;$i<count($currentArray);$i++){
                    $currentArray[$i]=  $this->currentPage-2+$i;
                }
            }
        }
        return $currentArray;
    }
    private function initCurrentPageArray(){
        $currentArray = array();
        for($i=0;$i<$this->diplayPageNum;$i++){
            $currentArray[$i]=$i;
        }
        return $currentArray;
    }
    
}
