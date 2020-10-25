<?php
namespace app\common\traits;
use app\common\util\Unzip;
use think\Db;
use think\Cache;

trait Market
{
    /**
     * 下载云端模块,并执行安装
     * @param number $id
     * @return void|\think\response\Json
     */
    protected function getapp($id=0,$type='m'){
        $keywords = input('keywords');
        $appkey = input('appkey');
        $domain = input('domain');
        $upvip = input('upvip'); //免费或者是破解版升级授权
        
        $basepath = $type=='m' ? APP_PATH : PLUGINS_PATH;
        
        if(!is_writable($basepath)){
            return $this->err_js($basepath.'目录不可写,请先修改目录属性可写');
        }elseif ( is_dir($basepath.$keywords) ){
            if ($upvip==1||($type=='m'&&modules_config($keywords))||($type!='m'&&plugins_config($keywords))) { //如果频道停用的话.原数据库会被清空
                $_array = modules_config($keywords);
                if($upvip==1){
                    $this->model->update([
                        'id'=>$_array['id'],
                        'version'=>'',
                        'version_id'=>$id,
                    ]);
                    Cache::clear();
                    return $this->err_js( '当前应用成功授权为正版应用,请按键盘F5键,刷新网页获取升级文件.' );
                }elseif ($type=='m' && $_array && $_array['version_id']!=$id) {
                    $_info = json_decode(http_curl("https://x1.php168.com/appstore/getapp/info.html?id=".$id),true);
                    $this->model->update([
                        'id'=>$_array['id'],
                        'version_id'=>$id,
                        'name'=>$_info['title']?:("增强版".$_array['name']),
                        'author'=>$_info['author']?:'',
                    ]);
                    Cache::clear();
                    return $this->err_js( $_array['name'].' 频道数据库升级成功,你还需要进一步升级文件,请按键盘F5键,刷新网页重新升级程序文件.' );
                }else{
                    return $this->err_js($basepath.$keywords.'该频道已经存在了,不能重复安装');
                }                
            }else{
                return $this->err_js("目录冲突，安全起见，请先卸载或删除当前目录:".$basepath.$keywords."，才能安装当前应用");
            }
            copy_dir($basepath.$keywords, RUNTIME_PATH."bakfile/$keywords".date('Y-m-d_H-i'));
            delete_dir($basepath.$keywords);
        }
        $url = "https://x1.php168.com/appstore/getapp/down.html?id=$id&domain=$domain&appkey=".urlencode($appkey);
        $result = $this->downModel($url,$keywords,$type);
        if($result!==true){
            return $this->err_js($result);
        }
        
        $result = $this->install($keywords,$type,$id);
        if($result!==true){
            return $this->err_js($result);
        }
        $this->clean_cache();
        return $this->ok_js(['url'=>url('group/admin_power',['id'=>$this->user['groupid']])],'模块安装成功,请设置一下后台权限');
    }
    
    /**
     * 清除相关缓存
     */
    protected function clean_cache(){
        cache('timed_task',null);
        cache('cache_modules_config',null);
        cache('cache_plugins_config',null);
        cache('hook_plugins',null);
    }
    
