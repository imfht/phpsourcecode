<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

/**
 * 文章详情
 * Class ArticleContent
 * @package App\Models
 */
class ArticleContent extends BaseModels
{

    protected $table = 'article_content';
    protected $guarded = [];

    public $timestamps = false;

}
