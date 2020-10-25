<?php
namespace plugins\log\model;
use app\common\model\User AS UserModel;
use think\Model;


//后台操作日志
class Action extends Model
{
	
    // 设置当前模型对应的完整数据表名称
    protected $table = '__LOG_ACTION__';
    // 自动写入时间戳
    protected $autoWriteTimestamp = TRUE;
    
    /**
     * 操作日志
     * @param array $where
     */
    public static function write()
    {
        $userdb = UserModel::login_info();
        $dispatch = request()->dispatch();
        
        if(in_array($dispatch['module'][0].'/'.$dispatch['module'][1].'/'.$dispatch['module'][2],['admin/index/login','admin/mysql/backup'])){
            return ;
        }
        
        $plugin = '';
        if ($dispatch['module'][1]=='plugin' && $dispatch['module'][2]=='execute') {
            $plugin = input('plugin_name').'/'.input('plugin_controller').'/'.input('plugin_action');
        }
        
        $data = input();
        foreach($data AS $key=>$value){
            if(empty($value) || in_array($key, ['plugin_name','plugin_controller','plugin_action'])){
                unset($data[$key]);
            }elseif(strlen($value)>100){
                $value = get_word(del_html($value), 100);
                $data[$key] = $value;
            }
        }
        $content = json_encode($data);
        $array = [
                'uid'=>intval($userdb['uid']),
                'ip'=>get_ip(),
                'model'=>$dispatch['module'][0],
                'controller'=>$dispatch['module'][1],
                'action'=>$dispatch['module'][2],
                'plugin'=>$plugin,
                'content'=>$content,
        ];
        return self::create($array);
    }
	
}