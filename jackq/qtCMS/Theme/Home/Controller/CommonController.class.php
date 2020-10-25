<?php
namespace Home\Controller;

use Think\Controller;

/**
 * CommonController
 * 通用控制器
 */
class CommonController extends Controller
{
    /**
     * 全局初始化
     * @return
     */
    public function _initialize()
    {
        // utf-8编码
        header('Content-Type: text/html; charset=utf-8');
        $siteinfo = session('__siteinfo__');
        if (empty($siteinfo)) {
            $siteinfo = M('Siteinfo')->limit(1)->select();
            session('__siteinfo__', $siteinfo[0]);
        }
        //echo($siteinfo['seo_keywords']);
    }


    /**
     * 空操作
     * @return
     */
    public function _empty()
    {
        $this->error('404,您访问的页面不存在！');
    }

    /**
     * { status : true, info: $info}
     * @param  string $info
     * @param  string $url
     * @return
     */
    protected function successReturn($info, $url=null) {
        $this->resultReturn(true, $info, $url);
    }

    /**
     * { status : false, info: $info}
     * @param  string $info
     * @param  string $url
     * @return
     */
    protected function errorReturn($info, $url=null) {
        $this->resultReturn(false, $info, $url);
    }

    /**
     * 返回带有status、info键值的json数据
     * @param  boolean $status
     * @param  string $info
     * @param  string $url
     * @return
     */
    protected function resultReturn($status, $info, $url) {
        $json['status'] = $status;
        $json['info'] = $info;
        $json['url'] = isset($url) ? $url : '';

        return $this->ajaxReturn($json);
    }

    /**
     * 下载文件
     * @param  文件路径 $filePath
     * @param  文件名称 $fileName
     * @return
     */
    protected function download($filePath, $fileName) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; '
            . 'filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    }

}