    /**
     * 云端下载模块
     * @param unknown $url
     * @param unknown $path
     */
    protected function downModel($url='',$path='',$type='m'){
        set_time_limit(0); //防止下载超时
        @unlink(RUNTIME_PATH.'temp.zip');
        downFile($url,RUNTIME_PATH.'temp.zip');
        if(!is_file(RUNTIME_PATH.'temp.zip')){
            return '文件下载失败';
        }elseif (filesize(RUNTIME_PATH.'temp.zip')<10){
            return read_file(RUNTIME_PATH.'temp.zip')?:'下载内容为空';
        }
        delete_dir(RUNTIME_PATH.'model');
        if (!function_exists('zip_open')) {
            return '你的空间php不支持zip扩展,请安装ZIP扩展,或更换PHP版本';
        }
        Unzip::unzip(RUNTIME_PATH.'temp.zip',RUNTIME_PATH.'model/');
        if(!is_dir(RUNTIME_PATH.'model/')){
            return '文件解压失败';
        }
        $ck = 0;
        $dir = opendir(RUNTIME_PATH.'model/');
        while(($file=readdir($dir))!==false){
            if($file=='.'||$file=='..'){
                continue ;
            }
            if($file=='static'){
                //图片及JS CSS目录必须命名为static目录
                copy_dir(RUNTIME_PATH."model/static",PUBLIC_PATH.'static',false);
//             }elseif($file=='template'){
//                 //模板目录
//                 copy_dir(RUNTIME_PATH."model/template",TEMPLATE_PATH,true);
            }elseif(!in_array($file,['application','extend','plugins','public','template','thinkphp','vendor','admin.php','index.php','member.php'])){
                if($type=='m'||$type=='p'){
                    //模块或插件的程序目录
                    copy_dir(RUNTIME_PATH."model/$file",($type=='m'?APP_PATH:PLUGINS_PATH).$path);
                }elseif($type=='hook'){
                    if(is_file(RUNTIME_PATH."model/$file")){
                        copy(RUNTIME_PATH."model/$file",APP_PATH.'common/hook/'.$file);
                    }else{
						copy_dir(RUNTIME_PATH."model/$file",ROOT_PATH.$file);
                        //return '钩子文件目录有误或者不存在';
                    }
                }
            }else{
                copy_dir(RUNTIME_PATH."model/$file",ROOT_PATH.$file);   //可以复制任何对应目录的文件
            }
            $ck++;
        }
        delete_dir(RUNTIME_PATH.'model');
        unlink(RUNTIME_PATH.'temp.zip');
        if($ck){
            return true;
        }else{
            return '文件解压失败或者复制文件失败';
        }        
    }
    
    /**
     * 复制数据表
     * @param string $newpre 新表前缀
     * @param string $oldpre 旧表前缀
     */
    protected function copy_table($newpre='',$oldpre=''){
        $query=Db::query("SHOW TABLE STATUS");
        foreach($query AS $rs){
            if(!preg_match("/^$oldpre/i", $rs['Name'])){
                continue;
            }
            $array = query("SHOW CREATE TABLE {$rs['Name']}")[0];
            $array['Create Table'] = str_replace($oldpre,$newpre,$array['Create Table']);
            Db::execute($array['Create Table']);
            $newtable = str_replace($oldpre,$newpre,$rs['Name']);
            Db::execute("INSERT INTO `{$newtable}` SELECT * FROM `{$rs['Name']}`");
        }
    }
    
    /**
     * 所有文件替换新的类名
     * @param unknown $oldkey
     * @param unknown $newkey
     */
    protected function replace_class_name($oldkey,$newkey,$type='m'){
        if($type=='m'){
            $basepath = APP_PATH;
            $basename = 'app';
        }else{
            $basepath = PLUGINS_PATH;
            $basename = 'plugins';
        }
        $file_array = get_dir_file($basepath.$newkey,'php');
        foreach ($file_array AS $file){
            $str = str_replace([" $basename\\$oldkey\\"," $basename\\$oldkey;"] , [" $basename\\$newkey\\"," $basename\\$newkey;"] , read_file($file));
            if (strstr($file,'/model/')||strstr($file,"\\model\\")) {
                $str = str_replace('__'.strtoupper($oldkey), '__'.strtoupper($newkey), $str);
            }
            write_file($file, $str);
        }
    }
    
    /**
     * 复制参数配置,包括参数分类分组
     * @param unknown $old_id
     * @param unknown $new_id
     */
    protected function copy_config($old_id,$new_id,$type='m'){
        $_old_id = $type=='m' ? $old_id : -$old_id;
        $config_group = Db::name('config_group')->where(['sys_id'=>$_old_id])->column(true);
        foreach($config_group AS $rs){
            $config = Db::name('config')->where(['type'=>$rs['id']])->column(true);
            unset($rs['id']);
            $rs['sys_id'] = $type=='m' ? $new_id : -$new_id;
            $groupid = Db::name('config_group')->insert($rs,false,true);
            foreach($config AS $vs){
                unset($vs['id']);
                $vs['sys_id'] = $type=='m' ? $new_id : -$new_id;
                $vs['type'] = $groupid;    //新的分类ID
                Db::name('config')->insert($vs);
            }
        }
    }
    
