<?php
/*---------------------------------------------------------------------
 * 此类实现分页, 不依赖数据数的分页 <br /> content paging class.
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */

namespace herosphp\utils;

//定制分页打印页数

define( 'PAGE_TOTAL_NUM', 1<<0 );        //打印总页数
define( 'PAGE_PREV', 1<<1 );        //打印上一页
define( 'PAGE_DOT', 1<< 2);         //打印省略号
define( 'PAGE_LIST', 1<<3 );        //打印列表页
define( 'PAGE_NEXT', 1<<4 );        //打印下一页
define( 'PAGE_INPUT', 1<<5 );       //打印跳转页
define( 'DEFAULT_PAGE_STYLE', PAGE_TOTAL_NUM | PAGE_PREV | PAGE_LIST | PAGE_NEXT );  //默认样式
define( 'FULL_PAGE_STYLE', DEFAULT_PAGE_STYLE | PAGE_INPUT  );

class Page {
    private $totalRows; //总记录数
    private $pagesize = 20; //每页记录数
    private $pageNow; //当前页
    private $pageNum; //总页数
    private $url; //请求url
    private $outPage = 3; //以pageNow为基准，两边各输出$outPage页
	private $pagePrevText = '<<';		//上一页
	private $pageNextText = '>>';		//上一页
	private $pageTotalText = '总记录：';
	private $pageGotoText = 'GO';
    private $limit = ' LIMIT 0, 10';

    const URL_NORMAL = 1;   //常规url访问，用 ? 传参
    const URL_STATIC = 2;   //伪静态路径访问，用 - 传参

    private $urlPathType = self::URL_NORMAL;

    public function __construct($rows_num, $pagesize, $page_now, $outPage=3) {

        $this->totalRows = $rows_num;
        $this->pagesize = $pagesize;
        $this->pageNow = intval($page_now);
        $this->outPage = intval($outPage);
        $this->pageNum = ceil($rows_num/$pagesize);
        $this->formatpageNow();
        $this->url = $this->getUrl();
        $this->limit = ($this->pageNow - 1) * $this->pagesize .", {$this->pagesize}";

    }

    /* 获取 LIMIT字符串 */
    public function getLimit() {
        return $this->limit;
    }

    /* 提供一个魔术方法获取limit */
    public function __get($var) {
        if ( $var == 'limit' ) return $this->limit;
        if ( $var == 'pageNum' ) return $this->pageNum;//新增获取总页数
    }

    /* 格式化当前页 */
    private function formatpageNow() {
        if ( $this->pageNow > $this->pageNum ) $this->pageNow = $this->pageNum;
        if ( $this->pageNow <= 0 ) $this->pageNow = 1;
    }

    /**
     * 获取url
     * @return string
     */
    private function getUrl() {
        $url = getSourceUrl($_SERVER['REQUEST_URI']);
        //移除page参数
        $urlInfo = parse_url($url);
        if ( $urlInfo['query'] ) {
            parse_str($urlInfo['query'], $query);
            unset($query['page']);
            if ( !empty($query) ) {
                foreach ( $query as $key => $value ) {
                    $query[$key] = urldecode($value);
                }
                $url = $urlInfo['path'].'?'.http_build_query($query);
            } else {
                $url = $urlInfo['path'];
            }

        }
        //判断是否还有其他参数
        $position = strpos($url, '?');
        if ( strpos($url, '?') === false ) {
            return $url.'?page=';
        } else if( ($position + 1) == strlen($url) ) {
            return $url.'page=';
        } else {
            return $url.'&page=';
        }
    }

    /* 打印上一页 */
    private function prevPage() {
        $pageStr = '';
        if ( $this->pageNow > 1 ) {
            $pageStr .= '<li><a href="'.$this->buildUrl($this->url.($this->pageNow - 1)).'">'.$this->pagePrevText.'</a></li>';
        } else {
            $pageStr .= '<li class="disabled"><a href="#">'.$this->pagePrevText.'</a></li>';
        }
        return $pageStr;
    }

    /* 打印下一页 */
    private function nextPage() {
        $pageStr = '';
        if ( $this->pageNow < $this->pageNum ) {
            $pageStr .= '<li><a href="'.$this->buildUrl($this->url.($this->pageNow + 1)).'">'.$this->pageNextText.'</a></li>';
        } else {
            $pageStr .= '<li class="disabled"><a href="#">'.$this->pageNextText.'</a></li>';
        }
        return $pageStr;
    }

