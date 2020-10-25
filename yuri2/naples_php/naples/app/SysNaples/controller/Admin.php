<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/19
 * Time: 13:57
 */

namespace naples\app\SysNaples\controller;


use Michelf\Markdown;
use Michelf\MarkdownExtra;
use naples\app\SysNaples\src\ScaffoldingCURDHelper;
use naples\lib\base\Controller;
use naples\lib\Factory;
use Pinq\Traversable;
use Respect\Validation\Validator as v;

class Admin extends Controller
{
    function __construct()
    {
        config('debug', false);
    }

    function login(){
        $rem=cookie('naplesAdminRem')=='yes'?'checked':'';
        $this->assign('rem',$rem);
        if (!isFlagNotSet(cookie('naplesAdminPsw'))){
            $this->assign('psw',cookie('naplesAdminPsw'));
        }else{
            $this->assign('psw','');
        }
        $this->assign('jump',get('jump'));

        $this->render();
    }

    function logout(){
        session('naples.isAdmin',false);
        cookie('naplesAdminPsw','',0);
        success('登出成功！',urlBased('login'));
    }

    /**
     * @method post
     */
    function checkLogin(){
        if ($this->checkToken()){
            if ($this->checkCaptcha(post('cap'))){
                $psw=post('psw');
                if (md5($psw)===NAPLES_ADMIN){
                    session('naples.isAdmin',true);
                    if (post('rem')=='yes'){
                        cookie('naplesAdminPsw',$psw,3600*24*3);
                        cookie('naplesAdminRem',post('rem'),3600*24*3);
                    }else{
                        cookie('naplesAdminPsw','',0);
                        cookie('naplesAdminRem','',0);
                    }
                    if (post('jump')){
                        success('登录成功！',post('jump'));
                    }else{
                        success('登录成功！',url('sysNaples/Admin'));
                    }
                }else{
                    error('登录失败，密码错误',url('SysNaples/Admin/login',['jump'=>post('jump')]));
                }
            }else{
                error('登录失败，验证码错误',url('SysNaples/Admin/login',['jump'=>post('jump')]));
            }
        }
        error('请勿重复提交表单');
    }

    /**
     * @naples admin
     */
    function index(){
        $fileMd=PATH_ROOT.'/readme.md';
        $text=is_file($fileMd)?file_get_contents($fileMd):"#哎呀，没找到说明文件\r\n###$fileMd";
        $mdHTML=Factory::getMdHtml($text);
        $this->assign('html',$mdHTML);
        $this->render();
    }

    /**
     * @naples admin
     */
    function report($param=''){
        $dir=PATH_RUNTIME.'/reports';
        $reports=[];
        $action='read';
        $reportArr=[];
        switch ($param){
            case 'list':
                //显示列表
                \Yuri2::ergodicDir($dir,function ($file) use (&$reports,$dir){
                    $filePath=$dir.'/'.$file;
                    $mTime=filemtime($filePath);
                    $report=[];
                    $name=\Yuri2::removeSuffix($file,'.rep');
                    $report['name']="<a style='font-size: 10px' href='".url("SysNaples/Admin/report/$name")."'>".$name."</a>";
                    $report['mtime']=$mTime;
                    $report['mdate']=date('Y/m/d H:i:s',$mTime);
                    $report['filePath']=$filePath;
                    $reports[]=$report;
                });
                $reportsTra=Traversable::from($reports);
                $reports=$reportsTra->orderByDescending(function ($row){
                    return $row['mtime'];
                })->select(function ($row) {
                    $reportArr=unserialize(file_get_contents($row['filePath']));
                    return [
                        '运行实例'=> $row['name'],
                        '异常数目'=>count($reportArr['错误']),
                        '生成时间'=> $row['mdate'],
                        '访问简报'=>"<a  href='".$reportArr['简报']['URL']."'>".$reportArr['简报']['访问者IP']."</a>",
                        '访问URL'=>"<a style='font-size: 10px' href='".$reportArr['简报']['URL']."'>".$reportArr['简报']['URL']."</a>",
                    ];
                });
                $action='list';
                break;
            case 'del':
                $action='del';
                \Yuri2::ergodicDir($dir,function ($file) use ($dir){
                    unlink($dir.'/'.$file);
                });
                redirect(url(res('',['list'])));
                break;
            default:
                //显示内容
                $filePath=$dir.'/'.$param.'.rep';
                if (is_file($filePath)){
                    $reportArr=unserialize(file_get_contents($filePath));
                }
                $action='read';
        }
        $this->assign('action',$action);
        $this->assign('reports',$reports);
        $this->assign('reportArr',$reportArr);
        $this->render();
    }

