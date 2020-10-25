<?php

namespace Addons\ImageSlider;

use Common\Controller\Addon;

/**
 * 图片轮播插件
 * @author birdy
 */
class ImageSliderAddon extends Addon
{

    public $info = array(
        'name' => 'ImageSlider',
        'title' => '图片轮播',
        'description' => '图片轮播',
        'status' => 1,
        'author' => 'birdy',
        'version' => '0.2'
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的pageFooter钩子方法
    public function imageSlider($param)
    {
        $config = $this->getConfig();
        if ($config['images']) {
            $images = M("Picture")->field('id,path')->where("id in ({$config['images']})")->select();
            
            
            
            $this->assign('urls', explode("\r\n", $config['url']));
            foreach ($images as &$img) {
                $img['path'] = fixAttachUrl($img['path']);
            }
            
            unset($img);
        
            $this->assign('images', $images);
            
            $config['imgWidth']=$config['imgWidth']+8;
            $config['imgHeight']=$config['imgHeight']+8;
            $this->assign('config', $config);
            $this->display('content');
        }
    }

    
}