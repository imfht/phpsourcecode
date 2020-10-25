<?php
namespace WxSDK\core\model\mass;

class Article{
    /**
     * 图文消息缩略图的media_id，可以在素材管理-新增素材中获得，永久图文须永久图片的
     * @var string
     */
    public $thumb_media_id;
    public $author;
    public $title;
    /**
     * 在图文消息页面点击“阅读原文”后的页面，受安全限制，如需跳转Appstore，可以使用itun.es或appsto.re的短链服务，并在短链后增加 #wechat_redirect 后缀。
     * @var string
     */
    public $content_source_url;
    /**
     * 
     * @var string
     */
    public $content;
    /**
     * 图文消息的描述，如本字段为空，则默认抓取正文前64个字
     * @var string
     */
    public $digest;
    /**
     * 是否显示封面，1为显示，0为不显示
     * 群发图文时，非必须；永久图文为必须
     * @var int
     */
    public $show_cover_pic;
    /**
     * Uint32 是否打开评论，0不打开，1打开
     */
    public $need_open_comment;
    /**
     * Uint32 是否粉丝才可评论，0所有人可评论，1粉丝才可评论
     * @var int
     */
    public $only_fans_can_comment;
    
    function __construct(string $title,string $thumb_media_id,string $content,string $content_source_url=null,bool $show_cover_pic=true) {
        $this->title = $title;
        $this->content = $content;
        $this->content_source_url = $content_source_url;
        $this->show_cover_pic = $show_cover_pic?1:0;
        $this->thumb_media_id = $thumb_media_id;
        
    }
}

