CKEditor 4
==========

Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.  
http://ckeditor.com - See LICENSE.md for license information.

CKEditor is a text editor to be used inside web pages. It's not a replacement
for desktop text editors like Word or OpenOffice, but a component to be used as
part of web applications and websites.

## Documentation

The full editor documentation is available online at the following address:
<http://docs.ckeditor.com>

## Installation

Installing CKEditor is an easy task. Just follow these simple steps:

 1. **Download** the latest version from the CKEditor website:
    http://ckeditor.com. You should have already completed this step, but be
    sure you have the very latest version.
 2. **Extract** (decompress) the downloaded file into the root of your website.
 3. **引入与基本用法**

```
	<!-- 引入入 ckeditor 文件 -->
    <script src="../../includes/ckeditor/ckeditor.js"></script>
    <!-- 添加 jQuery 支持 -->
    <script src="/ckeditor/adapters/jquery.js"></script>
    
   	<!-- 插件初始化 js -->
    $('#goods_desc').ckeditor({
        // filebrowserBrowseUrl:'http://www.baidu.com',
        // filebrowserImageUploadUrl: '/admin/upload.php',
        filebrowserUploadUrl: '/admin/upload.php',
    });
    
    <!-- html 主体 -->
    <textarea name="goods_desc" id="goods_desc" rows="20"></textarea>  
    
    // PHP
    // 服务器端返回 处理格式
    public function ckeditor($url,$message){
        $FuncNum = $_GET['CKEditorFuncNum'];
        $str = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(%s,'%s','%s')</script>";
        return sprintf($str,$FuncNum,$url,$message);
    }
    
    <!--获取表单内容 js-->
    $("#goods_desc").val(); //可编辑区域的内容和此处获得到的内容是不一样的哈。
```

**Note:** CKEditor is by default installed in the `ckeditor` folder. You can
place the files in whichever you want though.


## Change Log
* 2014-08-11 02:21:19 添加模仿微信编辑器插入视频插件，插件名：txvideo