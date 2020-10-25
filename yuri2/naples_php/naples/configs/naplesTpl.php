<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/13
 * Time: 10:25
 */
return [
    'defines'=>[
        'delimiter' => ['{{', '}}'],//左右识别定界符
        'no_translate' => ['<<<', '>>>'],//左右禁用转义标记定界符
        'save_path' => PATH_RUNTIME . '/naplesTpl',//模板存储目录（不建议修改）
        'auto_update' => true, //自动更新解析结果
    ],
    'rules'=>[
        '^\?([\s\S]*)$'=>function($v0,$v1){
            //?号开头的辨识为php代码块
            return Yuri2::addPhpTag($v1);
        },
        '^:(:?[\w]+)(\.[\w\.]+)?( \|[\w\|]+)?( [\S\s]*)?$'=>function($v0,$v1,$v2='',$v3='',$v4=''){
            //输出 a.b.c |e default
            $is_public=($v1{0}==':');
            $v1=$is_public?substr($v1,1):'$'.$v1;
            $v2=ltrim($v2,'.');

            if ($v2!==''){
                $var=$is_public?"\\Yuri2::arrPublic('$v1.$v2')":"\\Yuri2::arrGetSet($v1,'$v2')";
            }else{
                $var=$is_public?"\\Yuri2::arrPublic('$v1')":"$v1";
            }
            if ($v4!==''){
                $v4=preg_replace('/^ /','',$v4);
                $var = "(!isset($var) or is_null($var) or $var=='')?'$v4':$var";
            }
            if ($v3=='' and config('tpl_auto_escape')){$v3='|e';}
            else{$v3=preg_replace('/^ /','',$v3);}
            $rel=Yuri2::addPhpTag("\\Yuri2::smarterEcho($var,'$v3')");
            return $rel;
        },
        '^each ([^\s]+)( [^\s]+?)?( [^\s]+)?$'=>function($v0,$v1,$v2='',$v3=''){
            if ($v2=='' and $v3==''){
                return Yuri2::addPhpTag("foreach ($v1 as \$k=>\$v){");
            }
            elseif ($v3==''){
                return Yuri2::addPhpTag("foreach ($v1 as $v2){");
            }else{
                return Yuri2::addPhpTag("foreach ($v1 as $v2=>$v3){");
            }
        },
        '^if ([\s\S]+)$'=>function($v0,$v1){
            return Yuri2::addPhpTag("if ($v1) {");
        },
        '^else$'=>function(){
            return Yuri2::addPhpTag("} else {");
        },
        '^elseif ([\s\S]+)$'=>function($v0,$v1){
            return Yuri2::addPhpTag("} elseif($v1) {");
        },
        '^\/$'=>function(){
            //结束符
            return Yuri2::addPhpTag('}');
        },
        '^for ([\s\S]*);([\s\S]*);([\s\S]*)$'=>function($v0,$v1,$v2,$v3){
            return Yuri2::addPhpTag("for($v1;$v2;$v3) {");
        },
        '^for ([\s\S]+)=([\s\S]+) to ([\s\S]+)$'=>function($v0,$v1,$v2,$v3){
            $v1='$'.$v1;
            return Yuri2::addPhpTag("for($v1=$v2;$v1<=$v3;$v1++) {");
        },
        '^\*[\s\S]*$'=>function(){
            //注释
            return '';
        },
        '^while ([\s\S]+)$'=>function($v0,$v1){
            return Yuri2::addPhpTag("while ($v1) {");
        },
        '^table ([\s\S]+)$'=>function($v0,$v1){
            //打印表格内容
            return Yuri2::addPhpTag("echo \\naples\\lib\\Factory::getTplExtend()->arrToHtmlTableBody($v1);");
        },
        '^import ([\s\S]+)$'=>function($v0,$v1){
            return Yuri2::addPhpTag("echo \\naples\\lib\\Factory::getTplExtend()->import('$v1');");
        },
        '^qrCode(-[a-zA-Z]+)?(-\d)?(-cache)? ([\s\S]+?)$' => function ($v0, $v1 = '', $v2 = '', $v3 = '', $v4 = '') {
            //二维码
            if ($v1){
                $v1=Yuri2::strReplaceOnce('-','',$v1);
            }
            if ($v2 === '') {
                $v2 = 3;
            } else {
                $v2 = Yuri2::strReplaceOnce('-', '', $v2);
            }
            $isCache = $v3 ? 'true' : 'false';
            return Yuri2::addPhpTag("echo \\naples\\lib\\Factory::getTplExtend()->qrCode($v4,$v2,'$v1',$isCache);");
        },
        '^token( ([\s\S]+))?$'=>function($v0,$v1='',$v2=''){
            if ($v2){
                return Yuri2::addPhpTag("echo \\naples\\lib\\Factory::getTplExtend()->token('$v2');");
            }else{
                return Yuri2::addPhpTag("echo \\naples\\lib\\Factory::getTplExtend()->token();");
            }
        },
        '^captcha( (\d+))?( (\d+))?$'=>function($v0,$v1='',$v2='',$v3='',$v4=''){
            $v2=$v2?$v2:150;
            $v4=$v4?$v4:30;
            return Yuri2::addPhpTag("echo \\naples\\lib\\Factory::getTplExtend()->captcha($v2,$v4);");
        },
        '^url( ([\s\S]+?))?( ([[\s\S]+]))?( based)?$' => function ($v0, $v1 = '', $v2 = '', $v3 = '', $v4 = '', $v5 = '') {
            $v2=$v2?$v2:'';
            $v4=$v4?$v4:'[]';
            if ($v5){
                switch ($v5){
                    case ' based':
                        $funcName='urlBased';
                        break;
                    default:
                        $funcName='url';
                        break;
                }
            }else{
                $funcName='url';
            }
            return Yuri2::addPhpTag("echo $funcName('$v2',$v4);");
        },
        '^urlSelf$'=>function(){
            return Yuri2::addPhpTag("echo url();");
        },
        '^dump ([\s\S]+)$'=>function($v0,$v1){
            return Yuri2::addPhpTag("dump ($v1);");
        },
        '^ue(-full)?( [\$\w]+)?( [\$\w]+)?$'=>function($v0,$v1='',$v2='',$v3=''){
            $isFull = $v1 ? 'true' : 'false';
            $v2=trim($v2);
            $v3=trim($v3);
            if ($v2){
                if ($v2{0}=='$'){
                    if ($v3){
                        $v3=$v3{0}=='$'?$v3:"'$v3'";
                    }else{
                        $v3="''";
                    }
                    return Yuri2::addPhpTag("echo \\naples\\lib\\Factory::getTplExtend()->ueditor($v2,$isFull,$v3);");
                }
                return \naples\lib\Factory::getTplExtend()->ueditor($v2,$isFull);
            }else{
                return \naples\lib\Factory::getTplExtend()->ueditor('default',$isFull);
            }
        },
        '^up ([\w\W]+)$'=>function($v0,$v1){
            $rel="echo \\naples\\lib\\Factory::getTplExtend()->uparse($v1);";
            return Yuri2::addPhpTag($rel);
        },
        '^hidden ([\w\W]+)$'=>function ($v0,$v1){
            $rel="echo \\naples\\lib\\Factory::getTplExtend()->arrToInputHidden($v1);";
            return Yuri2::addPhpTag($rel);
        },
    ],
    'replace'=>[
        //直接替换
        '__PUBLIC__'=>URL_PUBLIC,
        '__HTML__'=>URL_PUBLIC.'/html/'.config('html_pages_dir'),
    ],

];