    /**
     * 如果详细报告的错误数目多，加载将需要太长时间，所以用ajax动态加载
     * @naples admin
     * @method ajax
     */
    function reportErrorRender()
    {
        config('debug', false);
        $value = post('data');
        $key = post('key');
        $html = '';
        $html .= "                    <div class=\"panel-body\">
                            <div class=\"panel panel-primary\">
                                <div class=\"panel-heading\">
                                    <h3 class=\"panel-title\">基本信息</h3>
                                </div>
                            </div>
                            <table class=\"table table-hover table-responsive table-bordered\">" . RN;
        $arr_temp = ['类型' => $value['type'], '摘要' => $value['msg'], '行号' . $value['line'] => $value['file']];
        foreach ($arr_temp as $k => $v) {
            $html .= "                                <tr>" . RN;
            $html .= "                                    <td>$k</td>" . RN;
            $html .= "                                    <td>" . RN;
            if (is_array($v)) {
                if (!empty($v)) {
                    $html .= dump($v, false);
                } else {
                    $html .= 'null';
                }
            } else {
                $html .= $v;
            }
            $html .= "                                    </td>" . RN;
            $html .= "                                </tr>" . RN;
        }
        $html .= "                    </table>
                    <div class=\"panel panel-primary\">
                                <div class=\"panel-heading\">
                                    <h3 class=\"panel-title\">运行轨迹</h3>
                                </div>" . RN;
        if (!isset($value['trace'])) {
            $value['trace'] = [];
        }
        foreach ($value['trace'] as $kk => $vv) {
            $html .= "                                <div class=\"panel-body\">
                                    <div class=\"panel panel-info btnLines\">
                                        <div class=\"panel-heading\">
                                            <table  border=\"0\">
                                                <tr><td style=\"min-width: 100px\">第{$kk}层</td><td>" . \Yuri2::arrGetSet($vv, 'class') . \Yuri2::arrGetSet($vv, 'type') . \Yuri2::arrGetSet($vv, 'function') . "()</td></tr>
                                                " . RN;
            if (!empty($vv['file'])) {
                $html .= "                                                <tr><td>LINE " . \Yuri2::arrGetSet($vv, 'line') . "</td><td>" . \Yuri2::arrGetSet($vv, 'file') . "</td></tr>" . RN;
            }
            $html .= "</table>
                                        </div>
                                    </div>" . RN;

            if (!empty($vv['file'])) {
                $html .= "<div class=\"panel-body fileShot \"";
                if ($kk != '0' and $kk != '1') {
                    $html .= "style=\"display:none\"";
                }
                $html .= "\">";
                $fileLines = \naples\app\SysNaples\controller\Admin::getFileShot($vv['file'], $vv['line']);
                $html .= "<table class=\"table table-hover table-striped table-condensed table-bordered \">";
                foreach ($fileLines as $kkk => $vvv) {
                    if ($kkk == $vv['line']) {
                        $html .= "<tr class=\"danger\">
                                    <td>$kkk</td>
                                    <td class=\"success\">$vvv</td>
                                </tr>";
                    } else {
                        $html .= "<tr>
                                    <td>$kkk</td>
                                    <td>$vvv</td>
                                </tr>";
                    }
                }
                $html .= "</table>
                </div>";
            }
            $html .= "                    </div>" . RN;
        }
        return ['data' => $html];

    }

