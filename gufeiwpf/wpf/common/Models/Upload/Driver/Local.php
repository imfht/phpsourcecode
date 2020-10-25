<?php
namespace Wpf\Common\Models\Upload\Driver;
class Local extends \Phalcon\Mvc\Model{
    
    public function onConstruct($config = null){

    }
    /**
     * 保存指定文件
     * Local::save()
     * 
     * @param mixed $file
     * @param mixed $savefile
     * @return
     */
    public function savefile($file,$savefile) {
        
        if((! is_object($file)) || (! method_exists($file,"moveTo"))){
            $this->error = '原始文件错误';
            return false;
        }
        
        $savepath = dirname($savefile);
        if(!is_dir($savepath)){
            mkdir($savepath,0755,true);
        }
        
        
        if(! $file->moveTo($savefile)){
            $this->error = '文件上传保存错误！';
            return false;
        }
        
        return true;
    }
    
    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->error;
    }
}