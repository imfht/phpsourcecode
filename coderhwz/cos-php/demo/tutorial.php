<?php
date_default_timezone_set("Asia/Chongqing");
require_once("./../cos.class.php");
$secretId = "AKID123123nsdad12312hjkhdas2312";

$cos_obj = new Cos(COS_HOST, 1000264, "7stz/gpe8vO5JJuQ+/HGWi29",$secretId);  //新用户具有secretId
try{
    //	//cos_create_bucket($cos_obj);
    //	cos_list_bucket($cos_obj);
    //	cos_get_bucket_info($cos_obj);
    //	cos_set_bucket_info($cos_obj);
    //	cos_get_bucket_info($cos_obj);
    //	cos_mkdir($cos_obj);
    //	cos_getmeta($cos_obj);
    //	cos_setmeta($cos_obj);
    //	cos_getmeta($cos_obj);
    //	cos_upload_file_by_file($cos_obj); //030
    //	cos_upload_file_by_content($cos_obj); //031
    //	cos_multipart_upload($cos_obj); //032
    //cos_complete_multipart_upload($cos_obj);
    //	cos_listFile($cos_obj);		
    //	cos_rename($cos_obj); //031new
    //	cos_upload_content_with_compress($cos_obj);
    //	cos_upload_file_with_compress($cos_obj);
    //	cos_compress_file($cos_obj);
    //	cos_delete_file($cos_obj);
    //	cos_delete_dir($cos_obj);
    //	cos_delete_bucket($cos_obj);
    cos_get_download_url($cos_obj);

    //cos_delete_file($cos_obj);
    //cos_get_upload_url($cos_obj);


}catch (Exception $e){
    echo ($e->getMessage());
}
die;

function cos_create_bucket($cos_obj)
{
    echo "----------------cos_create_bucket------------\n";
    $bucketId = "bucket03";
    //$referer = "qq.com";
    //$acl = 0;
    $ret = $cos_obj->create_bucket($bucketId);
    var_dump($ret);

    $bucketId = "bucket04";
    //$referer = "qq.com";
    //$acl = 0;
    $ret = $cos_obj->create_bucket($bucketId);
    var_dump($ret);
}

function cos_delete_dir($cos_obj)
{
    echo "----------------cos_delete_dir------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $ret = $cos_obj->delete_dir($bucketId, $path);
    var_dump($ret);

    $bucketId = "bucket04";
    $path = "/path04";
    $ret = $cos_obj->delete_dir($bucketId, $path);
    var_dump($ret);
}

function cos_delete_bucket($cos_obj)
{
    echo "----------------cos_delete_bucket------------\n";
    $bucketId = "bucket03";
    $ret = $cos_obj->delete_bucket($bucketId);
    var_dump($ret);

    $bucketId = "bucket04";
    $ret = $cos_obj->delete_bucket($bucketId);
    var_dump($ret);
}

function cos_get_bucket_info($cos_obj)
{
    echo "----------------cos_get_bucket_info------------\n";
    $bucketId = "bucket03";
    $ret = $cos_obj->get_bucket_info( $bucketId);
    var_dump($ret);

    $bucketId = "bucket04";
    $ret = $cos_obj->get_bucket_info( $bucketId);
    var_dump($ret);
}
function cos_set_bucket_info($cos_obj)
{
    echo "----------------cos_set_bucket_info------------\n";
    $bucketId = "bucket04";
    $opt = array(
        'acl'=>1,
        'referer'=>"qzone.qq.com"
    );
    $ret = $cos_obj->set_bucket_info( $bucketId, $opt);
    var_dump($ret);

    $opt = array(
        'acl'=>0,
        'referer'=>""
    );
    $ret = $cos_obj->set_bucket_info( $bucketId, $opt);
    var_dump($ret);
}
function cos_list_bucket($cos_obj)
{
    echo "----------------cos_list_bucket------------\n";
    $offset = 0;
    $count = 10;
    //$prefix = "";
    $ret = $cos_obj->list_bucket($offset,$count);
    var_dump($ret);
}

