<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

use tfc\ap\ErrorException;

/**
 * Paginator class file
 * 分页处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Paginator.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Paginator
{
    /**
     * @var integer 每页默认展示的行数
     */
    const DEFAULT_LIST_ROWS = 10;

    /**
     * @var integer 每页默认展示的页码数
     */
    const DEFAULT_LIST_PAGES = 4;

    /**
     * @var string 默认的从$_GET或$_POST中获取当前页的键名
     */
    const DEFAULT_PAGE_VAR = 'paged';

    /**
     * @var integer|null 当前的页码
     */
    protected $_currPage = null;

    /**
     * @var integer 总的记录数
     */
    protected $_totalRows = 0;

    /**
     * @var string 从$_GET或$_POST中获取当前页的键名
     */
    protected $_pageVar = self::DEFAULT_PAGE_VAR;

    /**
     * @var string 链接地址的前半部分，拼接上页码就是完整的链接
     */
    protected $_url = '?&paged=';

    /**
     * @var integer 每页展示的行数
     */
    protected $_listRows = self::DEFAULT_LIST_ROWS;

    /**
     * @var integer 每页展示的页码数
     */
    protected $_listPages = self::DEFAULT_LIST_PAGES;

    /**
     * 构造方法：初始化总的记录数、链接地址的前半部分、从$_GET或$_POST中获取当前页的键名
     * @param integer $totalRows
     * @param string $url
     * @param string $pageVar
     */
    public function __construct($totalRows, $url = '?', $pageVar = self::DEFAULT_PAGE_VAR)
    {
        $this->setTotalRows($totalRows);
        $this->setUrl($url);
        $this->setPageVar($pageVar);
    }

    /**
     * 获取所有的分页参数，包括行信息、页码信息、链接信息
     * @return array
     */
    public function getItems()
    {
        $rows = $this->getRows();
        $pages = $this->getPages();
        $links = $this->getLinks($pages);

        return array('rows' => $rows, 'pages' => $pages, 'links' => $links);
    }

    /**
     * 根据页码信息，获取这些页码组成的链接
     * 当前页链接、首页链接、尾页链接、上一页链接、下一页链接、列表页链接
     * <ul>
     * <li>{@link $curr}</li>
     * <li>{@link $begin}</li>
     * <li>{@link $end}</li>
     * <li>{@link $prev}</li>
     * <li>{@link $next}</li>
     * <li>{@link $lists}</li>
     * </ul>
     * @param array $pages
     * @return array
     */
    public function getLinks(array $pages)
    {
        $links = array(
            'curr' => $this->getUrl($pages['curr']),
            'begin' => $this->getUrl($pages['begin']),
            'end' => $this->getUrl($pages['end']),
            'prev' => $this->getUrl($pages['prev']),
            'next' => $this->getUrl($pages['next']),
            'lists' => array()
        );

        for ($pageNo = $pages['first']; $pageNo <= $pages['last']; $pageNo++) {
            $links['lists'][$pageNo] = ($pageNo == $pages['curr']) ? $pageNo : $this->getUrl($pageNo);
        }

        return $links;
    }

    /**
     * 获取所有的行信息
     * 当前页开始的记录数、每页展示的行数、总的行数
     * <ul>
     * <li>{@link $first}</li>
     * <li>{@link $list}</li>
     * <li>{@link $total}</li>
     * </ul>
     * @return array
     */
    public function getRows()
    {
        return array(
            'first' => $this->getFirstRow(),
            'list' => $this->getListRows(),
            'total' => $this->getTotalRows()
        );
    }

    /**
     * 获取所有的页码信息
     * 当前页、每页展示的页码数、总页数、首页、尾页、上一页、下一页、第一个列表页码、最后一个列表页码
     * <ul>
     * <li>{@link $curr}</li>
     * <li>{@link $list}</li>
     * <li>{@link $total}</li>
     * <li>{@link $begin}</li>
     * <li>{@link $end}</li>
     * <li>{@link $prev}</li>
     * <li>{@link $next}</li>
     * <li>{@link $first}</li>
     * <li>{@link $last}</li>
     * </ul>
     * @return array
     */
    public function getPages()
    {
        $pages = array();
        $pages['curr'] = $this->getCurrPage();
        $pages['list'] = $this->getListPages();
        $pages['total'] = $this->getTotalPages();
        $pages['begin'] = 1;
        $pages['end'] = $pages['total'];
        $pages['prev'] = ($pages['curr'] > 1) ? $pages['curr'] - 1 : 1;
        $pages['next'] = ($pages['curr'] < $pages['end']) ? $pages['curr'] + 1 : $pages['end'];

        $pages['first'] = (int) ceil($pages['curr'] - $pages['list'] / 2);
        if ($pages['first'] < 1) {
            $pages['first'] = 1;
        }

        $pages['last'] = $pages['first'] + $pages['list'];
        if ($pages['last'] > $pages['end']) {
            $pages['last'] = $pages['end'];
        }

        $pages['first'] = $pages['last'] - $pages['list'];
        if ($pages['first'] < 1) {
            $pages['first'] = 1;
        }

        return $pages;
    }

    /**
     * 获取总页码数
     * @return integer
     */
    public function getTotalPages()
    {
        return (int) ceil($this->getTotalRows() / $this->getListRows());
    }

    /**
     * 获取SQL-LIMIT语句
     * @return string
     */
    public function getLimitStr()
    {
        return 'LIMIT ' . $this->getFirstRow() . ', ' . $this->getListRows();
    }

    /**
     * 获取当前页开始的记录数
     * @return integer
     */
    public function getFirstRow()
    {
        return ($this->getCurrPage() - 1) * $this->getListRows();
    }

    /**
     * 获取当前的页码
     * @return integer
     */
    public function getCurrPage()
    {
        if ($this->_currPage === null) {
            $this->setCurrPage();
        }

        return $this->_currPage;
    }

    /**
     * 设置当前的页码
     * @param integer|null $currPage
     * @return \tfc\util\Paginator
     */
    public function setCurrPage($currPage = null)
    {
        if ($currPage === null) {
            $pageVar = $this->getPageVar();
            $currPage = (isset($_GET[$pageVar])) ? $_GET[$pageVar] : ((isset($_POST[$pageVar])) ? $_POST[$pageVar] : 1);
        }

        if (($currPage = (int) $currPage) <= 0) {
            $currPage = 1;
        }

        $this->_currPage = $currPage;
        return $this;
    }

    /**
     * 获取每页展示的行数
     * @return integer
     */
    public function getListRows()
    {
        return $this->_listRows;
    }

    /**
     * 设置每页展示的行数
     * @param integer $listRows
     * @return \tfc\util\Paginator
     * @throws ErrorException 如果设置的行数小于或等于0，抛出异常
     */
    public function setListRows($listRows)
    {
        if (($listRows = (int) $listRows) <= 0) {
            throw new ErrorException(sprintf(
                'Paginator list rows "%d" can not less or equal than 0.', $listRows
            ));
        }

        $this->_listRows = $listRows;
        return $this;
    }

    /**
     * 获取每页展示的页码数
     * @return integer
     */
    public function getListPages()
    {
        return $this->_listPages;
    }

    /**
     * 设置每页展示的页码数
     * @param integer $listPages
     * @return \tfc\util\Paginator
     * @throws ErrorException 如果设置的页码数小于或等于0，抛出异常
     */
    public function setListPages($listPages)
    {
        if (($listPages = (int) $listPages) <= 0) {
            throw new ErrorException(sprintf(
                'Paginator list pages "%d" can not less or equal than 0.', $listPages
            ));
        }

        $this->_listPages = $listPages;
        return $this;
    }

    /**
     * 通过页码返回完整的链接
     * @param integer|null $pageNo
     * @return string
     */
    public function getUrl($pageNo = null)
    {
        if ($pageNo === null) {
             return $this->_url;
        }

        return $this->_url . '&' . $this->getPageVar() . '=' . (int) $pageNo;
    }

    /**
     * 设置链接地址的前半部分，拼接上页码就是完整的链接
     * @param string $url
     * @return \tfc\util\Paginator
     */
    public function setUrl($url)
    {
        $this->_url = trim((string) $url);
        return $this;
    }

    /**
     * 获取总的记录数
     * @return integer
     */
    public function getTotalRows()
    {
        return $this->_totalRows;
    }

    /**
     * 设置总的记录数
     * @param integer $totalRows
     * @return \tfc\util\Paginator
     * @throws ErrorException 如果设置的总记录数小于0，抛出异常
     */
    public function setTotalRows($totalRows)
    {
        if (($totalRows = (int) $totalRows) < 0) {
            throw new ErrorException(sprintf(
                'Paginator total rows "%d" can not less than 0.', $totalRows
            ));
        }

        $this->_totalRows = $totalRows;
        return $this;
    }

    /**
     * 获取从$_GET或$_POST中获取当前页的键名
     * @return string
     */
    public function getPageVar()
    {
        return $this->_pageVar;
    }

    /**
     * 设置从$_GET或$_POST中获取当前页的键名
     * @param string $pageVar
     * @return \tfc\util\Paginator
     * @throws ErrorException 如果设置的键名为空字符串，抛出异常
     */
    public function setPageVar($pageVar)
    {
        $pageVar = (string) $pageVar;
        if (($pageVar = trim($pageVar)) == '') {
            throw new ErrorException(
                'Paginator set page var failed, pageVar must be string and not empty.'
            );
        }

        $this->_pageVar = $pageVar;
        return $this;
    }
}
