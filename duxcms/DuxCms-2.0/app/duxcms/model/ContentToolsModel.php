<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 内容工具
 */
class ContentToolsModel {

    /**
     * 获取内容指定图片
     * @param string $content 内容
     * @param int $num 第N张图片
     * @return string 图片URL
     */
    public function getImage($content, $num = 1)
    {
        $content = html_out($content);
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"]/i', $content, $matches);
        $num = $num - 1;
        $img = $matches[1][$num];
        return $img;
    }

    /**
     * 获取关键词
     * @param string $title 标题
     * @param string $content 内容
     * @return string 图片URL
     */
    public function getKerword($title, $content = '')
    {
        $data= \framework\ext\Http::doGet('http://keyword.discuz.com/related_kw.html?ics=utf-8&ocs=utf-8&title='.urlencode($title).'&content='.urlencode($content),10);
        if(empty($data)){
            return;
        }
        preg_match_all("/<kw>(.*)A\[(.*)\]\](.*)><\/kw>/",$data, $list, PREG_SET_ORDER);
        if(empty($list)){
            return;
        }
        $keywords = array();
        foreach ($list as $value) {
            $keywords[] = $value[2];
        }
        return implode(',', $keywords);
        
    }

    /**
     * 远程抓图
     * @param string $content 内容
     * @return string 抓取后内容
     */
    public function getRemoteImage($content)
    {
        if(empty($content)){
            return $content;
        }
        $filesName = date('Y-m-d').'/';
        //文件路径
        $filePath = './uploads/'.$filesName;
        //文件URL路径
        $fileUrl = __ROOT__ .'/uploads/'. $filesName;
        $body=htmlspecialchars_decode($content);
        $imgArray = array();
        preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|bmp|png))/isU",$body,$imgArray);
        $imgArray = array_unique($imgArray[2]);
        set_time_limit(0);
        $milliSecond = date("dHis") . '_';
        if(!is_dir($filePath)) @mkdir($filePath,0777,true);
        $http = new \framework\ext\Http;
        foreach($imgArray as $key =>$value)
        {
            $value = trim($value);
            $ext=explode('.', $value);
            $ext=end($ext);
            $getFile = $http->doGet($value,5);
            $getfileName = $milliSecond.$key.'.'.$ext;
            $getFilePath = $filePath.$getfileName;
            $getFileUrl = $fileUrl.$getfileName;
            if($getFile){
                if(@file_put_contents($getFilePath, $getFile)){
                    $body = str_replace($value,$getFileUrl,$body);
                }
            }
            
        }
        return $body;

    }

}