    /**
     * @naples admin
     */
    function log($param=''){
        $today=date('Y-m-d');
        if ($param=='today'){
            $param=$today;
        }
        $dir=PATH_RUNTIME.'/logs';
        $content='';$logs=[];$action='read';
        switch ($param){
            case 'list':
                //显示列表
                \Yuri2::ergodicDir($dir,function ($file) use (&$logs){
                    $log=[];
                    $log['name']=\Yuri2::removeSuffix($file,'.html');
                    $log['href']=url("SysNaples/Admin/log/{$log['name']}");
                    $logs[]=$log;
                });
                rsort($logs);
                $action='list';
                break;
            case 'del':
                $action='del';
                \Yuri2::ergodicDir($dir,function ($file) use ($dir){
                    unlink($dir.'/'.$file);
                });
                $urlLogToday=url(res('sysNaples/Admin/log/today'));
                $this->assign('urlLogToday',$urlLogToday);
                break;
            default:
                //显示日志内容
                $file=$dir.'/'.$today.'.html';
                if (is_file($file)){
                    $content=file_get_contents($file);
                }
                $action='read';
        }
        $this->assign('logs',$logs);
        $this->assign('logContent',$content);
        $this->assign('action',$action);
        $this->render();
    }

    /**获得文件简报*/
    public static function getFileShot($file,$line){
        if (!is_file($file)){return [];}
        $fileContent=file_get_contents($file);
        $lines=explode("\n",$fileContent);
        $num=count($lines);
        $up=$line-6<0?0:$line-6;
        $down=$line+4>$num-1?$num-1:$line+4;
        $rel=[];
        for ($i=$up;$i<=$down;$i++){
            $rel[$i+1]=str_replace(' ',"&nbsp",htmlspecialchars($lines[$i]));
        }
        return $rel;
    }

    /**
     * 系统管理
     * @naples admin
     */
    function sys($action=''){
        switch ($action){
            case 'clean':
                $cleans=[
                    'cache'=>'基础缓存',
                    'logs'=>'运行日志',
                    'naplesTpl'=>'模板缓存',
                    'qrCode_cache'=>'二维码缓存',
                    'reports'=>'详细报告',
                    'temp'=>'临时目录',
                ];
                foreach ($cleans as $k=>$v){
                    $dir=PATH_RUNTIME.'/'.$k;
                    $count=0;
                    if (is_dir($dir)){
                        \Yuri2::ergodicDir($dir,function ($file) use($dir,&$count){
                            if (is_file($dir.'/'.$file)){
                                $count++;
                            }
                        });
                    }
                    $cleans[$k].="($count)";
                }
                $this->assign('cleans',$cleans);
                break;
            case 'changePsw':
                //改密码
                break;
            case 'accessList':{
                //黑白名单
                $dbList=Factory::getArrDatabase('sys/accessList');
                $ip=\Yuri2::getIp();
                $modetype=\Yuri2::arrGetSet($dbList->data,'mode');
                if ($modetype=='white'){
                    $this->assign('modetype','白名单');
                }elseif($modetype=='black'){
                    $this->assign('modetype','黑名单');
                }else{
                    $this->assign('modetype','无限制');
                }
                $blackList=\Yuri2::arrGetSet($dbList->data,'blackList');
                $whiteList=\Yuri2::arrGetSet($dbList->data,'whiteList');
                $arr=['ip'=>$ip,'mode'=>$modetype,'blackList'=>$blackList,'whiteList'=>$whiteList];
                trace('ListArr',$arr);
                $this->assign($arr);
                break;}
        }
        $this->assign('action',$action);
        $this->render();
    }
    
    /**
     * @naples admin
     * @method post
     */
    function sysCleans(){
        if($this->checkToken()){
            $cbxCleans=post('cbxCleans');
            if (!$cbxCleans){$cbxCleans=[];}
            foreach ($cbxCleans as $item){
                $dir=PATH_RUNTIME.'/'.$item;
                \Yuri2::delDir($dir);
            }
            redirect(urlBased('sys'));
        }else{
            error('请勿重复提交表单','back');
        }
        
    }

