<?php
// FileName: Pager.class.php
// 分页类，这个类仅仅用于处理数据结构，不负责处理显示的工作
Class Pager
{
    var $PageSize;             //每页的数量
    var $CurrentPageID;        //当前的页数
    var $NextPageID;           //下一页
    var $PreviousPageID;       //上一页
    var $numPages;             //总页数
    var $numItems;             //总记录数
    var $isFirstPage;          //是否第一页
    var $isLastPage;           //是否最后一页
    var $sql;                  //sql查询语句

    function Pager($option)
    {
        global $db;
        $this->_setOptions($option);
        // 总条数
        if ( !isset($this->numItems) )
        {
            $res = $db->query($this->sql);
            $this->numItems = $res->numRows();
        }
        // 总页数
        if ( $this->numItems > 0 )
        {
            if ( $this->numItems < $this->PageSize ){ $this->numPages = 1; }
            if ( $this->numItems % $this->PageSize )
            {
                $this->numPages= (int)($this->numItems / $this->PageSize) + 1;
            }
            else
            {
                $this->numPages = $this->numItems / $this->PageSize;
            }
        }
        else
        {
            $this->numPages = 0;
        }

        switch ( $this->CurrentPageID )
        {
            case $this->numPages == 1:
                $this->isFirstPage = true;
                $this->isLastPage = true;
                break;
            case 1:
                $this->isFirstPage = true;
                $this->isLastPage = false;
                break;
            case $this->numPages:
                $this->isFirstPage = false;
                $this->isLastPage = true;
                break;
            default:
                $this->isFirstPage = false;
                $this->isLastPage = false;
        }

        if ( $this->numPages > 1 )
        {
            if ( !$this->isLastPage ) { $this->NextPageID = $this->CurrentPageID + 1; }
            if ( !$this->isFirstPage ) { $this->PreviousPageID = $this->CurrentPageID - 1; }
        }

        return true;
    }
    /***
     *
     * 返回结果集的数据库连接
     * 在结果集比较大的时候可以直接使用这个方法获得数据库连接，然后在类之外遍历，这样开销较小
     * 如果结果集不是很大，可以直接使用getPageData的方式获取二维数组格式的结果
     * getPageData方法也是调用本方法来获取结果的
     *
     ***/

    function getDataLink()
    {
        if ( $this->numItems )
        {
            global $db;

            $PageID = $this->CurrentPageID;

            $from = ($PageID - 1)*$this->PageSize;
            $count = $this->PageSize;
            $link = $db->limitQuery($this->sql, $from, $count);   //使用Pear DB::limitQuery方法保证数据库兼容性

            return $link;
        }
        else
        {
            return false;
        }
    }

    /***
     *
     * 以二维数组的格式返回结果集
     *
     ***/

    function getPageData()
    {
        if ( $this->numItems )
        {
            if ( $res = $this->getDataLink() )
            {
                if ( $res->numRows() )
                {
                    while ( $row = $res->fetchRow() )
                    {
                        $result[] = $row;
                    }
                }
                else
                {
                    $result = array();
                }

                return $result;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function _setOptions($option)
    {
        $allow_options = array(
            'PageSize',
            'CurrentPageID',
            'sql',
            'numItems'
        );

        foreach ( $option as $key => $value )
        {
            if ( in_array($key, $allow_options) && ($value != null) )
            {
                $this->$key = $value;
            }
        }

        return true;
    }
}
?>