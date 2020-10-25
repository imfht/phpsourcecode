<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Page
 *
 * @author Administrator
 */
class PageTool {
    //分页大小
    private $page_size = 20;
    //分页总数
    private $page_total = 0;
    //当前分页
    private $page_num = 0;
    //开始读取的记录位置
    private $start_row = 0;
    //结束读取的记录位置
    private $end_row = 0;
    //总记录数
    private $page_row = 0;
    //当前分页
    private $page_now = 0;


    public function __construct($row,$size){
        $this->page_size = $size;
        $this->page_row = $row;
        $this->page_total = ceil($this->page_row/$this->page_size);
    }
    
    public function show($page_now){
        $this->page_now = $page_now;
        $page_str = '<div id="page">';
        if($this->page_now > 1){
            $page_str .= '<a href="">首页</a><a href="">上一页</a>';
        }
        
        //前5个分页数字按钮
        $prev = $this->page_now;
        $prev_i = 5;
        while ($this->page_now > 1 ){
            if($prev_i  >= 5){
                break;
            }
            
            --$prev;
            --$prev_i;
            if($this->page_now - $prev < 0){
                continue;
            }
            
            $page_str .= '<a href="" >'. ($this->page_now -  (5 - $prev_i)) .'</a>';  
        }
        
         //当前分类按钮
         $page_str .= '<a href="" style="background:#1A64A1;color:#fff;">'.$this->page_now.'</a>';
         
         //后5个分页数字按钮
         $next = 1;
         while (($this->page_now < $this->page_total) && (($this->page_now + $next) <= $this->page_total)){
             if($next  > 5){
                break;
            }
            $page_str .= '<a href="" >'. ($this->page_now + $next) .'</a>';
            ++$next;
         }
        
        if($this->page_now < $this->page_total){
            $page_str .= '<a href="">下一页</a><a href="">尾页</a>';
        }
        
        $page_str .= '</div>';
        return $page_str;
    }
}