    /**
     * 安装当前模块要依赖于哪个模块或插件
     * @param string $modules
     * @param string $plugins
     */
    protected function bind_model($modules='',$plugins=''){
        $array_m = [];
        $array_p = [];
        if ($modules) {
            $array = is_array($modules)?$modules:array_flip(explode(',', $modules));
            foreach ($array AS $name=>$title){
                if ($name && empty(modules_config($name))) {
                    $array_m[] = is_numeric($title) ? $name : $title;
                }
            }
        }
        if ($plugins) {
            $array = is_array($plugins)?$plugins:array_flip(explode(',', $plugins));
            foreach ($array AS $name=>$title){
                if ($name && empty(plugins_config($name))) {
                    $array_p[] = is_numeric($title) ? $name : $title;
                }
            }
        }
        if ($array_m || $array_p) {
            $show = '';
            $array_m && $show.= '请在频道应用市场，先安装  “'.implode(',', $array_m).'” 频道，';
            $array_p && $show.= '请在插件应用市场，先安装  “'.implode(',', $array_p).'” 插件，';
            return $show.'如果已安装的话,请把关闭状态改为启用。';
        }
        return true;
    }

    /**
     * 执行安装模块插件
     * @param unknown $keywords 目录名即关键字
     * @param string $type 模块还是插件
     * @param number $version_id 云端对应的ID,方便日后升级核对
     * @return string|boolean
     */
    protected function install($keywords,$type='m',$version_id=0){
        $basepath = $type=='m' ? APP_PATH : PLUGINS_PATH;
        
        $info = @include $basepath."$keywords/install/info.php";
        if(empty($info)){
            return '安装配置文件不存在!';
        }elseif($this->bind_model($info['bind_modules'],$info['bind_plugins'])!==true){    //检查依赖的模块
            $keywords && delete_dir($basepath.$keywords);
            return $this->bind_model($info['bind_modules'],$info['bind_plugins']);
        }
        $sql = read_file($basepath."$keywords/install/install.sql");
        strlen($sql)>10 && into_sql($sql);
        $info['version_id'] = intval($version_id);
        
        $string = http_curl("https://x1.php168.com/appstore/upgrade/get_version.html?id=".$version_id);
        if ($string!='') {
            $detail = json_decode($string,true);
            if ($detail['md5']) {
                $info['version'] = $detail['time']."\t".$detail['md5'];
            }
        }
        $result = $this->model->create($info);
        if(empty($result)){
            return '数据库安装执行失败!';
        }
        $model_id = $result->id;
        
        $list = 10;
        $i = 0;
        $sys_id = $type=='m' ? $model_id : -$model_id;
        $ifsys = $type=='m' ? 0 : intval($info['ifsys']);
        foreach($info['config_group'] AS $title){
            $data = [
                'title'=>$title,
                'sys_id'=> $sys_id,
                'list'=>--$list,
                'ifsys'=> $ifsys,
                'ifshow'=>intval($info['ifshow']),
            ];
            $i++;
            //创建参数配置分类
            $groupid = Db::name('config_group')->insert($data,false,true);
            //修改入库前还没有进行分类的参数
            Db::name('config')->where('type',-$i)->update(['type'=>$groupid,'sys_id'=>$sys_id,'ifsys'=>$ifsys]);
        }
        $this->run_install($model_id,$type,$keywords,'install');
        
        cache($type=='m' ?'cache_modules_config':'cache_plugins_config',null);  //清空缓存
        
        return true;
    }
    
    /**
     * 卸载模块
     * @param number $ids
     */
    protected function uninstall($ids=0,$type='m'){
        $basepath = $type=='m' ? APP_PATH : PLUGINS_PATH;
        
        $id = intval($ids);
        $info = $this->getInfoData($id);
        if (empty($info)) return '缺少参数';
        if (empty($info['keywords'])) return '目录名不存在';
        
        $this->run_install($id,$type,$info['keywords'],'uninstall');
        
        //卸载时,要执行的SQL语句
        $sql = @file_get_contents($basepath.$info['keywords'].'/install/uninstall.sql');
        if (strlen($sql)>15) {  //完整的SQL语句,长度不会小于15个字符
            into_sql($sql,true,0);
        }        
        
        //删除频道模型记录表
        $this->model->destroy($id);        
        
        //删除程序目录
        delete_dir($basepath.$info['keywords']);
        
        //删除模板目录
        $this->delete_template_file($info['keywords'],'index',$type);    //前台模板
        $this->delete_template_file($info['keywords'],'admin',$type);    //后台模板
        $this->delete_template_file($info['keywords'],'member',$type);    //会员中心模板
        
        //删除数据表
        $oldpre = config('database.prefix').$info['keywords'].'_';
        $query=Db::query("SHOW TABLE STATUS");
        foreach($query AS $rs){
            if(!preg_match("/^$oldpre/i", $rs['Name'])){
                continue;
            }
            Db::execute("DROP TABLE IF EXISTS {$rs['Name']}");
        }
        
        $_id = $type=='m' ? $id : -$id ;
        //删除参数配置
        Db::name('config_group')->where(['sys_id'=> $_id])->delete();
        
        Db::name('config')->where(['sys_id'=>$_id])->delete();
        
        return true;
    }
    
