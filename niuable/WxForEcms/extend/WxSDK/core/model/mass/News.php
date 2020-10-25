<?php
namespace WxSDK\core\model\mass;

use WxSDK\core\model\Model;

class News extends Model{
    /**
     * 图文消息，一个图文消息支持1到8条图文
     * @var array
     */
    public $articles;
    public function __construct(Article... $article) {
        $this->articles = $article;
    }
}

