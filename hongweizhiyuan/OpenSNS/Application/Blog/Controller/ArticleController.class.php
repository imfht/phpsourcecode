<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Blog\Controller;


/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ArticleController extends BlogController
{

    /* 文档模型频道页 */
    public function index()
    {
        /* 分类信息 */
        $category = $this->category();

        //频道页只显示模板，默认不读取任何内容
        //内容可以通过模板标签自行定制

        /* 模板赋值并渲染模板 */
        $this->assign('category', $category);
        $this->display($category['template_index']);
    }

    /* 文档模型列表页 */
    public function lists($page = 1)
    {
        /* 分类信息 */
        $category = $this->category();

        /* 获取当前分类列表 */
        $Document = D('Document');
        $Category = D('Blog/Category');

        $children = $Category->getChildrenId($category['id']);
        if ($children == '') {
            //获取当前分类下的文章
            $list = $Document->page($page, $category['list_row'])->lists($category['id']);
            $is_top_category = ($category['pid'] == 0);
            if (!$is_top_category) {//判断是否是顶级分类，如果是顶级，就算没有子分类，也不获取同级
                //如果是不是顶级分类
                $children = $Category->getSameLevel($category['id']);
                $this->setCurrent($category['pid']);
                $this->assign('children_cates', $children);
            } else {
                //如果是顶级分类
                $this->setCurrent($category['id']);
            }


        } else {
            //如果还有子分类
            //分割分类
            $children = explode(',', $children);
            //将当前分类的文章和子分类的文章混合到一起
            $cates = $children;
            array_push($cates, $category['id']);
            $list = $Document->page($page, $category['list_row'])->lists(implode(',', $cates));
            //dump($children);exit;
            //得到子分类的目录
            foreach ($children as &$child) {
                $child = $Category->info($child);
            }
            unset($child);
            $this->setCurrent($category['id']);
            $this->assign('children_cates', $children);
        }


        if (false === $list) {
            $this->error('获取列表数据失败！');
        }


        /* 模板赋值并渲染模板 */
        $this->assign('category', $category);
        $this->setTitle('{$category.title|op_t}');
        $this->assign('list', $list);
        $this->display($category['template_lists']);
    }

    /* 文档模型详情页 */
    public function detail($id = 0, $p = 1)
    {
        /* 标识正确性检测 */
        if (!($id && is_numeric($id))) {
            $this->error('文档ID错误！');
        }

        /* 页码检测 */
        $p = intval($p);
        $p = empty($p) ? 1 : $p;

        /* 获取详细信息 */
        $Document = D('Document');
        $info = $Document->detail($id);
        if (!$info) {
            $this->error($Document->getError());
        }

        /* 分类信息 */
        $category = $this->category($info['category_id']);

        /* 获取模板 */
        if (!empty($info['template'])) { //已定制模板
            $tmpl = $info['template'];
        } elseif (!empty($category['template_detail'])) { //分类已定制模板
            $tmpl = $category['template_detail'];
        } else { //使用默认模板
            $tmpl = 'Article/' . get_document_model($info['model_id'], 'name') . '/detail';
        }

        /* 更新浏览数 */
        $map = array('id' => $id);
        $Document->where($map)->setInc('view');

        /* 模板赋值并渲染模板 */
        $this->assign('category', $category);
        $this->assign('info', $info);
        $this->assign('page', $p); //页码
        $this->setTitle('{$info.title} —— 资讯');

        $this->display($tmpl);
    }

    /* 文档分类检测 */
    private function category($id = 0)
    {
        /* 标识正确性检测 */
        $id = $id ? $id : I('get.category', 0);
        if (empty($id)) {
            $this->error('没有指定文档分类！');
        }

        $categoryModel = new \Blog\Model\CategoryModel();
        /* 获取分类信息 */
        $category = D('Blog/Category')->info($id);
        if ($category && 1 == $category['status']) {
            switch ($category['display']) {
                case 0:
                    $this->error('该分类禁止显示！');
                    break;
                //TODO: 更多分类显示状态判断
                default:
                    return $category;
            }
        } else {
            $this->error('分类不存在或被禁用！');
        }
    }

    /**
     * @param $category
     * @auth 陈一枭
     */
    private function setCurrent($category_id)
    {
        $this->assign('current', $category_id);
    }

}
