<?php


namespace WxSDK\core\model\mass;


class Filter{
    /**
     *
     * @var boolean
     */
    public $is_to_all;
    public $tag_id;
    function __construct(bool $isToAll = null , string $tagId = null) {
        $this->is_to_all = $isToAll == null ? true : $isToAll;
        $this->tag_id = $tagId;
    }
}