<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class Mypagination {

    //基本链接地址
    var $base_url = '';

    //数据总数
    var $total_rows = '';

    //每页条数
    var $per_page = 20;

    //要显示的左右链接的个数
    var $num_links = 5;

    //当前页数
    var $cur_page = 1;

    //首页字符
    var $first_link = '';

    //下一页的字符
    var $next_link = '下一页';

    //上一页的字符
    var $prev_link = '上一页';

    //末页的字符
    var $last_link = '';

    //分页数所在的uri片段位置
    var $uri_segment = 3;

    //分页区域开始的html标签
    var $full_tag_open = '<ul>';

    //分页区域结束的后html标签
    var $full_tag_close = '</ul>';

    //首页开始的html标签
    var $first_tag_open = '';

    //首页结束的html标签
    var $first_tag_close = ' ';

    //末页开始的html标签
    var $last_tag_open = ' ';

    //末页结束的html标签
    var $last_tag_close = '';

    //当前页开始的html标签
    var $cur_tag_open = ' <li>';

    //当前页结束的html标签
    var $cur_tag_close = '</li>';

    //下一页开始的html标签
    var $next_tag_open = '<li>';

    //下一页结束的html标签
    var $next_tag_close = '</li>';

    //上一页开始的html标签
    var $prev_tag_open = '<li>';

    //上一页结束的html标签
    var $prev_tag_close = '</li>';

    //“数字”链接的打开标签
    var $num_tag_open = '<li>';

    //“数字”链接的结束标签
    var $num_tag_close = '</li>';


    var $page_query_string = TRUE;

    var $query_string_segment = 'page';

    var $page_mode = 'default';

    //存在下划线时,页码所在数组下标位置
    var $underline_uri_seg = -1;

    //自定义当前页码，存在此值是，系统将不自动判断当前页数，默认不启用
    var $custom_cur_page = 0;


    function __construct() {

        $this -> Mypagination();

    }

    /**
     * Constructor
     *
     * @access public
     */

    function Mypagination() {

        if (file_exists(APPPATH . 'config/pagination.php')) {

            require_once (APPPATH . 'config/pagination.php');

            foreach ($config as $key => $val) {

                $this -> {$key} = $val;

            }

        }

        log_message('debug', "MY_Pagination Class Initialized");

    }

    // ------------------------------------------------------------------------


    /**
     * 初始化参数
     * @see  init()
     * @param <array> $params 待初始化的参数
     */

    function init($params = array()) {

        if (count($params) > 0) {

            foreach ($params as $key => $val) {

                if (isset($this -> $key)) {

                    $this -> $key = $val;

                }

            }

        }

    }

    // ------------------------------------------------------------------------


    /**
     * 创建分页链接
     * @see  create_links()
     * @param <boolean> $show_info 是否显示总条数等信息
     * @return <string> $output
     */

    function create_links($show_info = false, $top_info = false) {

        //如果没有记录或者每页条数为0,则返回空
        if ($this -> total_rows == 0 || $this -> per_page == 0) {

            return '';

        }

        //计算总页数

        $num_pages = ceil($this -> total_rows / $this -> per_page);

        //只有一页,返回空

        if ($num_pages == 1 && !$show_info) {

            return '';

        }

        $CI = &get_instance();

        //获取当前页编号

        if ($CI -> config -> item('enable_query_strings') === TRUE || $this -> page_query_string === TRUE) {

            if ($CI -> input -> get($this -> query_string_segment) != 0) {

                $this -> cur_page = $CI -> input -> get($this -> query_string_segment);

                $this -> cur_page = (int)$this -> cur_page;

            }

        } else {

            if (intval($this -> custom_cur_page) > 0) {

                $this -> cur_page = (int)$this -> custom_cur_page;

            } else {

                $uri_segment = $CI -> uri -> segment($this -> uri_segment, 0);

                if (!empty($uri_segment)) {

                    $this -> cur_page = $uri_segment;

                    //如果有下划线

                    if ($this -> underline_uri_seg >= 0) {

                        if (strpos($this -> cur_page, '-') !== false) {

                            $arr = explode('-', $this -> cur_page);

                        } else {

                            $arr = explode('_', $this -> cur_page);

                        }

                        $this -> cur_page = $arr[$this -> underline_uri_seg];

                        unset($arr);

                    }


                    $this -> cur_page = (int)$this -> cur_page;

                }

            }

        }

        //左右显示的页码个数

        $this -> num_links = (int)$this -> num_links;

        if ($this -> num_links < 1) {

            show_error('Your number of links must be a positive number.');

        }

        if (!is_numeric($this -> cur_page) || $this -> cur_page < 1) {

            $this -> cur_page = 1;

        }

        //如果当前页数大于总页数,则赋值给当前页数最大值

        if ($this -> cur_page > $num_pages) {

            $this -> cur_page = $num_pages;

        }

        if ($CI -> config -> item('enable_query_strings') === TRUE || $this -> page_query_string === TRUE) {

            $this -> base_url = rtrim($this -> base_url) . '&' . $this -> query_string_segment . '=';

        } else {

            $this -> base_url = rtrim($this -> base_url, '/') . '/';

        }

        if (strpos($this -> base_url, "{page}") !== false) {

            $this -> page_mode = 'replace';

        }

        $output = $top_output = '';

        //数据总量信息

        if ($show_info) {

            $output = " 共<b>" . $this -> total_rows . "</b>条记录 <span style='color:#ff0000;font-weight:bold'>{$this->cur_page}</span>/<b>" . $num_pages . "</b>页 每页<b>{$this->per_page}</b>条 ";

        }

        //数据信息，显示在上面，以供提醒

        if ($top_info) {

            $top_output = " 共 <b>" . $this -> total_rows . "</b> 条记录 第<span style='color:#ff0000;font-weight:bold'>{$this->cur_page}</span>页/共<b>" . $num_pages . "</b>页 ";

        }

        //判断是否要显示首页

//        if ($this -> cur_page > $this -> num_links + 1) {
//
//          $output .= $this -> first_tag_open . '<a href="' . $this -> makelink() . '">' . $this -> first_link . '</a>' . $this -> first_tag_close;
//
//        }

        //显示上一页

        if (!empty($this -> cur_page)) {
            $j = $this -> cur_page - 1;
            if ($j == 0){
                $output .='<li class="active"><a href="#">' . $this -> prev_link . '</a>' . $this -> prev_tag_close;
            }else{
                $output .= $this -> prev_tag_open . '<a href="' . $this -> makelink($j) . '">' . $this -> prev_link . '</a>' . $this -> prev_tag_close;
            }

        }

        //显示中间页

        for ($i = 1; $i <= $num_pages; $i++) {

            if ($i < $this -> cur_page - $this -> num_links || $i > $this -> cur_page + $this -> num_links) {

                continue;

            }

            //显示中间页数

            if ($this -> cur_page == $i) {

                //当前页


                $output .= '<li class="active"><a href="#">' . $i . '</a>' . $this -> num_tag_close;



            } else {
                $output .= $this -> num_tag_open . '<a href="'. $this -> makelink($i) .'">' . $i . '</a>' . $this -> num_tag_close;


            }
        }

        //显示下一页

        if ($this -> cur_page < $num_pages) {

            $k = $this -> cur_page + 1;

            $output .= $this -> next_tag_open . '<a href="' . $this -> makelink($k) . '">' . $this -> next_link . '</a>' . $this -> next_tag_close;

        }else{
            $output .= '<li class="active"><a href="#">'. $this -> next_link . '</a>' . $this -> next_tag_close;
        }

        //显示尾页

//        if (($this -> cur_page + $this -> num_links) < $num_pages) {
//
//           $output .= $this -> last_tag_open . '<a href="' . $this -> makelink($num_pages) . '">' . $this -> last_link . '</a>' . $this -> last_tag_close;
//
//        }

        $output = preg_replace("#([^:])//+#", "\\1/", $output);


        $output = $this -> full_tag_open . $output . $this -> full_tag_close;

        if ($top_info) {

            return array($output, $top_output);
        } else {

            return $output;

        }

    }

    // ------------------------------------------------------------------------

    /**
     * 创建链接url地址
     * @param <string> $str
     * @return <string>
     */

    function makelink($str = '') {

        if ($this -> page_mode == 'default') {

            return $this -> _forsearch($this -> base_url . $str);

        } else {

            $url = $this -> base_url;

            if ($str == 1) {

                $url = str_replace('/{page}', '', $this -> base_url);

            }

            $url = str_replace("{page}", $str, $url);

            return $this -> _forsearch($url);
        }

    }

    // ------------------------------------------------------------------------

    /**
     * 处理url地址
     * @see  _forsearch()
     * @param <string> $string pInfo
     * @return <string>
     */

    function _forsearch($string) {

        $length = strlen($string) - 1;

        if ($string{$length} == '/') {

            $string = rtrim($string, '/');

        }

        return site_url($string);

        return $string;
    }

    // ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------


// End Mypagination Class
/* End of file Mypagination.php */
/* Location: ./app/admin/libraries/Mypagination.php*/