    /**
     * 复制模块
     * @param array $info 旧模块信息
     * @param array $data 新模块信息
     * @param string $type
     * @return string|boolean
     */
    protected function copy_mod($info=[],$data=[],$type='m'){
        $basepath = $type=='m' ? APP_PATH : PLUGINS_PATH;
        if (is_dir($basepath.$data['keywords'])) {
            return '当前目录已经存在了!';
        }elseif(is_table($data['keywords'].'_content')) {
            return '当前数据表已经存在了!';
        }
        
        $array = [
                'name'=>$data['name'],
                'type'=>$info['type'],
                'icon'=>$info['icon'],
                'author'=>$info['author'],
                'ifopen'=>1,
                'keywords'=>$data['keywords'],
        ];
        $result = $this->model->create($array);
        $new_id = $result->id;
        
        //$old_id = $type=='m' ? modules_config($info['keywords'])['id'] : plugins_config($info['keywords'])['id'];
        $old_id =  $info['id'];
        
        copy_dir($basepath.$info['keywords'], $basepath.$data['keywords']); //复制程序目录
        $this->copy_template_file($info['keywords'],$data['keywords'],'index',$type);    //复制前台模板
        $this->copy_template_file($info['keywords'],$data['keywords'],'admin',$type);    //复制后台模板
        $this->copy_template_file($info['keywords'],$data['keywords'],'member',$type);    //复制会员中心模板
        
        $this->replace_class_name($info['keywords'],$data['keywords'],$type);
        $this->copy_table(config('database.prefix').$data['keywords'].'_' , config('database.prefix').$info['keywords'].'_');
        $this->copy_config($old_id,$new_id,$type);
        $this->run_install($new_id,$type,$data['keywords'],'copyinstall');
        return true;
    }
    
    /**
     * 复制模板文件
     * @param string $old_dir 原来的模块目录
     * @param string $new_dir 新模块的目录
     * @param string $entrance 前台还是后台
     * @param string $type 模块还是插件
     */
    protected function copy_template_file($old_dir='',$new_dir='',$entrance='index',$type='m'){
        $basepath = TEMPLATE_PATH.$entrance.'_style/';
        if($type=='p'){
            $old_dir = 'plugins/'.$old_dir;
            $new_dir = 'plugins/'.$new_dir;
        }
        $dir = opendir($basepath);
        while (($file=readdir($dir))!==false) {
            if($file!='.'&&$file!='..'&&is_dir($basepath.$file.'/'.$old_dir)){
                copy_dir($basepath.$file.'/'.$old_dir, $basepath.$file.'/'.$new_dir);
            }
        }
    }
    
    /**
     * 执行脚本安装或卸载
     * @param number $id 模块生成的ID
     * @param string $type 频道或插件
     * @param string $keyword 目录名关键字
     * @param string $act 安装或复制或卸载
     */
    protected function run_install($id=0,$type='m',$keyword='',$act='install'){
        if($type=='m'){
            $class = "app\\$keyword\\install\\".ucfirst($act);
        }else{
            $class = "plugins\\$keyword\\install\\".ucfirst($act);
        }
        if(class_exists($class) && method_exists($class, 'run')){
            $obj = new $class;
            $obj->run($id);
        }
    }
    
    /**
     * 删除模板目录
     * @param string $old_dir
     * @param string $entrance
     * @param string $type
     */
    protected function delete_template_file($old_dir='',$entrance='index',$type='m'){
        if($old_dir===''){
            return ;
        }
        $basepath = TEMPLATE_PATH.$entrance.'_style/';
        if($type=='p'){
            $old_dir = 'plugins/'.$old_dir;
        }
        $dir = opendir($basepath);
        while (($file=readdir($dir))!==false) {
            if($file!='.'&&$file!='..'&&is_dir($basepath.$file.'/'.$old_dir)){
                delete_dir($basepath.$file.'/'.$old_dir);
            }
        }
    }
    
    
}





