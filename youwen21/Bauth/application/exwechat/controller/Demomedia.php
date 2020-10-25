<?php

namespace app\exwechat\controller;

use youwen\exwechat\api\media\media;
use think\Controller;
/**
 * 多媒体案例
 * @author baiyouwen <youwen21@yeah.net>
 */
class Demomedia
{
    // 添加图文消息中的图片
    public function news_uploadimg()
    {
        $class = new media($_GET['token']);
        $ret = $class->news_uploadimg($_GET['img']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 编辑图文消息
    public function update_news()
    {
        $data = [
                "title"=> '永久图文－爱你爱翻天',
                "thumb_media_id"=> '2l6HDOnKdL_nRpmM1svLC_iL2i7dRxREGiT2JC3tKgU',
                "author"=> 'youwen21@yeah.net',
                "digest"=> '描述不能是哈哈',
                "show_cover_pic"=> 1,  //   是否显示封面，0为false，即不显示，1为true，即显示
                "content"=> '这是个一永久图文， 图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,涉及图片url必须来源"上传图文消息内的图片获取URL"接口获取。外部图片url将被过滤。',
                "content_source_url"=> 'http://demo.exwechat.com'
            ];
        $news['media_id'] = '2l6HDOnKdL_nRpmM1svLCy3vdrvmeT2WoC7PLyHIJQI';
        $news['index'] = 0;
        $news['articles'] = $data;

        $class = new media($_GET['token']);
        $ret = $class->update_news($news);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 添加图片消息
    public function add_news()
    {
        $data = [];
        $data = [
            [
                "title"=> '永久图文－爱你爱翻天',
                "thumb_media_id"=> '2l6HDOnKdL_nRpmM1svLC_iL2i7dRxREGiT2JC3tKgU',
                "author"=> 'youwen21@yeah.net',
                "digest"=> '哈哈',
                "show_cover_pic"=> 1,  //   是否显示封面，0为false，即不显示，1为true，即显示
                "content"=> '这是个一永久图文， 图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,涉及图片url必须来源"上传图文消息内的图片获取URL"接口获取。外部图片url将被过滤。',
                "content_source_url"=> 'http://demo.exwechat.com'
            ]
        ];
        $news['articles'] = $data;
        $class = new media($_GET['token']);
        $ret = $class->add_news($news);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 删除永久素材
    public function del_material()
    {
        $class = new media($_GET['token']);
        $ret = $class->del_material($_GET['media_id']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 获取素材列表
    public function batchget_material()
    {
        $class = new media($_GET['token']);
        $ret = $class->batchget_material($_GET['type'], $_GET['offset'], $_GET['count']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 获取各素材总数统计
    public function get_materialcount()
    {
        $class = new media($_GET['token']);
        $ret = $class->get_materialcount();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 获取永久素材文件
    public function get_material()
    {
        $class = new media($_GET['token']);
        $ret = $class->get_material($_GET['media_id']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 上传永久素材
    public function add_material()
    {
        $class = new media($_GET['token']);
        $ret = $class->add_material($_GET['img']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 下载临时素材
    public function tempGet()
    {
        $class = new media($_GET['token']);
        $ret = $class->tempGet($_GET['media_id']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
    // 上传临时素材
    public function tempUpload()
    {
        $class = new media($_GET['token']);
        $ret = $class->tempUpload($_GET['img']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }
}
