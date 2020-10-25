<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 内容AJAX处理工具
 */
class ContentToolsController extends AdminController
{
    /**
     * 获取内容图片
     */
    public function getImage()
    {
        $html = request('post.content');
        $num = request('post.num', 1);
        if (empty($html))
        {
            $this->error('未发现您输入的内容');
        }
        $image = target('ContentTools')->getImage($html, $num);
        if (empty($image))
        {
            $this->error('请插入图片后才能进行提取');
        }
        $this->success($image);
    }

    /**
     * 内容分词
     */
    public function getKerword()
    {
        $title = request('post.title');
        $content = request('post.content');
        if (empty($title) && empty($content))
        {
            $this->error('请先填写标题或内容');
        }
        $keyword = target('ContentTools')->getKerword($title, $content);
        if (empty($keyword))
        {
            $this->error('没有分析到关键词');
        }
        $this->success($keyword);
    }

    /**
     * 远程存图
     */
    public function getRemoteImage()
    {
        $content = request('post.content');
        if (empty($content))
        {
            $this->error('未发现内容');
        }
        $content = target('ContentTools')->getRemoteImage($content);
        $this->success($content);
    }
}

