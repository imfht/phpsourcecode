<?php
/**
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-1-20
 * $urlrule url规则 
 * example 1 /news/lists-category-23-page-{$page}.html 
 * 2 index.php?m=news&c=lists&category=23&page={$page}
 */
namespace framework\libraries;

class Page
{
    private $_config = array(
                'total' =>0,        //总条数
                'pageNum' =>8,      //每组显示页数
                'listNum' =>20,     //每页显示条数
                'current_page'=>null,  //当前页
                'urlrule'=>'',      //url规则
                'maxpage'=>0,       //最大显示页数 为0时 全部显示
                'page_model'=>'normal', //分页模式 full normal mini
                'is_ajax'=>false,    //是否为ajax 分页
                'current_style'=>'current', //当前页样式名
                'other_style'=>'',     //其他页样式
            );
    private $total_pages= 0;       //总页数
    //private $groupNum   = 0;       //页面组数
    public  $offset     = 0;       //当前页第一条 的序列号
    /**
     * 构造函数
     */
    function __construct($config =array()){
        $this->_config =array_merge($this->_config,$config);
    }
    public function __get($name){
        if(isset($this->_config[$name]))
            return $this->_config[$name];
        return null;
    }
    public function __set($name,$value){
        if(isset($this->_config[$name]))
            $this->_config[$name] = $value;
    }
    public function __isset($name){
        return $this->_config[$name];
    }
    /**
     * 生成分页 html
     * @return  string html
     */
    public  function pageHtml(){
        $this->total_pages  = ceil($this->total/$this->listNum);
        $this->current_page = max($this->current_page,1);
        $this->offset       = ($this->current_page-1)*$this->listNum;
        if($this->total_pages<2) return;
        //设置最大页数
        if($this->maxpage){
           $this->total_pages =  min($this->total_pages,$this->maxpage); 
        }
        $current_grouppage=ceil($this->current_page/$this->pageNum); //当前分组
        
        $multipage='';
        $multipage .=$this->page_model=='mini'?'':'<a class="'.$this->other_style.'">共'.$this->total.'条数据</a>';
        if($this->current_page==1 && $this->page_model=='full'){
            $multipage .= '<a class="'.$this->other_style.'">首页</a>';
            $multipage .= '<a class="'.$this->other_style.'">上一页</a>';
        }elseif($this->current_page>1){
            $multipage .= $this->page_model=='full'?'<a class="'.$this->other_style.'" href="'.$this->pageurl($this->urlrule, 1).'" pageNum="1">首页</a>':'';
            $multipage .= '<a class="'.$this->other_style.'" href="'.$this->pageurl($this->urlrule, $this->current_page-1).'" pageNum="'.($this->current_page-1).'">上一页</a>';
        }
        if($this->page_model!='mini'){
            for($i=1;$i<=$this->pageNum;$i++){
                 $page =($current_grouppage-1)*$this->pageNum+$i;
                 if($page==$this->current_page){
                    if($this->total_pages==1)break;
                    $multipage .='<a class="'.$this->current_style.'">'.$page.'</a>';
                 }else{
                    if($page>$this->total_pages){
                        break;
                    }
                    $multipage .= '<a class="'.$this->other_style.'" href="'.$this->pageurl($this->urlrule, $page).'" pageNum="'.$page.'">'.$page.'</a>';
                 }
            }
        }
        if($this->current_page>=$this->total_pages && $this->page_model=='full'){
            $multipage .= '<a class="'.$this->other_style.'">下一页</a>';
            $multipage .= '<a class="'.$this->other_style.'">尾页</a>';
        }elseif($this->current_page<$this->total_pages){
            $multipage .= '<a class="'.$this->other_style.'" href="'.$this->pageurl($this->urlrule, $this->current_page+1).'" pageNum="'.($this->current_page+1).'">下一页</a>';
            $multipage .= $this->page_model=='full'?'<a class="'.$this->other_style.'" href="'.$this->pageurl($this->urlrule, $this->total_pages).'" pageNum="'.$this->total_pages.'">尾页</a>':'';
        }
        return $multipage;
    }

    /**
     * 生成页 url 连接
     * @param string $urlrule url 规则
     * @param int $page 页数
     * @param string $par 页数跳转时要带的参数
     * @return mixed|string
     */
     private function pageUrl($urlrule, $page, $par='page={$page}'){
        //ajax 分页
        if($this->is_ajax)
            return 'javascript:void(0);';
            
        //为传递 url 规则    
        if(!$urlrule){
            $urlrule =  $_SERVER['REQUEST_URI'];
            $pos = strpos($urlrule,'?');
            if(!$pos){
                $urlrule .='?'.$par;
            }else{
                $parse = parse_url($urlrule);
                parse_str($parse['query'],$params);
                unset($params['page']);
                if(count($params)==0){
                    $urlrule =$parse['path'].'?'.$par;
                }else{
                    $urlrule =$parse['path'].'?'.http_build_query($params).'&'.$par;
                }  
            }
        }
        $find_tag     = '{$page}';
        $replace_value = $page;
        $url = str_replace($find_tag,$replace_value,$urlrule);
        return $url;
    }
}