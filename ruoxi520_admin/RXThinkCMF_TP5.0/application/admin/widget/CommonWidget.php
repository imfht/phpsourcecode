<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 常用挂件
 * 
 * @author 牧羊人
 * @date 2018-12-11
 */
namespace app\admin\widget;
class CommonWidget extends BaseWidget
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 单图上传
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function uploadImg($name, $imgUrl, $size='90x90', $nameStr='', $sizeStr='', $cropSize='', $cropRate='')
    {
        $size 	= $size ? $size : '90x90'; //默认尺寸 90x90
        $nameStr = $nameStr ? $nameStr : "图片";
        $isCrop = $cropSize ? 1 : 2;
        $cropSize = $cropSize ? $cropSize : '300x300'; //默认裁剪尺寸 300x300
        $cropRate = $cropRate ? $cropRate : 1/1;
        
        //长宽
        $itemArr = explode('x', $size);
        //裁剪尺寸
        $cropArr = explode('x', $cropSize);
        
        $this->assign('name', $name);
        $this->assign('imgUrl', $imgUrl);
        $this->assign('img', str_replace(IMG_URL, '', $imgUrl));
        $this->assign('imgW', $itemArr[0]);
        $this->assign('imgH', $itemArr[1]);
        $this->assign('nameStr',$nameStr);
        $this->assign('sizeStr',$sizeStr);
        $this->assign('cropW',$cropArr[0]);
        $this->assign('cropH',$cropArr[1]);
        $this->assign('cropRate',$cropRate);
        $this->assign('isCrop',$isCrop);
        return $this->fetch("widget/upload_singleImg");
    }
    
    /**
     * 多图上传
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function uploadMultImg($name, $imageList, $imgMsg, $size, $maxNum=9)
    {
        //字段名称
        $name = isset($name) ? trim($name) : 'file';
        //长宽
        $size 	= isset($size) ? trim($size) : '100x100'; //图片尺寸  100 x 100
        $sizeArr = explode('x', $size);
        //最大上传张数
        $maxNum = $maxNum ? $maxNum : 5;//默认上传5张
        
        $this->assign('name', $name);
        $this->assign('maxNum',$maxNum);
        $this->assign('imgMsg',$imgMsg);
        $this->assign('imgW', $sizeArr[0]);
        $this->assign('imgH', $sizeArr[1]);
        $this->assign('imageList',$imageList);
        return $this->fetch("widget/upload_multipleImg");
    }
    
    /**
     * SWITCH组件开关
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function switchCheck($idStr, $textStr, $selectId)
    {
        //渲染界面
        $this->assign('idStr',$idStr);
        $this->assign('textStr',$textStr);
        $this->assign("selectId",$selectId);
        return $this->fetch("widget/switch_checked");
    }
    
    /**
     * 下拉单选
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function singleSelect($param, $list, $selectId)
    {
        $arr = explode('|', $param);
        
        //参数
        $idStr = $arr[0];
        $isV = $arr[1];
        $msg = $arr[2];
        $show_name = $arr[3];
        $show_value = $arr[4];
        
        $this->assign('idStr',$idStr);
        $this->assign('isV',$isV);
        $this->assign('msg',$msg);
        $this->assign('show_name',$show_name);
        $this->assign('show_value',$show_value);
        $this->assign('dataList',$list);
        $this->assign("selectId",$selectId);
        return $this->fetch("widget/single_select");
    }
    
    /**
     * 拼音转换
     * 
     * @author 牧羊人
     * @date 2018-12-11
     */
    function pinyin($pinyin, $code)
    {
        if($_GET['action'] == "getPinyin"){
            //TODO...
        }else{
            $this->assign('pinyin',$pinyin);
            $this->assign('code',$code);
            $this->display("Widget:pinyin");
        }
    }
    
}