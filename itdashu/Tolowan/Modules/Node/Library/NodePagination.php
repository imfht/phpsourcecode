<?php
namespace Modules\Node\Library;

use Core\Config;

class NodePagination
{
    private $pagestr;       //被切分的内容
    private $pagearr;       //被切分文字的数组格式
    private $sum_word;      //总字数(UTF-8格式的中文字符也包括)
    private $sum_page;      //总页数
    private $page_word;     //一页多少字
    private $cut_tag;       //自动分页符
    private $cut_custom;    //手动分页符
    private $ipage;         //当前切分的页数，第几页
    private $url;

    function __construct($pagestr, $page_word = 2000)
    {
        //echo $pagestr;
        $nodeConfig = Config::get('m.node.config');
        $this->page_word = $nodeConfig['pagination'] > 30 ? $nodeConfig['pagination'] : $page_word;
        $this->cut_tag = array("</p>", "<br/>", "</table>", "</div>");
        $this->cut_custom = $nodeConfig['pagination_tag'];
        $tmp_page = 1;
        $this->ipage = $tmp_page > 1 ? $tmp_page : 1;
        $this->pagestr = $pagestr;
    }

    function cut_str()
    {
        $page_arr = [];
        $str_len_word = mb_strlen($this->pagestr);     //获取使用strlen得到的字符总数
        $i = 0;

        if (mb_strpos($this->pagestr, $this->cut_custom)) {

            $page_arr = explode($this->cut_custom, $this->pagestr);
        } elseif ($str_len_word <= $this->page_word) {
            $page_arr[$i] = $this->pagestr;
        } else {
            $page_arr = [];
            $startNum = 0;
            $currentBodyNum = 0;
            $stop = false;
            do {
                $currentString = mb_substr($this->pagestr, $startNum, $this->page_word);
                if (mb_strlen($currentString) < $this->page_word && $currentBodyNum >= $str_len_word) {
                    $stop = true;
                }
                $maxNum = 0;
                $currentTag = '';

                foreach ($this->cut_tag as $ct) {
                    $num = mb_strripos($currentString, $ct);
                    if ($num > $maxNum) {
                        $maxNum = $num;
                        $currentTag = $ct;
                    }
                }

                $tagLenght = mb_strlen($currentTag);
                $currentString = mb_substr($this->pagestr, $startNum, $maxNum + $tagLenght, 'utf-8');
                $startNum = $startNum + $maxNum + $tagLenght;
                $currentBodyNum = $currentBodyNum + $maxNum + $tagLenght;
                $page_arr[] = closeHtmlTags($currentString);
            } while ($stop === false);
        }

        $this->sum_page = count($page_arr);     //总页数
        $this->pagearr = $page_arr;
        return $page_arr;
    }
}