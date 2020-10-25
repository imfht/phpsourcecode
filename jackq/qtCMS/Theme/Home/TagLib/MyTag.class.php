<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/28
 * Time: 13:58
 */

namespace Home\TagLib;

use Think\Template\TagLib;


class MyTag extends TagLib
{
    protected $tags = array(
        'nav' => array('attr' => 'item,key,count', 'level' => 3),//导航
        'articles' => array('attr' => 'item,key,count,category_id,start,limit', 'level' => 3, 'close' => 1),//通过栏目名称找文章
        'slider'=> array('attr' => 'item,key,count,category_id,limit', 'level' => 3, 'close' => 1),//幻灯片
        'articleImg'=> array('attr' => 'id', 'level' => 3, 'close' => 1),//图片路径
    );

    public function _nav($tag, $content)
    {
        $item = !empty($tag['item']) ? $tag['item'] : 'cate';
        $key = !empty($tag['key']) ? $tag['key'] : 'key'; //数字索引
        $count = !empty($tag['count']) ? $tag['count'] : 'count'; //数组长度
        $where = "is_show = 1 and (pid =0 or pid is null)";
        $parseStr = '<?php  $Category = D("Category")->relation(true);  $cates = $Category->where("' . $where . '")->order("sort")->select(); ';
        $parseStr .= 'if(is_array($cates)):  $'.$count.'=count($cates); foreach($cates as $' . $key . '=>$' . $item . '): ?> ';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif; ?>';
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    public function _articles($tag, $content)
    {
        $item = !empty($tag['item']) ? $tag['item'] : 'article';
        $key = !empty($tag['key']) ? $tag['key'] : 'key'; //数字索引
        $count = !empty($tag['count']) ? $tag['count'] : 'count'; //数组长度
        $limit = !empty($tag['limit']) ? $tag['limit'] : 5; //mysql limit
        $start = !empty($tag['start']) ? (int)$tag['start'] : 0;
        $category_id = $tag['category_id'];
        if(empty($category_id)) return;
        $where ='category_id = '.$category_id;
        $parseStr = '<?php  $Article = M("Article");  $articles = $Article->where("' . $where . '")->order("public_time desc")->limit('.$start.','.$limit.')->select(); ';
        $parseStr .= 'if(is_array($articles)):  $'.$count.'=count($articles); foreach($articles as $' . $key . '=>$' . $item . '):  ?> ';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif; ?>';
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    public function _slider($tag, $content)
    {
        $item = !empty($tag['item']) ? $tag['item'] : 'article';
        $key = !empty($tag['key']) ? $tag['key'] : 'key'; //数字索引
        $count = !empty($tag['count']) ? $tag['count'] : 'count'; //数组长度
        $limit = !empty($tag['limit']) ? $tag['limit'] : 5; //mysql limit
        $sql = 'select ar.id as id,ar.title as title,att.url as url,ar.public_time as public_time from qt_article ar, qt_attachment att where att.attach_type=1 and ar.is_slide=1 and ar.id=att.article_id group by ar.id,ar.public_time order by ar.public_time desc limit 0,'.$limit;
        $parseStr = '<?php  $M = M();  $articles = $M->query("' . $sql . '"); ';
        $parseStr .= 'if(is_array($articles)):  $'.$count.'=count($articles); foreach($articles as $' . $key . '=>$' . $item . '):  ?> ';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endforeach; endif; ?>';
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    public function _articleImg($tag, $content){
        $id = $tag['id'];
        if(empty($id)) return;
        $id   = $this->autoBuildVar($id);
        //$sql ="select url from qt_attachment where attach_type=1 and article_id=".$id.' limit 1';
        $parseStr = '<?php $sql ="select url from qt_attachment where attach_type=1 and article_id=".'.$id.'." limit 1";   $M = M();  $articles = $M->query($sql); ';
        $parseStr .= 'if(is_array($articles)):  $imgUrl= $articles[0]["url"] ?> ';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php endif; ?>';
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;

    }

} 