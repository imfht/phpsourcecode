<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Pagination;
use Tang\I18n\II18n;

/**
 * 分页实现
 * Class Paginator
 * @package Tang\Pagination
 */
class Paginator implements IPaginator
{
    private $total = 0;
    private $pageNumber = 20;
    private $maxPage = 0;
    private $nowPage = 0;
    /**
     * 语言包
     * @var II18n
     */
    private $II18n;
    public function setl18n(II18n $II18n)
    {
        $this->II18n = $II18n;
    }
    /**
     * 设置总量
     * @param $total
     * @return mixed
     */
    public function setTotal($total)
    {
        $total = (int) $total;
        $total < 0 && $total = 0;
        $this->total = $total;
    }

    /**
     * 设置每页数量
     * @param $pageNumber
     * @return mixed
     */
    public function setPageNumber($pageNumber)
    {
        $pageNumber = (int)$pageNumber;
        $pageNumber < 1 && $pageNumber = 20;
        $this->pageNumber = $pageNumber;
    }

    /**
     * 获取每页数量
     * @return int
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * 获取最大页数
     * @return int
     */
    public function getMaxPage()
    {
        return $this->maxPage;
    }

    /**
     * 获取当前页
     * @return int
     */
    public function getNowPage()
    {
        return $this->nowPage;
    }

    /**
     * 获取分页数组
     * @param int $nowPage 当前页
     * @param int $total 总数量
     * @param int $pageNumber 一页数量
     * @throws NowPageLtMaxPageException
     * @return array
     */
    public function getPages($nowPage,$total = 0,$pageNumber = 0)
    {
        if ($total)
        {
            $this->setTotal($total);
        }
        if ($pageNumber)
        {
            $this->setPageNumber($pageNumber);
        }
        $nowPage = (int) $nowPage;
        $nowPage < 1 && $nowPage = 1;
        $this->nowPage = $nowPage;
        $maxPage = $this->maxPage = ceil($this->total / $this->pageNumber);
        if (!$maxPage)
        {
            return false;
        }
        if ($nowPage > $maxPage)
        {
            throw new NowPageLtMaxPageException('No more pages!',null,50013);
        }
        $pages = array();
        $pages[] = array('name' => $this->II18n->get('First page'),'page'=>1);
        if ($nowPage > 1)
        {
            $pages[] = array('name' => $this->II18n->get('Prev page'),'page'=>$nowPage-1);
        }
        if ($nowPage > 5)
        {
            for ($i = $nowPage - 5; $i < $nowPage; $i ++)
            {
                if ($i <= 0)
                    continue;
                $pages[] = array('name' => $i,'page'=>$i);
            }
            for ($i = 0; $i < 5; $i ++)
            {
                $p = $nowPage + $i;
                if ($p > $maxPage)
                {
                    break;
                }
                $pages[] = array('name' => $p,'page'=>$p);
            }
        } else
        {
            for ($i = 1; $i < 10; $i ++)
            {
                if ($i > $maxPage)
                {
                    break;
                }
                $pages[] = array('name' => $i,'page'=>$i);
            }
        }
        $nextPage = $nowPage + 1;
        if ($nowPage < $maxPage)
        {
            $pages[] = array('name' => $this->II18n->get('Next page'),'page'=>$nextPage);
        }
        $pages[] = array('name' => $this->II18n->get('End page'),'page'=>$maxPage);
        return $pages;
    }
}