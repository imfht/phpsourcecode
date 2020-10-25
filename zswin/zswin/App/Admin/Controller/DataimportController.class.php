<?php
namespace Admin\Controller;
use Think\Db;
use OT\Database;

/**
 * 数据库备份还原控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class DataimportController extends CommonController{

  public function index($type = null){
        
                //列出备份文件列表
                $path = realpath(C('DATA_BACKUP_PATH'));
                $flag = \FilesystemIterator::KEY_AS_FILENAME;
                $glob = new \FilesystemIterator($path,  $flag);

                $list = array();
                foreach ($glob as $name => $file) {
                    if(preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)){
                        $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

                        $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                        $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                        $part = $name[6];

                        if(isset($list["{$date} {$time}"])){
                            $info = $list["{$date} {$time}"];
                            $info['part'] = max($info['part'], $part);
                            $info['size'] = $info['size'] + $file->getSize();
                        } else {
                            $info['part'] = $part;
                            $info['size'] = $file->getSize();
                        }
                        $extension        = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
                        $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
                        $info['time']     = strtotime("{$date} {$time}");

                        $list["{$date} {$time}"] = $info;
                    }
                }
                $title = '数据还原';
             
        //渲染模板
        
        $this->assign('list', $list);
        $this->display();
    }
    /**
     * 删除备份文件
     * @param  Integer $time 备份时间
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del($time = 0){
        if($time){
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path  = realpath(C('DATA_BACKUP_PATH')) . DIRECTORY_SEPARATOR . $name;
            array_map("unlink", glob($path));
            if(count(glob($path))){
              $this->mtReturn(300, '备份文件删除失败，请检查权限！');
              
            } else {
            	 
             $this->mtReturn(201, '备份文件删除成功！','','forward',U('index'));
            }
        } else {
        	
           $this->mtReturn(300, '参数错误！');
        }
        
        
    }
    
    /**
     * 还原数据库
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
public function import($time = 0, $part = null, $start = null){
        if(is_numeric($time) && is_null($part) && is_null($start)){ //初始化
            //获取备份文件信息
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path  = realpath(C('DATA_BACKUP_PATH')) . DIRECTORY_SEPARATOR . $name;
            $files = glob($path);
            $list  = array();
            foreach($files as $name){
                $basename = basename($name);
                $match    = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz       = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }
            ksort($list);

            //检测文件正确性
            $last = end($list);
            if(count($list) === $last[0]){
                session('backup_list', $list); //缓存备份列表
                
                
                
                $this->success('初始化完成！', '', array('part' => 1, 'start' => 0));
                
         
            } else {
           
                $this->error('备份文件可能已经损坏，请检查！');
            }
           
        } elseif(is_numeric($part) && is_numeric($start)) {
        	
            $list  = session('backup_list');
            
            
            $db = new Database($list[$part], array(
                'path'     => realpath(C('DATA_BACKUP_PATH')) . DIRECTORY_SEPARATOR,
                'compress' => $list[$part][2]));
           
            $start = $db->import($start);
           
            if(false === $start){
                $this->error('还原数据出错！');
              
            } elseif(0 === $start) { //下一卷
                if(isset($list[++$part])){
                    $data = array('part' => $part, 'start' => 0);
                    $this->success("正在还原...#{$part}", '', $data);
                    
                   
                    
                } else {
                    session('backup_list', null);
                   
			        
                    $this->success('还原完成！');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if($start[1]){
                    $rate = floor(100 * ($start[0] / $start[1]));
                    $this->success("正在还原...#{$part} ({$rate}%)", '', $data);
               
                } else {
                    $data['gz'] = 1;
                 
                    $this->success("正在还原...#{$part}", '', $data);
                }
            }
           
        } else {
        	
            $this->error('参数错误！');
            
        }
        
    }
   

}
?>