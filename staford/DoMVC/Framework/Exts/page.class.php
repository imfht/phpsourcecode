<?php

/**
 * 分页类
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Page
{
    private $int_page_now; //当前页
    private $int_page_total; //总页数
    private $int_record_each; //每页显示记录数
    private $int_record_total; //总记录数
    private $str_page_link; //分页链接

    /**
     * 构造函数
     * @param int $page_now 当前页
     * @param int $record_each 每页显示记录数
     * @param int $record_total 总记录数
     * @param string $page_link 分页链接
     */
    public function __construct($page_now, $record_each, $record_total, $page_link)
    {
        $this->int_page_total = ceil($record_total / $record_each);

        //当前页数小于等于总页数，否则重置当前页数为末页页数
        $this->int_page_now = $page_now <= $this->int_page_total ? $page_now : $this->
            int_page_total;

        $this->int_record_each = $record_each + 0;
        $this->int_record_total = $record_total + 0;
        $this->str_page_link = trim($page_link);
    }

    /**
     * 获取记录数
     * @return string 共xxx条记录
     */
    public function get_Records_Total()
    {
        return '共' . $this->int_record_total . '条记录';
    }

    /**
     * 获取页数提示
     * @return string 当前第xxx/xxx页
     */
    public function get_Page_Info()
    {
        return '当前第' . $this->int_page_now . '/' . $this->int_page_total . '页';
    }

    /**
     * 获取首页链接
     * @param string $title 链接标题
     * @return string <a href="">标题</a>
     */
    public function get_First_Page($title)
    {
        return '<a href="' . $this->str_page_link . '1">' . trim($title) . '</a>';
    }

    /**
     * 获取尾页链接
     * @param string $title 链接标题
     * @return string <a href="">标题</a>
     */
    public function get_End_Page($title)
    {
        return '<a href="' . $this->str_page_link . $this->int_page_total . '">' . trim($title) .
            '</a>';
    }

    /**
     * 获取前一页链接
     * @param string $title 链接标题
     * @return string <a href="">标题</a>
     */
    public function get_Last_Page($title)
    {
        $page = $this->int_page_now - 1;
        if ($this->int_page_now < 2)
            $page = 1;
        return '<a href="' . $this->str_page_link . $page . '">' . trim($title) . '</a>';
    }

    /**
     * 获取后一页链接
     * @param string $title 链接标题
     * @return string <a href="">标题</a>
     */
    public function get_Next_Page($title)
    {
        $page = $this->int_page_now + 1;
        if ($page > $this->int_page_total)
            $page = $this->int_page_total;
        return '<a href="' . $this->str_page_link . $page . '">' . trim($title) . '</a>';
    }

    /**
     * 获取翻页导航链接列表
     * @param string $num 导航链接数量
     * @return string [1][2][3][4][5]
     */
    public function get_Page_List($num = 6)
    {
        $list = array();
        //如果展示页数小于一就返回假
        if ($num < 1)
            return false;

        //如果总页数小于展示页数，那么就赋值展示页数为总页数的值
        if ($this->int_page_total < $num)
            $num = $this->int_page_total;

        if ($this->int_page_now < 2) {
            $list = $this->create_nex_list($num);
        }

        if ($this->int_page_now > $this->int_page_total + 1) {
            $list = $this->create_pre_list($num);
        }

        if ($this->int_page_now < $this->int_page_total && $this->int_page_now > 1) {
            $middle_num = floor($num / 2);
            $list = $this->create_pre_list($middle_num) + $this->create_nex_list($middle_num);
        }
        ksort($list);

        $link = '';
        foreach ($list as $k => $v) {
            if ($k == $this->int_page_now) {
                $link .= '[<span style=\'font-weight:bold;\'>' . $k . '</span>]';
            } else {
                $link .= '[<a href="' . $v . '">' . $k . '</a>]';
            }
        }

        return empty($link) ? false : $link;
    }

    private function create_pre_list($num)
    {
        $arr_list = array();
        for ($i = 0; $i < $num + 1; $i++) {
            $page = $this->int_page_now - $i;
            if ($page < 1)
                break;
            $arr_list[$page] = $this->str_page_link . $page;
        }
        return $arr_list;
    }

    private function create_nex_list($num)
    {
        $arr_list = array();
        for ($i = 0; $i < $num + 1; $i++) {
            $page = $this->int_page_now + $i;
            if ($page > $this->int_page_total)
                break;
            $arr_list[$page] = $this->str_page_link . $page;
        }
        return $arr_list;
    }
}

?>