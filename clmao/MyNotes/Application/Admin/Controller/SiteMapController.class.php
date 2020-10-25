<?php

namespace Admin\Controller;

use Think\Controller;

class SiteMapController extends CommonController {

    //生成XML地图
    public function createXML() {
        header('Content-type: application/xml');
        // $allId = $this->art->all_content_id();
        $allId = M('content')->field('id,title,time')->where(array('status' => 1))->order('id desc')->limit(500)->select();
        $cId = M('category')->field('id,title')->order('id desc')->select();
        //$cId = $this->category->get_all_category();
        $data_array = array();
        $data_array[0]['loc'] = U('/');
        $data_array[0]['lastmod'] = date('Y-m-d\TH:i:s', time()) . "+00:00";
        $data_array[0]['changefreq'] = 'daily';
        $data_array[0]['priority'] = '1.0';
        foreach ($allId as $k => $v) {
            $data_array[$k + 1]['loc'] = U('Admin/Index/content', array('id' => $v['id']));
            $data_array[$k + 1]['lastmod'] = date('Y-m-d\TH:i:s', $v['time']) . "+00:00";
            $data_array[$k + 1]['changefreq'] = 'monthly';
            $data_array[$k + 1]['priority'] = '0.6';
        }
        foreach ($cId as $k => $v) {
            $data_array[] = array(
                'loc' => U('Admin/Index/category', array('c_id' => $v['id'])),
                'lastmod' => date('Y-m-d\TH:i:s', time()) . "+00:00",
                'changefreq' => 'Weekly',
                'priority' => '0.1',
            );
        }
        //  属性数组 

        $attribute_array = array(
        );
        //  创建一个XML文档并设置XML版本和编码。。 
        $dom = new \DomDocument('1.0', 'utf-8');
        //  创建根节点 
        $article = $dom->createElement('urlset');
        $article->SetAttribute("xmlns:xsi", 'http://www.w3.org/2001/XMLSchema-instance');
        $article->SetAttribute("xmlns", 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $article->SetAttribute("xsi:schemaLocation", 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
        $dom->appendchild($article);
        foreach ($data_array as $data) {
            $item = $dom->createElement('url');
            $article->appendchild($item);
            create_item($dom, $item, $data, $attribute_array);
        }
        //echo $dom;
        $dom->save("sitemap.xml");
        if (I('get.act')) {
            $this->success('地图更新成功', U('/Admin/Admin/main'));
        }
    }

    /*
     * 生成站点地图HTML
     */

    public function createHtml() {
        $allId = M('content')->field('id,title,time')->where(array('status' => 1))->order('id desc')->LIMIT(100)->select();
        $cId = M('category')->field('id,title')->order('id desc')->select();
        $data_array = array();
        //print_r($allId);print_r($cId);
        foreach ($allId as $k => $v) {
            $data_array[0][] = '<li><a href="' . U('Home/Index/content', array('id' => $v['id'])) . '">' . $v['title'] . '</a></li>';
        }
        foreach ($cId as $k => $v) {
            $data_array[1][] = '<li><a href="' . U('Home/Index/category', array('c_id' => $v['id'])) . '">' . $v['title'] . '</a></li>';
        }
        //print_r($data_array);
        $str = '<!doctype html><html><head><meta name="HandheldFriendly" content="true"><meta name="MobileOptimized" content="320"><meta http-equiv="Cache-Control" content="no-transform" /><meta http-equiv="Cache-Control" content="no-siteapp"/><meta  name="viewport"  content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"><meta  name="format-detection"  content="telephone=no"><meta charset="utf-8"><meta name="keywords" content="站点地图," /><meta name="author" content="blog.clmao.com" /><meta name="copyright" content="blog.clmao.com" /><title>站点地图</title><style type="text/css">body {font-family: Verdana;FONT-SIZE: 12px;MARGIN: 0;color: #000000;background: #ffffff;}nav{ width:90%; margin-left:5%;display:block; height:20px; line-height:20px; border:#CCC solid 1px;  padding-left:20px;}section{display:block; margin-top:20px; line-height:20px; border:#CCC solid 1px; width:90%; margin-left:5%; padding-left:20px;}li{ padding:0px; margin:0px; list-style:none; }a{ color:#000; text-decoration:none;}a:hover{ color:#F00;}ul{padding:0px; margin:0px;padding-bottom:5px;}h2{ margin-left:5%;}footer{ width:100%; text-align:center; margin-top:50px;}</style></head><body><h2>';
        $homeUrl = U('/');
        $str = $str . getSiteOption('siteName') . ' 站点地图</h2><nav><a href="' . $homeUrl . '">' . getSiteOption('siteName') . '</a>' . ' >> <a href="';
        $str = $str . $homeUrl . 'sitemap.html">站点地图</a></nav><section><strong>最新文章</strong><ul>';
        foreach ($data_array[0] as $value) {
            $str.=$value;
        }
        $str.='</ul></section><section><strong>分类目录</strong><ul>';
        foreach ($data_array[1] as $value) {
            $str.=$value;
        }
        $str.='</ul></section><footer>Powered by <a href="http://blog.clmao.com">Clmao</a></footer></body></html>';
        file_put_contents('sitemap.html', $str);
        if (I('get.act')) {
            $this->success('地图更新成功', U('/Admin/Admin/main'));
        }
    }

}
