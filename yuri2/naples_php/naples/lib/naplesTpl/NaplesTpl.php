<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/13
 * Time: 10:16
 */

namespace naples\lib\naplesTpl;


use naples\lib\base\Service;

class NaplesTpl extends Service
{
    /** @var callable $funcResToFile */
    private static $funcResToFile;
    private $text='';
    private $base='';
    private $delimiter_l='';
    private $delimiter_r='';
    private $no_translate=[];
    private $savePath='';
    private $fileName='';
    private $fileFull='';
    private $tplFile='';
    private $relatedFiles=[];
    private $traceInfo=[];
    private $strNoTrans=[];

    function init(){
        $defines=$this->config('defines');
        $this->delimiter_l=$defines['delimiter'][0];
        $this->delimiter_r=$defines['delimiter'][1];
        $this->no_translate=$defines['no_translate'];
        $this->savePath=$this->configs['defines']['save_path'];
        $this->relatedFiles[]=['file'=>PATH_NAPLES.'/configs/naplesTpl.php'];
        \Yuri2::createDir($this->savePath);
        $this->traceInfo['config']=$this->config('defines');
    }

    //渲染
    function render($res,$data){
        tick('模板分析res');
        $resToFull=$this->resToFull($res);
        $this->traceInfo['res']=$resToFull;
        $this->fileName=self::resToFileName($resToFull);
        $this->fileFull=$this->savePath.'/'.$this->fileName;
        $this->tplFile=$this->resToFile($res);
        tick('模板分析res');
        if ($this->isNeedCompile()){
            $this->compile();
        }else{
            $this->traceInfo['is_recompiled']=false;
        }
        //布置变量
        foreach ($data as $k=>$v){
            $$k=$v;
        }
        //引用文件
        tick('执行php页面');require $this->fileFull;tick('执行php页面');
    }

    //检查是否需要重新编译
    private function isNeedCompile(){
        if (config('debug')){return true;}
        if (!$this->configs['defines']['auto_update'] and is_file($this->fileFull)){
            //不自动更新的情况下，有缓存页就调用
            return false;
        }
        $prefix='naplesTpl_';
        if (!is_file($this->fileFull)){
            return true;
        }
        $mtime=filemtime($this->fileFull);
        $mTpl=filemtime($this->tplFile);
        if ($mTpl>$mtime){return true;}
        $cache=cache($prefix.$this->fileFull);
        if (isFlagNotSet($cache)){
            return true;
        }
        $this->traceInfo['related']=$cache;
        foreach ($cache as $item){
            $file=$item['file'];
            if (!is_file($file)){
                return true;
            }
            $mRelTime=filemtime($file);
            if ($mRelTime>$mtime){return true;}
        }
        //检查文件关联

        return false;
    }

    private function compile(){
        tick('模板编译');
        self::checkFileExist($this->tplFile);
        $this->text=file_get_contents($this->tplFile);
        tick('模板规则解析');
        $this->preprocessed();
        $this->loadBase();
        $this->loadInc();
        $this->replace();
        $this->loadRules();
        $this->afterProcessed();
        tick('模板规则解析');
        file_put_contents($this->fileFull,$this->text);
        $prefix='naplesTpl_';
        cache($prefix.$this->fileFull,$this->relatedFiles,7200);
        tick('模板编译');
    }

    private static function resToFileName($res){
        $fileName=str_replace('/','-',$res).'.php';
        return $fileName;
    }

    //公共：检查文件是否存在
    private static function checkFileExist($file){
        if (!is_file($file)){
            \Yuri2::throwException('Can not read tpl file:'.$file);
        }
    }

    /**
     * 注入res转模板文件函数
     * @param $func
     * @return NaplesTpl
     */
    function callResToFile($func){
        NaplesTpl::$funcResToFile=$func;
        return $this;
    }

    private function resToFile($res){
        if (is_callable(NaplesTpl::$funcResToFile)){
            $func=NaplesTpl::$funcResToFile;
            $file= $func($res);
            return $file[1];
        }else{
            return false;
        }
    }

    private function resToFull($res){
        if (is_callable(NaplesTpl::$funcResToFile)){
            $func=NaplesTpl::$funcResToFile;
            $file= $func($res);
            return $file[0];
        }else{
            return false;
        }
    }

    //读取基类模板
    private function loadBase(){
        $preg='/^<!--\s*extend ([\w\/]*?)\s*-->/';
        while(preg_match($preg,$this->text,$matches)){
            $matchedRes=$matches[1];
            $baseFile=$this->resToFile($matchedRes);
            if (is_file($baseFile)){
                $this->base=file_get_contents($baseFile);
                $this->relatedFiles[]=['file'=>$baseFile];
                $this->extend();
            }else{
                if(config('debug')){
                    header('HTTP/1.0 404 Not Found');
                    error("找不到父级模板文件:<p>$baseFile</p>");
                }else{
                    header('HTTP/1.0 404 Not Found');
                    error('您所访问的页面不存在');
                }
            }
        }

        //去掉多余的标签block
        $this->text=preg_replace('/<(stay_)?block_(\w+)>/','',$this->text);
        $this->text=preg_replace('/<(\/stay_)?block_(\w+)>/','',$this->text);
    }

