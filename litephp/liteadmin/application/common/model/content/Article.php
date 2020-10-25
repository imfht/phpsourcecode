<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/10
 * Time: 16:48
 */

namespace app\common\model\content;

use think\Model;

class Article extends Model
{
    protected $name = 'content_article';

    public function tags()
    {
        return $this->belongsToMany(Tags::class,TagsMap::class,'tag_id','article_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'cid','id');
    }
}