function cos_mkdir($cos_obj)
{
    echo "----------------cos_mkdir------------\n";	
    $bucketId = "bucket03";
    $path = "/path03";
    $expires = 7200;
    $cacheControl = "max-age=7200";
    $contentEncoding = "utf-8";
    $contentLanguage = "zh-CN";
    $ret = $cos_obj->create_dir($bucketId, $path, $expires, $cacheControl,$contentEncoding,$contentLanguage);
    var_dump($ret);

    $bucketId = "bucket04";
    $path = "/path04";
    $expires = 7200;
    $cacheControl = "max-age=7200";
    $contentEncoding = "utf-8";
    $contentLanguage = "zh-CN";
    $ret = $cos_obj->create_dir($bucketId, $path, $expires, $cacheControl,$contentEncoding,$contentLanguage);
    var_dump($ret);

}
function cos_getmeta($cos_obj)
{
    echo "----------------cos_getmeta------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $ret = $cos_obj->get_meta($bucketId , $path);
    var_dump($ret);
}
function cos_setmeta($cos_obj)
{
    echo "----------------cos_setmeta------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $expires = 4444;
    $cacheControl = "max-age=7200";
    $contentEncoding = "utf-8";
    $contentLanguage = "zh-CN";
    $opt = array(
        'expires'=>4444,
        'cacheControl'=>'max-age=7200',
        'contentEncoding' => "utf-8",
        'contentLanguage' => "zh-CN",
    );
    $ret = $cos_obj->set_meta($bucketId, $path, $opt);
    var_dump($ret);
}
function cos_upload_file_by_file($cos_obj)
{
    echo "----------------cos_upload_file_by_file------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $filename = "/data/kevenzhou/src_data.jpg";
    $obj_name = "file030";
    $ret = $cos_obj->upload_file_by_file( $bucketId, $path, $obj_name, $filename);
    var_dump($ret);
}
function cos_get_download_url($cos_obj)
{
    echo "----------------cos_get_download_url------------\n";
    $bucketId = "bucket03";
    $path = "/path03/file032";
    $option = array(
        "res_cache_control"=>"",
        "res_content_disposition"=>"",
        "res_content_type"  =>	"",
        "res_encoding"	    => "",
        "res_expires"	    =>	"",
        "res_content_language"=> "",
    );
    $need_sig = true;
    $ret =$cos_obj->get_download_url($bucketId,$path,$need_sig);
    var_dump($ret);

    $bucketId = "ticket_attachment";
    $path = "/909619400_182_赵丽坤.jpg";
    $option = array(
        "res_cache_control"=>"",
        "res_content_disposition"=>"attachment%3Bfilename%3D%D5%D4%C0%F6%C0%A4.jpg",
        "res_content_type"  =>	"",
        "res_encoding"	    => "",
        "res_expires"	    =>	"",
        "res_content_language"=> "",
    );
    $need_sig = true;
    $ret =$cos_obj->get_download_url($bucketId,$path,$need_sig,$option);
    var_dump($ret);


    $bucketId = "bucket04";
    $path = "/path04/file034_zip.jpg";
    $option = array(
        "res_cache_control"=>"",
        "res_content_disposition"=>"",
        "res_content_type"  =>	"",
        "res_encoding"	    => "",
        "res_expires"	    =>	"",
        "res_content_language"=> "",
    );
    $need_sig = true;
    $ret =$cos_obj->get_download_url($bucketId,$path,$need_sig);
    var_dump($ret);
}
function cos_get_upload_url($cos_obj)
{
    echo "----------------cos_get_upload_url------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $cosFile = "file039";
    $ret =$cos_obj->get_upload_url($bucketId,$path,$cosFile);
    var_dump($ret);

}
function cos_upload_file_by_content($cos_obj)
{
    echo "----------------cos_upload_file_by_content------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $obj_name = "file031";
    $content = file_get_contents("/data/kevenzhou/src_data.jpg");
    $ret = $cos_obj->upload_file_by_content( $bucketId, $path, $obj_name, $content);
    var_dump($ret);
}
function cos_multipart_upload($cos_obj)
{
    echo "----------------cos_multipart_upload------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $file_path = "/data/kevenzhou/src_data.jpg";
    $storage_name = "file032";
    $fd = fopen($file_path,"rw");
    if($fd===false){
        echo "open file faied!";
        return false;
    }
    $max_part_size = 2097152; //50M n*64k
    $cur_read_num = 0;
    $part_content = null;
    $part_len = 0;
    $continue_process = true;
    $offset =0;
    $has_error = false;
    while($continue_process)
    {
        $piece = fread($fd,8192);
        $piece_len = strlen($piece);
        if($piece_len>0){
            $part_content .= $piece;
            $part_len += $piece_len;
        }
        if($part_len>=$max_part_size || feof($fd)){
            //process
            sleep(1);
            $ret = $cos_obj->multipart_upload( $bucketId, $path, $storage_name, $offset, $part_content);
            echo "part ".$offset." ".$part_len." content_len: ".strlen($part_content)."\n";
            var_dump($ret);
            if($ret['code']!=0){
                $has_error = true;
                $continue_process = false;
            }
            else if($ret['code'] == -24990)
            {
                //storage obj is exists / completed file
                var_dump($ret);
                return false;
            }
            $offset += $part_len;
            $part_content = null;
            $part_len = 0;
            if(feof($fd)){
                $continue_process = false;
            }
        }
    }
    fclose($fd);
    if(!$has_error){
        $obj_full_path = $path."/".$storage_name;
        $ret = $cos_obj->complete_multipart_upload( $bucketId, $obj_full_path);
        echo "complete:\n";
        var_dump($ret);
        if($ret['code'] ==0){
            echo "multipart upload success\n";
            return true;
        }
    }
    $ret = $cos_obj->delete_file( $bucketId, $storage_name ,$path);
    if($ret["code"] ==0){
        echo "multipart upload failed!,roll back suc.\n";
    }else{
        echo "multipart upload failed!,roll back failed! please delete obj ".$path."/".$storage_name."\n";
    }
    return false;
}
function cos_listFile($cos_obj)
{
    echo "----------------cos_listFile------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $offset =0;
    $count = 20;
    $prefix="";
    $ret = $cos_obj->list_file($bucketId , $path, $offset, $count,$prefix);
    var_dump($ret);
}
function cos_rename($cos_obj)
{
    echo "----------------cos_rename------------\n";
    $bucketId = "bucket03";
    $spath = "/path03/file031";
    $dpath = "/path03/file031new";
    $ret = $cos_obj->rename($bucketId, $spath, $dpath );
    var_dump($ret);
}
function cos_complete_multipart_upload($cos_obj)
{
    echo "----------------cos_complete_multipart_upload------------\n";
    $bucketId = "bucket03";
    $path = "/path03/file032";
    $ret = $cos_obj->complete_multipart_upload( $bucketId, $path);
    var_dump($ret);
}
function cos_delete_file($cos_obj)
{
    echo "----------------cos_delete_file------------\n";
    $bucketId = "bucket03";
    $path = "/path03";
    $file_list = array(
        'file030',
        'file031new',			
    );
    $ret = $cos_obj->delete_file( $bucketId, $path, $file_list );
    var_dump($ret);

    $file_list = array(
        "file031",
    );
    $ret = $cos_obj->delete_file( $bucketId, $path, $file_list );
    var_dump($ret);

    $file_list = array(
        "file032",
    );
    $ret = $cos_obj->delete_file( $bucketId, $path, $file_list );
    var_dump($ret);

    $file_list = array(
        "file033.jpg",
    );
    $ret = $cos_obj->delete_file( $bucketId, $path, $file_list );
    var_dump($ret);

    $file_list = array(
        "file034.jpg",
    );
    $ret = $cos_obj->delete_file( $bucketId, $path, $file_list );
    var_dump($ret);


    $bucketId = "bucket04";
    $path = "/path04";
    $file_list = array(
        'file033.jpg',
    );
    $ret = $cos_obj->delete_file( $bucketId, $path, $file_list );
    var_dump($ret);

    $file_list = array(
        'file034.jpg',
    );            
    $ret = $cos_obj->delete_file( $bucketId, $path, $file_list );
    var_dump($ret);

    $file_list = array(
        'file034_zip.jpg',
    );              
    $ret = $cos_obj->delete_file( $bucketId, $path, $file_list );
    var_dump($ret);
}


