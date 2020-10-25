<?php

namespace App\Model;
use Swoole;
/**
 * 微信用户标签模型.
 */
class WxUserTag extends \App\Component\BaseModel
{
    public $primary = 'tagId';
    /**
     * 表名.
     *
     * @var string
     */
    public $table = 'wx_user_tag';

    /**
     * @return array
     */
    public function getUserTagList($cleanCache = false)
    {
        $cacheId = 'allWxUserTagList';
        $tagList = Swoole::$php->cache->get($cacheId);
        if (empty($tagList) || $cleanCache){
            $tagList = $this->gets([
                'select' => 'tagId,wxTagId,tagName,parentId,orderNum',
                'from' => $this->table,
                'where' => "isDel=0",
                'order' => "orderNum ASC,tagId ASC",
            ]);
        }
        return $tagList;
    }
}