    /**
     * @naples admin
     * @method post
     */
    function sysChangePsw(){
        if($this->checkToken()){
            $newPsw=post('psw1');
            $preg="/define\\('NAPLES_ADMIN','([\\s\\S]+?)'\\);\\/\\/管理员密码md5/";
            $file=PATH_NAPLES.'/naples.inc.php';
            $fileContent=file_get_contents($file);
            if(preg_match($preg,$fileContent,$matches)){
                $fileContent=str_replace($matches[0],"define('NAPLES_ADMIN','".md5($newPsw)."');//管理员密码md5",$fileContent);
                file_put_contents($file,$fileContent);
                success('修改密码完成',urlBased('sys'));
            }else{
                error('修改失败，源文件不匹配','back');
            }
        }else{
            error('请勿重复提交表单','back');
        }

    }

    /**
     * @naples admin
     * @method post
     */
    function sysAddList(){
        $ip=post('ip');
        $info=post('info');
        $exp=post('exp');
        $list=post('list');
        if (!empty($ip) and is_numeric($exp) and !empty($list)){
            $dbList=Factory::getArrDatabase('sys/accessList');
            switch ($list){
                case 'white':
                    $dbList->data['whiteList'][$ip]=['info'=>$info,'exp'=>TIMESTAMP+$exp*60];
                    break;
                case 'black':
                    $dbList->data['blackList'][$ip]=['info'=>$info,'exp'=>TIMESTAMP+$exp*60];
                    break;
            }
            $dbList->save();
            redirect(url(res('sys').'/accessList'));
        }else{
            error('错误的输入参数','back');
        }
    }

    /**
     * @naples admin
     * @method ajax
     */
    function sysDelAccessList(){
        $type=post('type');
        $ip=post('ip');
        $dbList=Factory::getArrDatabase('sys/accessList');
        switch ($type){
            case 'white':
                unset($dbList->data['whiteList'][$ip]);
                break;
            case 'black':
                unset($dbList->data['blackList'][$ip]);
                break;
        }
        $dbList->save();
        return ['msg'=>'delete success'];
    }

    /**
     * @naples admin
     * @method ajax
     */
    function sysSwitchAccessList(){
        $type=post('type');
        $dbList=Factory::getArrDatabase('sys/accessList');
        \Yuri2::arrGetSet($dbList->data,'mode',$type);
        $dbList->save();
        return ['msg'=>'switch success'];
    }

