<?php

/**
 * SAEAssetsAction class file.
 * 根据传递的path在web上显示源文件。
 */
class SAEAssetsAction extends CAction
{
	/**
	 * Runs the action.
	 */
	public function run()
	{
        $asset = $_GET['path'];
        #修改为action读取文件资料
        $SAECommon = new SAECommon();
        $path = $SAECommon->saedisk_decrypt($asset);
        if(!file_exists($path))
        {
			throw new CHttpException(404,Yii::t('yii','The asset "{asset}" to be published does not exist.',
				array('{asset}'=>$asset)));
        }
        // 浏览器根据etag来缓存，增加 date('H') 则为一小时更新
        $etag = md5($path + date('d'));

        header("ETag: {$etag}");

        $offset = 60 * 60 * 24;//css文件的距离现在的过期时间，这里设置为一天
        $expire = "expires: " . gmdate ("D, d M Y H:i:s", time() + $offset) . " GMT";
        header ($expire);
        $type = CFileHelper::getMimeType($path);

        header ("content-type: {$type}; charset: UTF-8");//注意修改到你的编码
        #header ("cache-control: max-age=$offset,must-revalidate");
        header ("cache-control: max-age=$offset");
        #header ("Pragma:");
        #print_r($_SERVER['HTTP_IF_NONE_MATCH']);die;
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) AND $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) 
        {
            #header('HTTP/1.1 304 Not Modified');
            header('Etag:'.$etag,true,304);
        }
        else
        {
            if(extension_loaded('zlib')){//检查服务器是否开启了zlib拓展
	            ob_start('ob_gzhandler');
            }
            //加载文件 
            include($path);
            if(extension_loaded('zlib'))
            {
	           ob_end_flush();//输出buffer中的内容，即压缩后的css文件
            }
        }
        exit();
	}
}
