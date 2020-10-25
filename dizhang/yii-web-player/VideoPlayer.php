<?php
/**
 * VideoPlayer.php
 *
 * @author Di Zhang <zhangdi_me@163.com>
 */

require(dirname(__FILE__) . '/BasePlayer.php');

/**
 * Class VideoPlayer
 *
 * 通用视频播放器，适用于优酷，搜狐，土豆等
 *
 */
class VideoPlayer extends BasePlayer
{
    public $defaultOptions = array(
        'allowFullScreen' => 'true',
        'allowScriptAccess' => 'always',
        'wmode' => 'transparent',
        'quality' => 'high',
        'autoplay' => 'false',
        'vars' => '',
        'mode' => 'transparent'
    );

    public function init()
    {
        parent::init();

        echo CHtml::openTag('object', array('width' => $this->width, 'height' => $this->height));

        echo CHtml::tag('param', array('name' => 'movie', 'value' => $this->url));
        $options = CMap::mergeArray($this->defaultOptions, $this->options);
        foreach ($options as $name => $value) {
            echo CHtml::tag('param', array('name' => $name, 'value' => $value));
        }

        echo CHtml::tag('embed', CMap::mergeArray($options, array(
            'src' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
            'type' => "application/x-shockwave-flash"
        )));
    }

    public function run()
    {
        echo CHtml::closeTag('object');
    }
}