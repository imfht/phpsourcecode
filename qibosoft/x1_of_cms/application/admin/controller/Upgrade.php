<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 
use app\common\model\Module;
use app\common\model\Plugin;
use app\common\model\Hook_plugin;
use app\common\model\Market as MarketModel;
use app\common\model\Timedtask AS TaskModel;

class Upgrade extends AdminBase
{
    public function _initialize(){
        parent::_initialize();
        if(config('client_upgrade_edition')==''){
            config('client_upgrade_edition',RUNTIME_PATH . '/client_upgrade_edition.php');
        }
    }
	
	public function index()
    {
        $this->clean_cache();
        $array = @include(config('client_upgrade_edition'));
        $this->assign('upgrade',$array);
		return $this->fetch('index');
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
	 * 更新升级日志
	 * @param string $upgrade_edition
	 * @return boolean|string
	 */
	private function writelog($upgrade_edition=''){
	    $data = $this->request->post();
	    if($data['m']){
	        $array = modules_config();
	        foreach ($array AS $rs){
	            $de = $data['m'][$rs['keywords']];
	            if($de){
	                $vs = $de['time']."\t".$de['md5'];
	                Module::update(['id'=>$rs['id'],'version'=>$vs]);
	            }
	        }
	    }
	    if($data['p']){
	        $array = plugins_config();
	        foreach ($array AS $rs){
	            $de = $data['p'][$rs['keywords']];
	            if($de){
	                $vs = $de['time']."\t".$de['md5'];
	                Plugin::update(['id'=>$rs['id'],'version'=>$vs]);
	            }
	        }
	    }
	    if($data['h']){
	        $array = cache('hook_plugins');
	        foreach ($array AS $rs){
	            $de = $data['h'][$rs['version_id']];
	            if($de){
	                $vs = $de['time']."\t".$de['md5'];
	                Hook_plugin::update(['id'=>$rs['id'],'version'=>$vs]);
	            }
	        }
	    }
	    if($data['t']){
	        $array = cache('timed_task');
	        foreach ($array AS $rs){
	            $de = $data['t'][$rs['version_id']];
	            if($de){
	                $vs = $de['time']."\t".$de['md5'];
	                TaskModel::update(['id'=>$rs['id'],'version'=>$vs]);
	            }
	        }
	    }
	    $this->upgrade_mark($data['admin_style'],'admin_style');
	    $this->upgrade_mark($data['index_style'],'index_style');
	    $this->upgrade_mark($data['member_style'],'member_style');
	    $this->upgrade_mark($data['qun_style'],'qun_style');
	    $this->upgrade_mark($data['haibao_style'],'haibao_style');
	    $this->upgrade_mark($data['model_style'],'model_style');
	    $this->upgrade_mark($data['packet'],'packet');
	    
	    $this->clean_cache();
	    if( file_put_contents(config('client_upgrade_edition'), '<?php return ["md5"=>"'.$upgrade_edition.'","time"=>"'.date('Y-m-d H:i').'",];') ){
	        return true;
	    }else{
	        return '权限不足,日志写入失败';
	    }
	}
	
	/**
	 * 应用市场,比如风格更新升级日志
	 * @param array $data
	 * @param string $type 比如admin_style index_style member_style
	 */
	private function upgrade_mark($data=[],$type=''){
	    if($data){
	        $array = MarketModel::get_list(['type'=>$type]);
	        foreach ($array AS $rs){
	            $de = $data[$rs['version_id']];
	            if($de){
	                $vs = $de['time']."\t".$de['md5'];
	                MarketModel::update(['id'=>$rs['id'],'version'=>$vs]);
	            }
	        }
	    }
	}
	
	/**
	 * 更新前,先备份一下文件
	 * @param string $filename
	 */
	private function bakfile($filename=''){
	    $bakpath = RUNTIME_PATH.'bakfile/';
	    if(!is_dir($bakpath)){
	        mkdir($bakpath);
	    }
	    $new_name = $bakpath.date('Y_m_d-H_i--').str_replace(['/','\\'], '--', $filename);
	    copy(ROOT_PATH.$filename,$new_name);
	}
	
	/**
	 * 升级数据库
	 * @param string $filename
	 */
	private function up_sql($filename=''){
	    if(preg_match('/\/upgrade\/([\w]+)\.sql$/', $filename)){
	    //if(preg_match('/^\/application\/common\/upgrade\/([\w]+)\.sql/', $filename)){
	        into_sql(ROOT_PATH.$filename,true,0);
	    }
	}
	
	/**
	 * 执行更多复杂的逻辑性的升级
	 * @param string $filename
	 */
	private function up_run($filename=''){
	    if(preg_match('/^\/application\/common\/upgrade\/([\w]+)\.php$/', $filename)){
	        $classname = "app\\common\\upgrade\\".ucfirst(substr(basename($filename), 0,-4));
	    }elseif(  preg_match('/(application|plugins)\/([\w]+)\/upgrade\/([\w]+)\.php$/',$filename,$array) ){     //实际已包含了上面的
	        $m_p = $array[1]=='application'?'app':'plugins';
	        $model = $array[2];
	        $file = $array[3];
	        $classname = "$m_p\\$model\\upgrade\\".ucfirst($file);
	    }else{
	        return;
	    }
	    if( class_exists($classname) && method_exists($classname, 'up') ){
	        $obj = new $classname;
	        try {
	            $obj->up();
	        } catch(\Exception $e) {
	            //echo $e;
	        }
	    }
	}
	
	
	/**
	 * 正式执行开始升级,一个一个的文件升级替换
	 */
	public function sysup($filename='',$upgrade_edition=''){
	    if($upgrade_edition){  //升级完毕,写入升级信息日志
	        $result = $this->writelog($upgrade_edition);
	        if( $result===true ){
	            return $this->ok_js([],'升级成功');
	        }else{
	            return $this->err_js($result);
	        }
	    }
	    list($filename,$id) = explode(',',$filename);
	    if($filename==''){
	        return $this->err_js('文件不存在!');
	    }
	    
	    $str = $this->get_server_file($filename,$id);
	    if($str){
	        $filename = $this->format_filename($filename); //针对模块或插件的升级做替换处理
	        $this->bakfile($filename);
	        makepath(dirname(ROOT_PATH.$filename));    //检查并生成目录
	        if( file_put_contents(ROOT_PATH.$filename, $str) ){
	            $this->up_sql($filename);
	            $this->up_run($filename);
	            return $this->ok_js([],'文件升级成功');
	        }else{
	            return $this->err_js('目录权限不足,文件写入失败');
	        }	        
	    }else{
	        return $this->err_js('获取云端数据失败,请确认服务器DNS是否正常,能否访问外网?');
	    }
	}
	
	/**
	 * 升级前,可以查看任何一个文件的源代码
	 * @param string $filename
	 */
	public function view_file($filename='',$id=0){
	    $str = $this->get_server_file($filename,$id);
	    $str = str_replace(['<','>'], ['&lt;','&gt;'], $str);
	    echo '<textarea style="width:100%;height:100%;">'.$str.'</textarea>';
	    die();
	}
	
	/**
	 * 针对要升级的模块与插件的文件名特别处理, 替换后,// 双斜杠开头的文件就是插件或模块升级的文件
	 * @param string $filename
	 * @return string|mixed
	 */
	protected function format_filename($filename=''){
	    if(strstr($filename,'/../../')){
	        $filename = str_replace(['/../../template/','/../../plugin/','/../../hook/'], '/../../', $filename);
	        $filename = preg_replace('/^\/..\/..\/([\w]+)/','/',$filename);
	    }
	    return $filename;
	}
	
	/**
	 * 核对需要升级文件,展示出来给用户挑选哪些不想升级
	 * 这里的升级文件列表,即有系统的,也有频道插件与风格的
	 * @return void|\think\response\Json
	 */
	public function check_files($upgrade_edition=''){
	    set_time_limit(0); //防止超时
	    $array = $this->getfile();
	    if(empty($array)){
	        $str = http_curl("https://x1.php168.com/appstore/upgrade/get_version.html?id=46");
	        if (!strstr($str,'md5')) {
	            return $this->err_js('你的服务器无法访问齐博官网,请检查你的服务器DNS是否配置正确，或者是否设置了防火墙不能访问外网');
	        }
	        return $this->err_js('获取云端文件列表数据失败,请晚点再偿试');
	    }
	    $data = [];
	    foreach($array AS $rs){
	        $showfile = $this->format_filename($rs['file']);
	        $file = ROOT_PATH.$showfile;
// 	        if(is_file($file.'.lock')){
// 	            continue;          //用户不想升级的文件
// 	        }
	        if(!is_file($file) || md5_file($file)!=$rs['md5']){
	            $data[]=[
	                'file'=>$rs['file'],
	                'showfile'=>$showfile,
	                'id'=>$rs['id'],
	                'islock'=>is_file($file.'.lock')?1:0,
	                'ctime'=>is_file($file)?date('Y-m-d H:i',filemtime($file)):'缺失的文件',
	                'time'=>date('Y-m-d H:i',$rs['time']),
	            ];
	        }
	    }
	    
	    $array_sql = $array_php = [];
	    foreach ($data AS $key=>$rs){
	        if( preg_match("/\/upgrade\/([\w]+\.sql)/i",$rs['file']) ){
	            unset($data[$key]);
	            $array_sql[$rs['file']] = $rs;
	        }elseif( preg_match("/\/upgrade\/([\w]+\.php)/i",$rs['file']) ){
	            unset($data[$key]);
	            $array_php[$rs['file']] = $rs;
	        }
	    }
	    ksort($array_php);
	    ksort($array_sql);
	    $data = array_values(array_merge($data,$array_sql,$array_php));
	    
	    if($data){
	        return $this->ok_js($data);
	    }else{
	        $upgrade_edition && $reustl = $this->writelog($upgrade_edition);
	        return $this->err_js($reustl!==true?$reustl:'没有可更新文件');
	    }
	}
	
	/**
	 * 获取云端的某个最新文件
	 * @param string $filename 升级的文件名
	 * @param number $id 对应云端的插件ID
	 * @return string|mixed
	 */
	protected function get_server_file($filename='',$id=0){
	    @set_time_limit(600);  //防止超时
	    $str = http_curl('https://x1.php168.com/appstore/upgrade/make_client_file.html?filename='.$filename.'&id='.$id.'&appkey='.urlencode($this->webdb['mymd5']).'&domain='.urlencode($this->request->domain()));
	    if(substr($str,0,2)=='QB'){    //文件核对,防止网络故障,抓取一些出错信息
	        $str = substr($str,2);
	    }else{
	        $str='';
	    }
	    return $str;
	}
	
	/**
	 * 获取云端的最新文件列表
	 * @return string|mixed
	 */
	protected function getfile(){
	    $str = http_curl('https://x1.php168.com/appstore/upgrade/get_list_file.html?typeid='.$this->webdb['typeid'].'&appkey='.urlencode($this->webdb['mymd5']).'&domain='.urlencode($this->request->domain()),['app_edition'=>fun('upgrade@local_edition')]);
	    return $str ? json_decode($str,true) : '';
	}

}
?>