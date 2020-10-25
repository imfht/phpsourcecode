<?php

namespace app\admin\logic;
use Think\Db;
/**
 * 数据备份还原逻辑
 */
class Database extends AdminBase
{
    
    // 会员模型
    public static $databaseModel = null;
    public static $path;
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        $this->path=config('DATA_BACKUP_PATH');
       
    }
    
    /**
     * 获取数据表信息
     */
    public function getDatabaseList()
    {
        $list = Db::query('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case', $list);
        return $list;
    }
    public function importList()
    {
          
                $path = realpath($this->path);
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
                krsort($list);
        return $list;
    }
     public function optimize($tables)
    {
    	 
       if (!empty($tables)) {
       	if (is_array($tables['tables'])) {
       		$tables = implode('`,`', $tables['tables']);
       	}else{
       		$tables=$tables['tables'];
       	}
           
            $list = Db::query("OPTIMIZE TABLE `{$tables}`");
            if ($list) {
               // $this->success("数据表优化完成！");
              
                return [RESULT_SUCCESS, '数据表优化完成'];
            } else {
               // $this->error("数据表优化出错请重试！");
               
                return [RESULT_ERROR, '数据表优化出错请重试'];
            }
        } else {
            //$this->error("请指定要优化的表！");
           
             return [RESULT_ERROR, '请指定要优化的表'];
        }

   
    } 
         public function repair($tables)
    {
    	
     if (!empty($tables)) {
       	if (is_array($tables['tables'])) {
       		$tables = implode('`,`', $tables['tables']);
       	}else{
       		$tables=$tables['tables'];
       	}
            $list = Db::query("REPAIR TABLE `{$tables}`");
            if ($list) {
              return [RESULT_SUCCESS, '数据表修复完成'];
              
            } else {
               
               
                 return [RESULT_ERROR, '数据表修复出错请重试'];
            }
        } else {
           // $this->error("请指定要优化的表！");
            return [RESULT_ERROR, '请指定要修复的表'];
        }
    } 
    