    //脚手架---------------------------------------------------
    /**
     * 脚手架主界面
     * @naples admin
     * @action 子动作
     */
    function scaffolding($action='map'){

        $this->assign('action',$action);
        switch ($action){
            case 'controller':
                //查找目前的模块
                $modules=[];
                \Yuri2::ergodicDir(PATH_APP,function($file) use (&$modules){
                    if (preg_match('/^[A-Z]\w*$/',$file) and is_dir(PATH_APP.'/'.$file)){
                        $modules[]=$file;
                    }
                });
                $this->assign('modules',$modules);
                break;
            case 'map':
                //网站导览
                //查找目前的模块
                $modules=[];
                \Yuri2::ergodicDir(PATH_APP,function($file) use (&$modules){
                    if (preg_match('/^[A-Z]\w*$/',$file) and is_dir(PATH_APP.'/'.$file)){
                        $module=['moduleName'=>$file,'ctrls'=>[]];
                        \Yuri2::ergodicDir(PATH_APP.'/'.$file.'/controller',function($ctrl) use (&$module){
                            if(!(preg_match('/\.php$/',$ctrl))){
                                return;
                            }
                            $ctrlName=preg_replace('/\.php$/','',$ctrl);
                            $ctrl=[
                                'ctrlName'=>$ctrlName,
                                'infos'=>Admin::readCtrl($module['moduleName'],$ctrlName)
                            ];
                            $module['ctrls'][]=$ctrl;
                        });
                        $modules[]=$module;
                    }
                });
                $this->assign('modules',$modules);
                break;
            case 'fastEdit':
                $fullPath=$this->getFastEditTmpFilePath();
                $this->assign('tmp',file_get_contents($fullPath));
                break;
            case 'model':{
                $step=request('step')?request('step'):1;
                if ($step==1){
                    $con_file=PATH_NAPLES.'/configs/dbConfig.php';
                    $db_configs=is_file($con_file)?require $con_file:[];
                    $db_sels=[];
                    foreach ($db_configs as $k=>$v){
                        $db_sels[]=$k;
                    }
                    $this->assign('db_sels',$db_sels);
                }elseif($step==2){
                    //预读数据库
                    $con_file=PATH_NAPLES.'/configs/dbConfig.php';
                    $db_configs=is_file($con_file)?require $con_file:[];
                    if (!isset($db_configs[request('model-db')])){
                        error('数据库配置文件错误');
                    }
                    initDb(request('model-db'));
                    $table_name=request('model-table-name');
                    $one=\ORM::for_table($table_name)->find_one();
                    $oneArr=$one?$one->asArray():[];
                    $this->assign('db_cols',array_keys($oneArr));
                    $this->assign('db_type',$db_configs[request('model-db')]['type']);
                }elseif($step==3){
                    //分析列数据
                    $cols=[];
                    $col_order=[];
                    foreach ($_REQUEST as $k=>$v){
                        if (preg_match('/^cols-new-(.*?)$/',$k,$matches)){
                            $col=$matches[1];
                            $cols[$col]=[
                                'name'=>request('cols-name-'.$col),
                                'doc'=>request('cols-doc-'.$col),
                            ];
                            $col_order[]=$col;
                        }
                    }

                    $this->assign('cols',$cols);
                    $this->assign('col_order',implode(',',$col_order));
                    $hiddens=request();
                    $hiddens['step']++;
                    $this->assign('hiddens',$hiddens);
                }elseif ($step==4){
                    //生成结果

                    //分析列数据
                    $cols=[];
                    foreach ($_REQUEST as $k=>$v){
                        if (preg_match('/^cols-new-(.*?)$/',$k,$matches)){
                            $col=$matches[1];
                            $cols[$col]=[
                                'name'=>request('cols-name-'.$col),
                                'doc'=>request('cols-doc-'.$col),
                            ];
                        }
                    };
                    $data=[
                        'model-id'=>request('model-id'),
                        'model-name'=>request('model-name'),
                        'model-db'=>request('model-db'),
                        'model-db-type'=>request('model-db-type'),
                        'model-table-name'=>request('model-table-name'),
                        'model-page-num'=>request('model-page-num'),
                        'col-pk'=>request('col-pk'),
                        'col-order-by'=>request('col-order-by'),
                        'col-order-rule'=>request('col-order-rule'),
                        'col-order'=>request('col-order'),
                        'cols'=>$cols,
                        'config-url'=>url()
                    ];
                    //搜寻模块
                    $modules=[];
                    \Yuri2::ergodicDir(PATH_APP,function($file) use (&$modules){
                        if (preg_match('/^[A-Z]\w*$/',$file) and is_dir(PATH_APP.'/'.$file)){
                            $modules[]=$file;
                        }
                    });
                    //尝试读取缓存txt_file_content_cache
                    $txt_file_content_cache=hasCache(request('cache_id'))?cache(request('cache_id')):'';

                    $this->assign('txt_file_content_cache',$txt_file_content_cache);
                    $this->assign('modules',$modules);
                    $this->assign('data',json_encode($data));
                }
                $this->assign('step',$step);
                break;
            }
        }
        $this->render();
    }

    /**
     * 处理CURD生成的ajax方法
     * @naples admin
     * @method ajax
     * @method post
     */
    public function scaffoldingModelBuilder(){
        //初始化
        $errno=0;
        $msg='';
        $date=date('Y/m/d H:i:s');

        $helper=new ScaffoldingCURDHelper(post());
        if ($helper->checkFile()){
            $title='操作完成 '.$date;
            $content=$helper->writeFile();
        }else{
            $title='文件预处理流程发生警告 '.$date;
            $content=$helper->getErrMsg();
        }

        return[
            'errno'=>$errno,
            'msg'=>$msg,
            'title'=>$title,
            'content'=>$content,
        ];
    }

