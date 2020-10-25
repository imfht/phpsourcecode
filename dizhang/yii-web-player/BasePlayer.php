<?php
/**
 * BasePlayer.php
 * 
 * @author Di Zhang <zhangdi_me@163.com>
 */

/**
 * Class BasePlayer
 *
 * 播放器基类
 *
 */
class BasePlayer extends CWidget{

    /**
     * @var int 播放器宽度
     */
    public $width = 600;

    /**
     * @var int 播放器高度
     */
    public $height = 400;

    /**
     * @var string 需要播放的视频 URL
     */
    public $url;

    /**
     * @var array 播放器参数
     */
    public $options = array();

    /**
     * 初始化
     *
     * @throws CException
     */
    public function init(){
        parent::init();

        if (empty($this->url)) {
            throw new CException('请配置需要百度影音播放的 URL 地址，如: bdhd://301568740|FCDAC749BD2BB0C009A32ABB4AF428D7|宫锁心玉01.rmvb');
        }
    }
}