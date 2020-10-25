<?php
/**
 * 翻页
 * @author user
 *
 */
class Paginator {
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter;
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow;
    // 分页总页面数
    protected $totalPages;
    // 总行数
    protected $totalRows;
    // 当前页数
    protected $nowPage;
    // 分页的栏的总页数
    protected $coolPages;
    // 分页显示定制
    protected $config = array(
        'ha'=> '共',
        'header'=> '条',
        'prev'=> '上一页',
        'next'=> '下一页',
        'first'=> '第一页',
        'last'=> '最后一页',
        'theme'=> ' %upPage%  %first%  %prePage%  %linkPage%  %downPage% %nextPage% %end%' 
    );
    // 'theme'=> ' %upPage% %first% %prePage% %linkPage% %downPage% %nextPage% %ha% %totalRow% %header% %end%'
    
    // 默认分页变量名
    protected $varPage = 'page';
    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows = '', $parameter = ''){
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        if(!empty($listRows)){
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows / $this->listRows); // 总页数
        $this->coolPages = ceil($this->totalPages / $this->rollPage);
        $tmp = isset($_GET[$this->varPage]) ? intval($_GET[$this->varPage]) : 1;
        if(!$tmp) $tmp = 1;
        $this->nowPage = !empty($_GET[$this->varPage]) && $tmp ? $tmp : 1;
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages){
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows * ($this->nowPage - 1);
    }
    public function setConfig($name, $value){
        if(isset($this->config[$name])){
            $this->config[$name] = $value;
        }
    }
    /**
     * 分页显示输出
     * @access public
     */
    public function show(){
        // if(0 == $this->totalRows) return '';
        $p = $this->varPage;
        $nowCoolPage = ceil($this->nowPage / $this->rollPage);
        $url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : "?") . $this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])){
            parse_str($parse['query'], $params);
            unset($params[$p]);
            $url = $parse['path'] . '?' . http_build_query($params);
        }
        // 上下翻页字符串
        $upRow = $this->nowPage - 1;
        $downRow = $this->nowPage + 1;
        if($upRow > 0){
            $upPage = "<li><a data-url='{$url}&{$p}={$upRow}' href='javascript:void(0)'  data-pageid='this' data-navname='this' class='page_btn'>{$this->config['prev']}</a></li>";
        }else{
            $upPage = "";
        }
        if($downRow <= $this->totalPages){
            $downPage = "<li><a data-url='{$url}&{$p}={$downRow}' href='javascript:void(0)'  data-pageid='this' data-navname='this' class='page_btn'>{$this->config['next']}</a></li>";
        }else{
            $downPage = "";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i = 1;$i <= $this->rollPage;$i++){
            $page = ($nowCoolPage - 1) * $this->rollPage + $i;
            if($page != $this->nowPage){
                if($page <= $this->totalPages){
                    $linkPage .= "<li><a data-url='{$url}&{$p}={$page}' href='javascript:void(0)'  data-pageid='this' data-navname='this' class='page_btn'>&nbsp;{$page}&nbsp;</a></li>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= "<li class='active'><a>" . $page . "</a></li>";
                }
            }
        }
        $pageStr = str_replace(array(
            '%ha%',
            '%header%',
            '%nowPage%',
            '%totalRow%',
            '%totalPage%',
            '%upPage%',
            '%downPage%',
            '%first%',
            '%prePage%',
            '%linkPage%',
            '%nextPage%',
            '%end%' 
        ), array(
            $this->config['ha'],
            $this->config['header'],
            $this->nowPage,
            $this->totalRows,
            $this->totalPages,
            $upPage,
            $downPage,
            $linkPage 
        ), $this->config['theme']);
        return array(
            'html'=> $pageStr,
            'totalRow'=> $this->totalRows 
        );
    }
}