    /**
     * 返回一个控制器的结构信息
     * @param $moduleName string
     * @param $ctrlName string
     * @return array
     */
    static function readCtrl($moduleName,$ctrlName){
        $className="naples\\app\\$moduleName\\controller\\$ctrlName";
        $classRef=new \ReflectionClass($className);
        $publicMethods=$classRef->getMethods(\ReflectionMethod::IS_PUBLIC);
        $infos=[];
        foreach ($publicMethods as $publicMethod){
            $info=[];
            $info['name']=$publicMethod->getName();
            if(in_array($info['name'],['__construct','beforeAction','afterAction','config'])){
                continue;
            }
            $info['doc']=$publicMethod->getDocComment();
            if (!$info['doc']){$info['doc']='';}
            $paramNames=[];
            $params=$publicMethod->getParameters();
            foreach ($params as $param){
                $paramNames[]=$param->getName();
            }
            $info['params']=implode(',',$paramNames);
            $info['url']=url("$moduleName/$ctrlName/".$info['name']);
            $infos[]=$info;
        }
        return $infos;
    }

    /**
     * 创建模块文件
     * @naples admin
     * @method ajax
     * @method post
     */
    function createModule(){
        $moduleName=post('moduleName');
        $msg='成功创建了模块 '.$moduleName;
        try{
            v::stringType()->length(1,20)->regex('/^[A-Z]\w*$/')->check($moduleName);
            //通过验证后处理文件
            $dirModule=PATH_APP.'/'.$moduleName;
            if (is_dir($dirModule)){
                $msg='该模块已经存在！';
            }
            $dirCtrl=$dirModule.'/controller';
            $dirView=$dirModule.'/view';
            $dirModel=$dirModule.'/model';
            $dirSrc=$dirModule.'/src';
            $fileConfig=$dirModule.'/config.php';
            $fileContent=file_get_contents(PATH_DATA.'/scaffolding/configs.tpl.php');
            \Yuri2::createDir($dirCtrl);
            \Yuri2::createDir($dirView);
            \Yuri2::createDir($dirModel);
            \Yuri2::createDir($dirSrc);
            file_put_contents($fileConfig,$fileContent);
        }catch (\Exception$e){
            $msg=$e->getMessage();
        }
        return ['msg'=>$msg];
    }

    /**
     * 创建控制器
     * @naples admin
     * @method ajax
     * @method post
     */
    function createCtrl(){
        $msg=dump(post(),false);
        $ctrlName=post('ctrlName');
        $moduleName=post('moduleName');
        $actions=post('actions');
        try{
            v::stringType()->length(1,20)->regex('/^[A-Z]\w*$/')->check($ctrlName);
            $dirModule=PATH_APP.'/'.$moduleName;
            $dirCtrl=$dirModule.'/controller';
            $dirView=$dirModule.'/view';
            $dirModel=$dirModule.'/model';
            $fileCtrl=$dirCtrl.'/'.$ctrlName.'.php';
            if (is_file($fileCtrl)){
                \Yuri2::throwException('该控制器文件已经存在！');
            }

            $createTime=date('Y/m/d H:i:s',TIMESTAMP);

            //处理actions
            $actionContent='';
            foreach ($actions as $action){
                $actionName=$action['actionName'];
                v::stringType()->length(1,32)->regex('/^\w+$/')->check($actionName);
                $actionDoc=$action['actionDoc'];
                $doclines='';
                if ($actionDoc){
                    $docs=explode("\n",$actionDoc);
                    foreach ($docs as $doc){
                        $doc=trim($doc);
                        $doclines.='     * '.$doc."\r\n";
                    }
                }
                $tplEngine=$action['tplEngine'];
                switch ($tplEngine){
                    case 'naples':
                        $render="\$this->render();\r\n        ";
                        $fileName=$dirView.'/'.$ctrlName.'/'.$actionName.'.'.config('tpl_suffix');
                        if (!is_file($fileName)){
                            $fileContent=file_get_contents(PATH_DATA.'/scaffolding/naples.tpl.html');
                            \Yuri2::createDir(dirname($fileName));
                            file_put_contents($fileName,$fileContent);
                        }
                        break;
                    case 'php':
                        $render="\$this->render();\r\n        ";
                        $fileName=$dirView.'/'.$ctrlName.'/'.$actionName.'.php';
                        if (!is_file($fileName)){
                            $fileContent=file_get_contents(PATH_DATA.'/scaffolding/php.tpl.php');
                            \Yuri2::createDir(dirname($fileName));
                            file_put_contents($fileName,$fileContent);
                        }
                        break;
                    default :
                        $render='';
                        break;
                }
                $thisAction=<<<EOT
                
    /**
$doclines     */
    public function $actionName(){
        {$render}return ;
    }

EOT;
                $actionContent.=$thisAction;
            }

            $content=<<<EOT
<?php 
/** 
 * 该控制器文件由naples脚手架生成
 * 创建时间 $createTime
 */
namespace naples\\app\\$moduleName\\controller;

use naples\\lib\\base\\Controller;

class $ctrlName extends Controller
{ //class $ctrlName begin

    /** 构造函数 */
    function __construct(){
        
    }

    $actionContent
} //class $ctrlName end
EOT;

            if (file_put_contents($fileCtrl,$content)){
                $msg='成功创建了控制器 '.$ctrlName;
            }else{
                $msg='创建失败，请检查文件权限';
            }
        }catch (\Exception$e){
            $msg=$e->getMessage();
        }
        return ['msg'=>$msg];
    }