    /**
     * 打印中间页码列表，以当前页为基准， 两边各输出 $outPage 页
     * @param int $style 分页样式
     * @param string $type 返回的数据类型
     * @return string|array
     */
    private function printPageList($style, $type='string') {

        $pageList = '';
        $pageData = array();
        //打印左边页码
        $leftPages = '';
        if ( ($this->pageNow - $this->outPage) >= 2  ) {
            $leftPages .= '<li><a href="'.$this->buildUrl($this->url.'1').'" class="page_list page_Rounded5">1</a></li>';
            //$leftPages .= '<li><a href="'.url($this->url.'2').'" class="page_list page_Rounded5">2</a></li>';

            //$pageData['1'] = $this->buildUrl($this->url.'1');

            //打印左边省略号
            if ( $style & PAGE_DOT ) {
                $leftPages .= '<li class="disabled"><a href="#">...</a></li>';
            }

            for ( $i = ($this->pageNow - $this->outPage); $i < $this->pageNow; $i++ ) {
                $leftPages .= '<li><a href="'.$this->buildUrl($this->url.$i).'">'.$i.'</a></li>';
                $pageData[$i] = $this->buildUrl($this->url.$i);
            }
        } else {
            for ( $i = 1; $i < $this->pageNow; $i++ ) {
                $leftPages .= '<li><a href="'.$this->buildUrl($this->url.$i).'">'.$i.'</a></li>';
                $pageData[$i] = $this->buildUrl($this->url.$i);
            }
        }
        $pageList .= $leftPages;

        //打印当前页
        $pageList .= '<li class="active"><a href="#">'.$this->pageNow.'</a></li>';
        $pageData[$this->pageNow] = '#';

        /* 打印pageNow 右边的页码 */
        $rightPages = '';
        if ( $this->pageNum >= ($this->pageNow + $this->outPage + 2) ) {
            for ( $i = $this->pageNow+1; $i <= $this->pageNow + $this->outPage; $i++ ) {
                $rightPages .= '<li><a href="'.$this->buildUrl($this->url.$i).'">'.$i.'</a></li>';
                $pageData[$i] = $this->buildUrl($this->url.$i);
            }

            //打印右边省略号
            if ( $style & PAGE_DOT ) {
                $rightPages .= '<li class="disabled"><a href="#">...</a></li>';
            }

            //$rightPages .= '<li><a href="'.url($this->url.($this->pageNum - 1)).'">'.($this->pageNum - 1).'</a></li>';
            $rightPages .= '<li><a href="'.$this->buildUrl($this->url.$this->pageNum).'">'.$this->pageNum.'</a></li>';
            //$pageData[$this->pageNum] = $this->buildUrl($this->url.$this->pageNum);

        } else {
            for ( $i = ($this->pageNow + 1); $i <= $this->pageNum; $i++  ) {
                $rightPages .=  '<li><a href="'.$this->buildUrl($this->url.$i).'">'.$i.'</a></li>';
                $pageData[$i] = $this->buildUrl($this->url.$i);
            }
        }
        $pageList .= $rightPages;

        if ( $type == 'string' ) {
            return $pageList;
        } else if ( $type == 'array' ) {
            return $pageData;
        }
    }

    private function printInput() {

		return '<li><input type="text" onblur="javascript:var page = this.value;
		document.getElementById(\'page_go_to\').href=\''.$this->url.'\'+page;"
		class="form-control my_page_input" value="'.$this->pageNow.'"><a id="page_go_to">'.$this->pageGotoText.'</a></li>';

    }

	public function setPagePrevText($text) {
		$this->pagePrevText = $text;
	}

	public function setPageNextText($text) {
		$this->pageNextText = $text;
	}

	public function setPageTotalText($text) {
		$this->pageTotalText = $text;
	}

	public function setPageGotoText($text) {
		$this->pageGotoText = $text;
	}

    public function setUrlPathType($type) {
        $this->urlPathType = $type;
    }

    //组建url
    private function buildUrl($url) {
        if ( $this->urlPathType == self::URL_NORMAL ) {
            return $url;
        } else if ( $this->urlPathType == self::URL_STATIC ) {
            return url($url);
        }
    }

    /**
     * 打印分页列表
     * @param int $style
     * @return string
     */
    public function showPageHandle($style = DEFAULT_PAGE_STYLE) {

        if ( $this->pageNum <= 1 ) return false;

        $html = '';
        if ( $style & PAGE_TOTAL_NUM ) $html .= '<li class="disabled"><a href="#">总记录：'.$this->totalRows.'</a></li>';
        if ( $style & PAGE_PREV ) $html .= $this->prevPage();
        if ( $style & PAGE_LIST ) $html .= $this->printPageList($style);
        if ( $style & PAGE_NEXT ) $html .= $this->nextPage();
        if ( $style & PAGE_INPUT ) $html .= $this->printInput();

        return $html;

    }

    /**
     * 获取分页数据
     * @param int $style
     * @param $showFirstPage 当只有一页的时候是否显示分页列表
     * @return array
     */
    public function getPageData($style = DEFAULT_PAGE_STYLE, $showFirstPage=false) {

        if ( $this->pageNum <= 1 && $showFirstPage == false ) return array();

        $pages = array('url' => $this->buildUrl($this->url.'{page}'));
        if ( $style & PAGE_TOTAL_NUM ) $pages['total'] = $this->totalRows;
        $pages['first'] = $this->buildUrl($this->url."1");
        if ( $style & PAGE_PREV ) {
            if ( $this->pageNow > 1 ) {
                $pages['prev'] = $this->buildUrl($this->url.($this->pageNow -1));
            } else {
                $pages['prev'] = '#';
            }
        }

        if ( $style & PAGE_LIST ) {
            $pages['list'] = $this->printPageList($style, 'array');
        }
        if ( $style & PAGE_NEXT ) {
            if ( $this->pageNow < $this->pageNum ) {
                $pages['next'] = $this->buildUrl($this->url.($this->pageNow +1));
            } else {
                $pages['next'] = '#';
            }
        }
        $pages['page'] = $this->pageNow;
        $pages['last'] = $this->buildUrl($this->url.$this->pageNum);
        return $pages;

    }
}
