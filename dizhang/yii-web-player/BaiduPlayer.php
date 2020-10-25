<?php
/**
 * BaiduPlayer.php
 *
 * @author Di Zhang <zhangdi_me@163.com>
 */

require(dirname(__FILE__).'/BasePlayer.php');

/**
 * Class BaiduPlayer
 *
 * 百度影音播放器
 *
 * @property $url string 视频地址，如：bdhd://301568740|FCDAC749BD2BB0C009A32ABB4AF428D7|宫锁心玉01.rmvb
 */
class BaiduPlayer extends BasePlayer
{
    /**
     * @var array 播放器选项，参数见：{@link http://player.baidu.com/diaoyongnew.html}
     */
    public $options = array(
        'time' => 0, // 缓冲广告展示时间(如果设为0,则根据缓冲进度自动控制广告展示时间)
        'buffer' => 'http://player.baidu.com/lib/show.html?buffer', // 贴片广告网页地址
        'pause' => 'http://player.baidu.com/lib/show.html?pause', // 暂停广告网页地址
        'end' => 'http://player.baidu.com/lib/show.html?end', // 影片播放完成后加载的广告
        'download' => 'http://player.baidu.com/yingyin.html', // 播放器下载地址
        'tn' => '12345678', // 播放器下载地址渠道号
        'width' => 800, // 播放器宽度(只能为数字)
        'height' => 550, // 播放器高度(只能为数字)
        'showclient' => 1, // 是否显示拉起拖盘按钮(1为显示 0为隐藏)
        'url' => '', // 当前播放任务播放地址
        'nextcacheurl' => '', // 下一集播放地址(没有请留空)
        'lastwebpage' => '', // 上一集网页地址(没有请留空)
        'nextwebpage' => '', // 下一集网页地址(没有请留空)
    );

    public function init()
    {
        parent::init();

        $this->options['url'] = $this->url;

        $options = array();
        foreach ($this->options as $key => $value) {
            if (is_int($value)) {
                $options[] = "BdPlayer['{$key}']={$value}";
            } else {
                $options[] = "BdPlayer['{$key}']='{$value}'";
            }
        }

        $optionsString = implode("\n", $options);

        echo <<<EOF
        <script language="javascript">
            var BdPlayer = new Array();
            {$optionsString}
        </script>
EOF
        ;
    }

    public function run()
    {
        echo '<script language="javascript" src="http://player.baidu.com/lib/player.js" charset="utf-8"></script>';
    }
}