public function export($param)
    {
    	if (!empty($param['tables'])) {
            $tables = $param['tables'];
            if (!is_dir($this->path)) {
            	mkdir($path, 0755, true);
            }
            $config = array('path' => realpath($this->path) . DS,
            		'part' => config('DATA_BACKUP_PART_SIZE'),
            		'compress' => config('DATA_BACKUP_COMPRESS'),
            		'level' => config('DATA_BACKUP_COMPRESS_LEVEL'));
           
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
              return [RESULT_ERROR, '检测到有一个备份任务正在执行，请稍后再试！'];
              
            } else {
                file_put_contents($lock, time());
            }
            if(!is_writeable($config['path'])){
            	return [RESULT_ERROR, '备份目录不存在或不可写，请检查后重试！'];
            	
            }
            session('backup_config', $config);
            $file = ['name' => date('Ymd-His', time()), 'part' => 1];
            session('backup_file', $file);
            session('backup_tables', $tables);
          
            $Databack = new \org\Databack($file, $config);
            if (false !== $Databack->create()) {
            	$tab = array('id' => 0, 'start' => 0);
            	 return [RESULT_SUCCESS, '初始化成功','',array('tables' => $tables, 'tab' => $tab)];
              
            } else {
            	return [RESULT_ERROR, '初始化失败，备份文件创建失败！'];
            
            }
        } elseif (isset($param['id']) && isset($param['start'])) {
            $tables = session('backup_tables');
            $id = intval($param['id']);
            $start = intval($param['start']);
            $Databack = new \org\Databack(session('backup_file'), session('backup_config'));
            $r = $Databack->backup($tables[$id], $start);
            if (false === $r) {
                return [RESULT_ERROR, '备份出错！'];
            
            } elseif (0 === $r) {
                if (isset($tables[++$id])) {
                	$tab = array('id' => $id, 'start' => 0);
                	
                	
                	return [RESULT_SUCCESS, '备份完成！','',array('tab' => $tab)];
                	
                  
                } else {
                    @unlink(session('backup_config.path') . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                   
                    return [RESULT_SUCCESS, '备份完成！'];
                }
            } else {
                $rate = floor(100 * ($r[0] / $r[1]));
                $tab  = array('id' => $id, 'start' => $r[0]);
                return [RESULT_SUCCESS,"正在备份...({$rate}%)",'',array('tab' => $tab)];
             
                
            }
        } else {
        	return [RESULT_ERROR, '请指定要备份的表！'];
        	
        }
    }
    /**
     * 删除备份文件
     * @param  Integer $time 备份时间
     */
    public function deleteBak($time) {
    	if ($time) {
    		$name = date('Ymd-His', $time) . '-*.sql*';
    		$path = realpath($this->path) . DIRECTORY_SEPARATOR . $name;
    		array_map("unlink", glob($path));
    		if (count(glob($path))) {
    			
    			return [RESULT_ERROR, '备份文件删除失败，请检查权限！'];
    		} else {
    			
    			return [RESULT_SUCCESS,'备份文件删除成功'];
    		}
    	} else {
    		
    		return [RESULT_ERROR, '参数错误！'];
    	}
    }

    public function import($time, $part , $start)
    {
    	if (is_numeric($time) && is_null($part) && is_null($start)) { 
			//初始化
			
			//获取备份文件信息
			$name  = date('Ymd-His', $time) . '-*.sql*';
			$path  = realpath($this->path) . DIRECTORY_SEPARATOR . $name;
			$files = glob($path);
			
			$list  = array();
			foreach ($files as $name) {
				$basename        = basename($name);
				$match           = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
				$gz              = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
				$list[$match[6]] = array($match[6], $name, $gz);
			}
			ksort($list);
			//检测文件正确性
			$last = end($list);
		
			if (count($list) === $last[0]) {
				session('backup_list', $list); //缓存备份列表
				return [RESULT_SUCCESS,'初始化完成！', '', array('part' => 1, 'start' => 0)];
			} else {
				return [RESULT_ERROR, '备份文件可能已经损坏，请检查！'];
			}
		} elseif (is_numeric($part) && is_numeric($start)) {
			$list = session('backup_list');

			$db = new \org\Databack($list[$part], array('path' => realpath($this->path) . DIRECTORY_SEPARATOR, 'compress' => $list[$part][2]));

			$r = $db->import($start);

			if (false === $r) {
				return [RESULT_ERROR, '还原数据出错！'];
			} elseif (0 === $r) {
				//下一卷
				if (isset($list[++$part])) {
					
					$data = array('part' => $part, 'start' => 0);
					return [RESULT_SUCCESS, "正在还原...#{$part}", '', $data];
				} else {
					session('backup_list', null);
					
					return [RESULT_SUCCESS, "还原完成！"];
				}
			} else {
				$data = array('part' => $part, 'start' => $r[0]);
				if ($r[1]) {
					$rate = floor(100 * ($r[0] / $r[1]));
					return [RESULT_SUCCESS, "正在还原...#{$part} ({$rate}%)", '', $data];
				} else {
					$data['gz'] = 1;
					return [RESULT_SUCCESS,"正在还原...#{$part}",'',$data];
				}
			}
		} else {
			return [RESULT_ERROR, '参数错误！'];
		}
    }
    public function downloadBak($time)
    {
    	if ($time) {
    		$name = date('Ymd-His', $time) . '-*.sql*';
    		$filename = date('Ymd-His', $time) . 'sql';
    		$path = realpath($this->path) . DIRECTORY_SEPARATOR . $name;
    		$file=glob($path);
    		$count=count($file);
    		if($count>0){
    			
    	
    			
    			$data=array('path'=>encrypt(json_encode($file)),'local'=>1,'name'=>$filename);
    			return [RESULT_SUCCESS,'备份文件开始下载','',$data];
    		}else{
    			return [RESULT_ERROR, '无可下载文件！'];
    		}
    	
    			 
    		
    	
    	} else {
    	
    		return [RESULT_ERROR, '参数错误！'];
    	}
        
        
       
    }
}
