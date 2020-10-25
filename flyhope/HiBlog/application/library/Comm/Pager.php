<?php

/**
 * 分页类
 *
 * @package Comm
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Comm;
class Pager {

    /**
     * 数据总数
     * 
     * @var int
     */
    public $total = 0;
    
    /**
     * 第几页
     * 
     * @var int
     */
    public $page = 1;
    
    /**
     * 每页多少项
     * 
     * @var int
     */
    public $count = 10;
    
    /**
     * 当前请求时最后一个ID
     * 
     * @var int
     */
    public $next_since_id;
    
    /**
     * 当前请求时第一个ID
     * 
     * @var int
     */
    public $prev_since_id;
    
    /**
     * 上一次请求的页面
     * 
     * @var int
     */
    public $last_page;
    
    /**
     * 最多可选择页数
     * 
     * @var int
     */
    public $page_total = 1;
    
    /**
     * 链接回调
     * 
     * @var closure
     */
    public $link_callback = null;
    
    /**
     * 构造方法
     * 
     * @param int $total         总数
     * @param int $page          第几页
     * @param int $count         每页多少项
     * @param int $last_page     上一次请页的页面
     * @param int $next_since_id 当前请求最后一个ID
     * @param int $prev_since_id 当前请求第一个ID
     */
    public function __construct($total, $count = 20, $page = false, $last_page = false, $next_since_id = false, $prev_since_id = false) {
        $this->total = $total;
        $this->page = $page;
        $this->last_page = $last_page;
        $this->count = $count;
        $this->next_since_id = $next_since_id;
        $this->prev_since_id = $prev_since_id;
        
        if($this->page === false) {
            $this->page = Arg::get('p', FILTER_VALIDATE_INT, ['min_range' => 1]) ?: 1;
        }
        if($this->next_since_id === false) {
            $this->next_since_id = Arg::get('next_since_id', FILTER_VALIDATE_INT) ?: 0;
        }
        if($this->prev_since_id === false) {
            $this->next_since_id = Arg::get('prev_since_id', FILTER_VALIDATE_INT) ?: 0;
        }
        if(!$this->last_page === false) {
            $this->last_page = Arg::get('last_page', FILTER_VALIDATE_INT, ['min_range' => 1]) ?: 0;
        }
        
        $this->page_total = ceil($this->total / $this->count);
    }
    
    /**
     * 获取基础URL
     * 
     * @return string
     */
    public function showBaseHref() {
        static $response = null;
        if($response === null) {
            $result = $_GET;
            if(isset($result['last_page'])) {
                unset($result['last_page']);
            }
            if(isset($result['next_since_id'])) {
                unset($result['next_since_id']);
            }
            if(isset($result['prev_since_id'])) {
                unset($result['prev_since_id']);
            }
            if(isset($result['p'])) {
                unset($result['p']);
            }
            $response = '?' . http_build_query($result);
        }
        
        return $response;
    }
    
    /**
     * 获取链接
     * 
     * @param int $page 第几页
     * 
     * @return string
     */
    public function showHref($page) {
        if(is_callable($this->link_callback)) {
            $result = call_user_func($this->link_callback, $page);
        } else {
            $page = (int)$page;
            $base_href = $this->showBaseHref();
            $this->showBaseHref() !== '?' && $base_href .= '&';
            $result .= "p={$page}";
        }

        return $result;
    }
}