    /**
     * 加载快速编辑测试页面（一般嵌入至iframe）
     * @naples admin
     */
    function fastEdit(){
        config('debug',true);
        config('show_debug_btn',true);
        $fullPath=$this->getFastEditFilePath();
        require $fullPath;
    }

    /**
     * 上传快速编辑测试页面的代码
     * @naples admin
     * @method ajax
     * @method post
     */
    function fastEditSub(){
        $code=post('code');
        $fullPath=$this->getFastEditFilePath();
        $rel=file_put_contents($fullPath,$code);
        return ['msg'=>'上传代码长度：'.$rel];
    }

    /**
     * 上传快速编辑草稿的代码
     * @naples admin
     * @method ajax
     * @method post
     */
    function fastEditTmpSub(){
        $code=post('tmp');
        $fullPath=$this->getFastEditTmpFilePath();
        $rel=file_put_contents($fullPath,$code);
        return ['msg'=>'上传代码长度：'.$rel];
    }

    /** 获得快速编辑页文件路径（带预处理） */
    private function getFastEditFilePath(){
        $dir=PATH_DATA.'/scaffolding';
        $fileName='fastEdit.php';
        $fullPath=$dir.'/'.$fileName;
        if (!is_file($fullPath)){
            //初始化文件
            \Yuri2::createDir($dir);
            file_put_contents($fullPath,"<?php\r\n ");
        }
        return $fullPath;
    }

    /** 获得快速编辑页草稿纸文件路径（带预处理） */
    private function getFastEditTmpFilePath(){
        $dir=PATH_DATA.'/scaffolding';
        $fileName='fastEditTmp.txt';
        $fullPath=$dir.'/'.$fileName;
        if (!is_file($fullPath)){
            //初始化文件
            \Yuri2::createDir($dir);
            file_put_contents($fullPath,"");
        }
        return $fullPath;
    }

    /**
     * 代码编辑器
     * @naples admin
     */
    function fastEditMirror(){
        config('debug',false);
        $fullPath=$this->getFastEditFilePath();
        $code=file_get_contents($fullPath);
        $this->assign('code',$code);
        $this->render();
    }

    /**
     * webShell
     * @naples admin
     */
    function webShell(){
        config('debug',false);
        if (isset($_COOKIE)){
            $_COOKIE['pass']='4b91d138d6298a78069c5117e757f6d3dcf2186b';
        }
        $this->render();
    }

    /**
     * 打开本地任一文件
     * 参数 request ：file_path,is_html
     * @naples admin
     */
    function openFile(){
        $file_path=request('file_path');
        $is_html=request('is_html');
        if (is_file($file_path)){
            $file_content=file_get_contents($file_path);
            if ($is_html=='true' or $is_html=='1'){
                echo $file_content;
            }else{
                echo htmlspecialchars($file_content);
            }
        }else{
            error("找不到文件<h4>$file_path</h4>");
        }
    }

}