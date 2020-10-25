<?php
require_cache('Home/Index/Lib/Url.class.php');
require_cache('Home/Index/Lib/Functions.php');
C('TPL_TAGS', array('@.Index.Tag.ContentTag'));

/**
 * 静态生成
 * @author 楚羽幽 <Name_Cyu@Foxmail.com>
 */
class Html extends Controller
{
    private $model;//模型缓存
    private $category;//栏目缓存
    public $error;//错误信息

    public function __init()
    {
        $this->model = S('model');
        $this->category = S("category");
        define('__TEMPLATE__', __ROOT__ . '/Theme/' . C('WEB_STYLE'));
    }

    /**
     * 生成首页
     * @return bool
     */
    public function index()
    {
        if (C('CREATE_INDEX_HTML')) {
            $template = 'Theme/' . C('WEB_STYLE') . '/index.html';
            //验证模板文件
            if (!is_file($template) || !is_readable($template)) {
                $this->error = '模板不存在';
                return false;
            }
            if ($this->createHtml('index.html', './', $template)) {
                return true;
            } else {
                $this->error = '生成失败';
            }
        }
    }

    /**
     * 内容页
     * @param $mid 模型mid
     * @param $aid 文章aid
     * @return bool
     */
    public function content($mid, $aid)
    {
        $data = ContentViewModel::getInstance($mid)->one($aid);
        if (!$data) {
            return;
        }
        //文章动态访问(文章定义生成方式)
        if ($data['url_type'] == 2) {
            return true;
        }
        //文章没定义生成方式时使用栏目规则
        if ($data['url_type'] == 3 && $data['arc_url_type'] == 2) {
            return true;
        }
        //模板文件
        if (empty($data['template'])) {
            $tplFile = $data['arc_tpl'];
        } else {
            $tplFile = $data['template'];
        }
        $template = 'Template/' . C('WEB_STYLE') . '/' . $tplFile;
        //验证模板文件
        if (!is_file($template) || !is_readable($template)) {
            return false;
        }
        //HTML存放根目录
        $html_path = C("HTML_PATH") ? C("HTML_PATH") . '/' : '';
        //栏目定义的内容页生成静态规则
        $_s = array('{catdir}', '{y}', '{m}', '{d}', '{aid}');
        $time = getdate($data['addtime']);
        $_r = array($data['catdir'], $time['year'], $time['mon'], $time['mday'], $data['aid']);
        //静态文件
        if (empty($data['html_path'])) {
            $htmlFile = $data['arc_html_url'];
        } else {
            $htmlFile = $data['html_path'];
        }
        $htmlFile = $html_path . str_replace($_s, $_r, $htmlFile);
        $_REQUEST['mid'] = $data['mid'];
        $_REQUEST['cid'] = $data['cid'];
        $_REQUEST['aid'] = $data['aid'];
        $this->assign('hdcms', $data);
        return $this->createHtml(basename($htmlFile), dirname($htmlFile) . '/', $template);
    }

    /**
     * 生成文章的上一篇与下一篇(关联文章)
     * @param $mid 模型mid
     * @param $aid 文章aid
     */
    public function relation_content($mid, $aid)
    {
        $model = ContentModel::getInstance($mid);
        //生成上一篇
        $preAid = $model->where('aid<' . $aid)->limit(1)->order("aid DESC")->getField('aid');
        if ($preAid) $this->content($mid, $preAid);
        //生成下一篇
        $nextAid = $model->where('aid>' . $aid)->limit(1)->order("aid ASC")->getField('aid');
        if ($nextAid) $this->content($mid, $nextAid);
    }

    /**
     * 生成单一栏目
     * @param $cid 栏目cid
     * @param int $page 生成页码
     * @return bool
     */
    public function category($cid, $page = 1)
    {
        $categoryCache = S('category');
        if (!isset($categoryCache[$cid])) {
            return false;
        }
        $cat = $categoryCache[$cid];
        $GLOBALS['totalPage'] = 0;
        //动态规则或外部链接栏目不生成
        if ($cat['cat_url_type'] == 2 || $cat['cattype'] == 3) {
            return true;
        }
        //单文章
        if ($cat['cattype'] == 4) {
            $Model = ContentViewModel::getInstance($cat['mid']);
            $aid = $Model->where("category.cid={$cat['cid']}")->getField('aid');
            if ($aid) {
                return $this->content($cat['mid'], $aid);
            }
        } else {
            //模板文件
            switch ($cat['cattype']) {
                case 1 : //普通栏目
                    $template = 'Template/' . C("WEB_STYLE") . '/' . $cat['list_tpl'];
                    break;
                case 2 : //封面栏目
                    $template = 'Template/' . C("WEB_STYLE") . '/' . $cat['index_tpl'];
                    break;
            }
            //验证模板文件
            if (!is_file($template) || !is_readable($template)) {
                return false;
            }
            //普通栏目与封面栏目
            $htmlDir = C("HTML_PATH") ? C("HTML_PATH") . '/' : '';
            $_REQUEST['page'] = $_GET['page'] = $page;
            $_REQUEST['mid'] = $cat['mid'];
            $_REQUEST['cid'] = $cat['cid'];
            $Model = ContentViewModel::getInstance($cat['mid']);
            $cat['content_num'] = $Model->where("category.cid ={$cat['cid']}")->count();
            $htmlFile = $htmlDir . str_replace(array('{catdir}', '{cid}', '{page}'), array($cat['catdir'], $cat['cid'], $page), $cat['cat_html_url']);
            $this->assign("hdcms", $cat);
            $this->createHtml(basename($htmlFile), dirname($htmlFile) . '/', $template);
            //第1页时复制index.html
            if ($page == 1) {
                copy($htmlFile, $htmlDir . $cat['catdir'] . '/index.html');
            }
            return true;
        }
    }

    /**
     * 生成栏目分页列表
     * @param $cid
     * @return bool
     */
    public function relation_category($cid)
    {
        $cache = S('cate');
        if (!isset($cache[$cid])) return false;
        $cat = $cache[$cid];
        //单文章与外部链接栏目不生成
        if ($cat['cat_url_type'] == 2 || $cat['cattype'] == 3) {
            return true;
        }
        unset($GLOBALS['totalPage']);
        $d = $page = 1;
        do {
            $this->category($cid, $page);
            $d++;
            $page++;
            $totalPage = $GLOBALS['totalPage'];
        } while ($d <= $totalPage && $d < 10);
        return true;
    }

    /**
     * 生成所有父级栏目
     * @param $cid
     * @return bool
     */
    public function parent_category($cid)
    {
        $parent = Data::parentChannel($this->category, $cid);
        if (!$parent) return;
        foreach ($parent as $p) {
            $this->relation_category($p['cid']);
        }
        return true;
    }

    /**
     * 生成所有栏目
     * @return bool
     */
    public function all_category()
    {
        foreach ($this->cate as $cat)
        {
            $this->relation_category($cat['cid']);
        }
        return true;
    }
}