function cos_upload_file_with_compress($cos_obj)
{
    echo "----------------cos_upload_file_with_compress------------\n";
    $compressBucketId = "bucket04";
    $compressPath     = "/path04/file033.jpg";
    $fileName         = "/data/kevenzhou/src_data.jpg";

    $opt = array(
        'uploadBucketId'   => "bucket03",
        'uploadFilePath'       => "/path03/file033.jpg",
    );

    $ret = $cos_obj->upload_file_with_compress($compressBucketId,  
        $compressPath, $fileName, $opt);
    var_dump($ret);
}
function cos_upload_content_with_compress($cos_obj)
{
    echo "----------------cos_upload_content_with_compress------------\n";
    $compressBucketId = "bucket04";
    $compressPath     = "/path04/file034.jpg";
    $content = file_get_contents("/data/kevenzhou/src_data.jpg");

    $opt = array(
        'uploadBucketId'   => "bucket03",
        'uploadFilePath'       => "/path03/file034.jpg",
    );

    $ret = $cos_obj->upload_file_content_with_compress($compressBucketId, 
        $compressPath, $content, $opt);
    var_dump($ret);
}
function cos_compress_file($cos_obj)
{
    echo "----------------cos_compress_file------------\n";
    $srcBucketId = "bucket03";
    $dstBucketId = "bucket04";
    $srcPath = "/path03/file034.jpg";
    $dstPath = "/path04/file034_zip.jpg";

    $ret = $cos_obj->compress_online_file( $srcBucketId, $dstBucketId, $srcPath, $dstPath );
    var_dump($ret);
}





