<?php

/**
 * 记录用户访问历史，方便用户回退到上一页
 * HistoryStack.php
 * @author: liyongsheng
 * @email： liyongsheng@huimai365.com
 * @date: 2015/6/25
*/

namespace framework\libraries;

use framework\session\Session;

/**
 * Class HistoryStack
 * @package framework\libraries
 */
class HistoryStack
{
    /** @var  \SplStack $_stack */
    private $_stack;

    /** @var int 栈的最大长度 */
    private $maximize = 20;

    /** @var  Session */
    private $session;

    public function __construct()
    {
        $this->session = new Session();
        $this->_stack = unserialize($this->session->get('historyStack'));
        if(empty($this->_stack) || !($this->_stack instanceof \SplStack)){
            $this->_stack = new \SplStack();
        }
    }

    /**
     * 记录访问历史
     * @param $currentUrl
     * @param $fromUrl
     */
    public function pushPop($currentUrl, $fromUrl)
    {
        /** 用户中间重新打开标签手动输入网址进入 */
        if(empty($fromUrl)){
            $this->_stack = new \SplStack();
            $this->_saveStack();
            return;
        }
        $lastFromUrl = $this->getLastFromUrl();
        if($lastFromUrl == $fromUrl){
            return ;
        }
        /**
         * 当前url和最后一次来源相同说明是回退
         */
        if($lastFromUrl == $currentUrl){
            $this->_stack->pop();
        }else{
            $this->_stack->push($fromUrl);
        }
        /**
         * 超出最大记录条数弹出最早的记录
         */
        if($this->_stack->count() > $this->maximize){
            $this->_stack->shift();
        }
        $this->_saveStack();
    }

    /**
     * 获取最后一次来源url
     * @return mixed
     */
    public function getLastFromUrl()
    {
        if($this->_stack->isEmpty()) {
            return null;
        }else{
            return $this->_stack->top();
        }
    }

    /**
     * 获取全部的历史
     */
    public function getAllHistory()
    {
        return $this->_stack;
    }
    /**
     * 保存访问历史数据到session
     */
    private function _saveStack()
    {
        $this->session->set('historyStack', serialize($this->_stack));
    }
}