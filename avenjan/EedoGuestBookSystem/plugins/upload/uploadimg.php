<?php
header("Content-Type:text/html;charset=utf-8");
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
//上传文件目录获取
$month = date('Ym',time());
define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
$dir = BASE_PATH."../../upload/".$month."/";
//生成随机文件名函数       
  function random($length)   
  {   
    $hash = 'eedo.net-';   
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';   
    $max = strlen($chars) - 1;   
    mt_srand((double)microtime() * 1000000);   
      for($i = 0; $i < $length; $i++)   
      {   
        $hash .= $chars[mt_rand(0, $max)];   
      }   
    return $hash;   
  }  
  $filename=explode(".",$_FILES['file']['name']);   
  $filename[0]=random(5); //设置随机数长度   
  $name=implode(".",$filename);   
  //$name1=$name.".Mcncc";   
  $uploadfile=$dir.$name;   
//初始化返回数组
  $arr = array(
  'code' => 0,
  'msg'=> '',
  'data' =>array(
       'src' => $system_domain.'upload/'.$month."/".$name,
       'title' => $name
       ),
  );
$file_info = $_FILES['file'];
$file_error = $file_info['error'];
if(!is_dir($dir))//判断目录是否存在
{
    mkdir ($dir,0777,true);//如果目录不存在则创建目录
};
$file = $dir.$_FILES["file"]["name"];
if(!file_exists($file))
{
if($file_error == 0){
        if(move_uploaded_file($_FILES["file"]["tmp_name"],$uploadfile)){
           $arr['msg'] ="上传成功";
        }else{
           $arr['msg'] = "上传失败";
        }
    }else{
        switch($file_error){
            case 1:
           $arr['msg'] ='上传文件超过了PHP配置文件中upload_max_filesize选项的值';
                break;
            case 2:
              $arr['msg'] ='超过了表单max_file_size限制的大小';
                break;
            case 3:
               $arr['msg'] ='文件部分被上传';
                break;
            case 4:
              $arr['msg'] ='没有选择上传文件';
                break;
            case 6:
                $arr['msg'] ='没有找到临时文件';
                break;
            case 7:
            case 8:
               $arr['msg'] = '系统错误';
                break;
        }
    }
}
else
{
   $arr['code'] ="1"; 
  $arr['msg'] = "当前目录中，文件".$file."已存在";
}
  echo json_encode($arr);
?>