    //继承 替换
    private function extend(){
        $text=$this->base;
        $this->text=str_replace("<stay_block_","<block_",$this->text);
        $this->text=str_replace("</stay_block_","</block_",$this->text);
        $preg='/<block_(\w+)>/';
        $count=0;
        while(preg_match($preg,$text,$matches)){
            if (++$count>20){
                $errmsg=config('debug')?'模板引擎错误<h3>太多次的模板继承</h3>':'页面运行发生错误';
                header('HTTP/1.1 500 Internal Server Error');
                error($errmsg);
            }
            $blockName=$matches[1];
            //在子模板寻找
            $preg_child="/<block_($blockName)>([\s\S]*?)<\/block_$blockName>/";
            preg_match($preg_child,$this->text,$matches_child);
            if ($matches_child){
                $text=preg_replace($preg_child,'<stay_block_$1>'.$matches_child[2].'</stay_block_$1>',$text);
            }else{
                $text=str_replace("<block_$blockName>","<stay_block_$blockName>",$text);
                $text=str_replace("</block_$blockName>","</stay_block_$blockName>",$text);
            }
        }
        $this->text=$text;
    }

    //读取inc
    private function loadInc(){
        $inc_max=20;
        $text=$this->text;
        $preg_inc='/'.$this->delimiter_l.'inc ([\w\-\/]+)'.$this->delimiter_r.'/';
        while((--$inc_max)>0 and preg_match($preg_inc,$text,$matches_inc)){
            $res=$matches_inc[1];
            $inc_file=$this->resToFile($res);
            if (is_file($inc_file)){
                $this->relatedFiles[]=['file'=>$inc_file];
                $fileContent=file_get_contents($inc_file);
                $text=str_replace($matches_inc[0],$fileContent,$text);
            }else{
                if(config('debug')){
                    header('HTTP/1.0 404 Not Found');
                    error("找不到待引用模板文件:<p>$inc_file</p>");
                }else{
                    header('HTTP/1.0 404 Not Found');
                    error('您所访问的页面不存在');
                }
            }
        }
        if (config('debug') and $inc_max<=0){
            error('太多次的模板引用');
        }
        $this->text=$text;
    }

    //解读规则
    private function loadRules()
    {
        $this->traceInfo['matches']=array();
        $text=$this->text;
        if (self::isCached('delimitersForPreg')){
            $delimitersForPreg=self::useCache('delimitersForPreg');
            $delimiter_l=$delimitersForPreg[0];
            $delimiter_r=$delimitersForPreg[1];
        }else{
            $delimiter_l=\Yuri2::strForPreg($this->delimiter_l);
            $delimiter_r=\Yuri2::strForPreg($this->delimiter_r);
            self::useCache('delimitersForPreg',[$delimiter_l,$delimiter_r]);
        }

        $preg = '/' . $delimiter_l . '([\S\s]+?)' . $delimiter_r . '/';
        $rep_max=200;
        while ((--$rep_max) > 0 and preg_match($preg, $text, $matches)) {
            $rel=$this->pregRep($matches[1]);
            $text=str_replace($matches[0],$rel,$text);
        }
        if (config('debug') and $rep_max<=0){
            error('太多次的模板规则解析');
        }
        $this->text=$text;
    }

    //解读一次正则替换规则
    private function pregRep($target){
        $rules=$this->config('rules');
        foreach ($rules as $rule=>$func){
            //构建正则
            $preg='/'.$rule.'/';
            $isMatch=preg_match($preg,$target,$matches);
            if ($isMatch){
                $this->traceInfo['matches'][]=$rule;
                /** @var  $func callable */
                $rel=call_user_func_array($func,$matches);
                if ($rel!==false){
                    return $rel;
                }
            }
        }
        return $target;
    }

    private function replace(){
        $reps=$this->config('replace');
        $text=$this->text;
        foreach ($reps as $k=>$v){
            $text=str_replace($k,$v,$text);
        }
        $this->text=$text;
    }

    function getTrace(){
        $arrRelated=[];
        foreach ($this->relatedFiles as $item){
            $arrRelated[]=$item['file'];
        }
        $this->traceInfo['关联文件']=$arrRelated;
        return $this->traceInfo;
    }

    //文本不转义预处理
    function preprocessed(){
        if (self::isCached('pregForNoTrans')){
            $preg=self::useCache('pregForNoTrans');
        }else{
            $delimiter_l=\Yuri2::strForPreg($this->no_translate[0]);
            $delimiter_r=\Yuri2::strForPreg($this->no_translate[1]);
            $preg="/{$delimiter_l}([\\s\\S]*){$delimiter_r}/";
            self::useCache('pregForNoTrans',$preg);
        }
        $text=$this->text;
        while (preg_match($preg,$text,$matches)){
            $need=\Yuri2::uniqueID();
            $text=str_replace($matches[0],$need,$text);
            $this->strNoTrans[$need]=$matches[1];
        }
        $this->text=$text;
    }

    //文本不转义还原
    function afterProcessed(){
        foreach ($this->strNoTrans as $key=>$val){
            $this->text=str_replace($key,$val,$this->text);
        }
    }

    
    

}