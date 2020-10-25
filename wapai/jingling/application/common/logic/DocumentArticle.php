<?php
// +----------------------------------------------------------------------
// |   精灵后台系统 [ 基于TP5，快速开发web系统后台的解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 - 2017 http://www.apijingling.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wapai 邮箱:wapai@foxmail.com
// +----------------------------------------------------------------------
namespace app\common\logic;
use think\Model;

/**
 * 文档模型子模型 - 文章模型
 */
class DocumentArticle extends Base{  

    /**
     * 获取文章的详细内容
     * @return boolean
     * @author wapai   邮箱:wapai@foxmail.com
     */
    protected function getContent(){
        $type = input('post.type');
        $content = input('post.content');
        if($type > 1){//主题和段落必须有内容
            if(empty($content)){
                return false;
            }
        }else{  //目录没内容则生成空字符串
            if(empty($content)){
                $_POST['content'] = ' ';
            }
        }
        return true;
    }

}
