<?php
namespace App\Controller;

use Kernel\Loader;

use App\Model\Article as ArticleModel;
use App\Model\ArticleCategory as CategoryModel;
use App\Model\Tag as TagModel;

use Parsedown;

class Article extends Controller
{
    public function _initialize()
    {
        parent::_initialize();

        $this->category_model = Loader::singleton(CategoryModel::class);
        $this->article_model = Loader::singleton(ArticleModel::class);
        $this->tag_model = Loader::singleton(TagModel::class);
    }

    public function index($cid=1,$p=1){
        //页面介绍
        $detail = $this->category_model->getDetail($cid);
        if(!$detail) show_404();

        $this->assign('TITLE',$detail['title']);

        //面包屑导航
        $crumb = [];
        $crumb[] = ['id'=>$detail['id'],'title'=>$detail['title'],'url'=>$detail['url']];
        if($pids = $detail['pids']){
            $parents=$this->category_model->getParentList($pids);
            $crumb = array_merge($parents,$crumb);
        }

        //同类和子类的文章
        $where = '';
        $filter = $this->category_model->getChildFilter($cid);
        if($filter) $where .= "AND {$filter} ";
        
        $articles=$this->article_model->getListPage($where,6,$p,'/article/index-'.$cid.'-{page}.html');

        $this->assign(compact('crumb','articles'));
        return $this->fetch('article_index');
    }

    public function detail($id){
        //页面介绍
        $detail=$this->article_model->getDetail($id);
        if(!$detail) show_404();

        $this->assign('TITLE',$detail['title']);

        $category = $this->category_model->getDetail($detail['cid']);

        //面包屑导航
        $crumb = [];
        $crumb[] = ['id'=>$category['id'],'title'=>$category['title'],'url'=>$category['url']];
        if($pids = $category['pids']){
            $parents=$this->category_model->getParentList($pids);
            $crumb = array_merge($parents,$crumb);
        }

        //增加点击量
        $this->article_model->increaseView($id);

        // 格式化数据
        $detail['content']=stripslashes(htmlspecialchars_decode($detail['content']));
        $detail['create_time']=date('Y/m/d',strtotime($detail['create_time']));
        $detail['view'] +=1;

        // 获取标签
        $tags = Loader::singleton(TagModel::class)->getListByArticleId($detail['id']);

        // 修改SEO
        if(!empty($detail['description'])){
            $this->assign('SEO_DESCRIPTION',$detail['description']);
        }
        if(!empty($tags)){
            $tags_text = implode(',', array_column($tags,'title'));
            $this->assign('SEO_KEYWORDS',$tags_text);
        }

        $this->assign(compact('crumb','detail','tags'));
        return $this->fetch('article_detail');
    }

    public function category()
    {
        $this->assign('TITLE','文章分类');

        $categorys=$this->category_model->getList();
        $counts=$this->article_model->getCountByCategory();

        //当前分类加上子集统计
        foreach ($categorys as $k => $v) {
            if(isset($counts[$v['id']])){
                $_count = $counts[$v['id']];
            }else{
                $_count = 0;
            }
            $child_ids = $this->category_model->getChildId($v['id']);
            if($child_ids){
                foreach ($counts as $kk => $vv) {
                    if(in_array($kk, $child_ids)){
                        $_count += $vv;
                    }
                }
            }
            $categorys[$k]['count'] = $_count;
        }

        require_once EXTEND_PATH.'Tree.php';
        $category_tree=\Tree::listToTree($categorys);

        $this->assign(compact('category_tree'));

        return $this->fetch('article_category');
    }
    public function archive()
    {
        $this->assign('TITLE','日期归档');

        $list = $this->article_model->getListByDate();
        $years = array_values(array_unique(array_column($list,'create_year')));

        $this->assign(compact('list','years'));
        return $this->fetch('article_archive');
    }
    public function tag()
    {
        $this->assign('TITLE','相关标签');

        $list = $this->tag_model->getTitleCountList();

        $this->assign(compact('list'));
        return $this->fetch('article_tag');
    }
}