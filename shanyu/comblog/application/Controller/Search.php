<?php
namespace App\Controller;

use Kernel\Loader;

use App\Model\Article as ArticleModel;
use App\Model\ArticleCategory as CategoryModel;
use App\Model\Tag as TagModel;

class Search extends Controller
{

    public function index($p=1){
        $this->assign('TITLE','博文搜索');

        $search=[];
        $filter='';
        if(isset($_GET['tag']) && $_GET['tag'] > 0){
            $tag_id = intval($_GET['tag']);
            $search['type'] = '标签搜索';
            $search['keyword'] = Loader::singleton(TagModel::class)->getTitle($tag_id);
            if(!$search['keyword']){
                return show_404();
            }
            $filter="AND id IN (SELECT article_id FROM article_tag WHERE tag_id = {$tag_id})";
        }elseif(isset($_GET['keyword']) && !empty($_GET['keyword'])){
            $keyword = htmlspecialchars($_GET['keyword']);
            if(mb_strlen($keyword,'utf8') > 10){
                $keyword = mb_substr($keyword, 0,10,'utf8');
            }
            $search['type'] = '关键字搜索';
            $search['keyword'] = $keyword;
            $filter="AND title LIKE '%{$keyword}%'";
        }else{
            return show_404();
        }

        $articles=Loader::singleton(ArticleModel::class)->getListPage($filter,6,$p,'/index-{page}.html');
        $this->assign(compact('search','articles'));

        return $this->fetch('search_index');
    }

}