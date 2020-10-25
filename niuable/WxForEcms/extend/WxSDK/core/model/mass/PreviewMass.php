<?php
namespace WxSDK\core\model\mass;

use WxSDK\core\model\Model;

class PreviewMass extends Model
{
    /**
     * 
     * @var string
     */
    public $towxname;
    /**
     * 
     * @var string
     */
    public $touser;
    /**
     * 
     * @var string
     */
    public $msgtype;
    /**
     * 
     * @var string
     */
    public $mpnews;
    /**
     * 
     * @var array
     */
    public $text;
    /**
     * 
     * @var array
     */
    public $voice;
    /**
     * 
     * @var array
     */
    public $image;
    /**
     * 
     * @var array
     */
    public $mpvideo;
    /**
     * 
     * @var array
     */
    public $wxcard;
}

