<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/6
 * Time: 9:17
 */

namespace naples\lib;
use naples\lib\base\Service;

/**
 * 大部分模板引擎都支持在模板中使用变量
 * 本类封装了naplesPHP跟模板相关的一些方法，例如生成url、表单令牌、验证码等等
 *
 */
class TplExtend extends Service
{
    /** 引用公共函数url */
    function url($url='',$params=[]){
        return url($url,$params);
    }

    /** 验证码 */
    function captcha($width=150,$height=30){
        $url=url('/SysNaples/Captcha/index',['width'=>$width,'height'=>$height]);
        return "<img style='max-width: {$width}px;max-height:{$height}px;width:100%;height: 100%;' src='$url' onclick='this.src=\"$url&nonce=\"+Math.random(); '/>";
    }

    /**
     * 生成表单令牌
     * 如果一个页面有多个表单，可指定前缀来区分
     * @param $prefix string
     * @return string html
     */
    function token($prefix='default'){
        $value=$prefix.'_'.\Yuri2::uniqueID();
        $html= "<input type='hidden' value='$value' name='naples_sys_auto_token'>";
        //添加到session
        session('sysNaples.form_tokens.'.$prefix,$value);
        return $html;
    }

    /**
     * 生成二维码
     * @param $value string
     * @param $size int
     * @param $water string 水印名字
     * @param $cache bool
     * @return string html
     */
    function qrCode($value='null',$size=3,$water='naples',$cache=false){
        $seri=serialize(['value'=>$value,'size'=>$size,'water'=>$water,'margin'=>1,'cache'=>$cache]);
        $seri=\Yuri2::encrypt($seri);
        $url=url('/SysNaples/Qrcode/index',['qr_content'=>$seri]);
        return "<img src='$url'>";
    }

    /**
     * 返回合理的导入html资源字符串
     * @param $res_html string 如果以html开头，表示绝对路径，否则表示在 html_pages_dir 下的路径
     * @return string
     */
    function import($res_html)
    {
        if ($res_html{0} != '/') {
            $res_html = '/' . $res_html;
        }
        $resArr = \Yuri2::explodeWithoutNull($res_html, '/');
        $head=count($resArr)>0?$resArr[0]:'';
        if ($head!='html'){
            $res_html = '/html/' . config('html_pages_dir') . $res_html;
        }
        $ext = \Yuri2::getExtension($res_html);
        $href = URL_PUBLIC . $res_html;
        $rel='';
        switch ($ext){
            case 'ico':
                $rel="<link rel = 'Shortcut Icon'  type='image/x-icon' href='$href' >";
                break;
            case 'js':
                $rel="<script type='text/javascript' src='$href' ></script>";
                break;
            case 'css':
                $rel="<link rel='stylesheet' type='text/css' href='$href' >";
                break;
        }

        return $rel;
    }

    /**
     * 数组转表格
     * @param $arr array 数组 数据源
     * @return string
     */
    function arrToHtmlTableBody($arr){
        $ths=$arr[0];
        $htmlThs='';$htmlTds='';
        if (!\Yuri2::isAssoc($ths)){
            foreach ($ths as $th){
                $htmlThs.="<th>$th</th>";
            }
            $htmlThs="\n<tr>$htmlThs</tr>";
            array_shift($arr);
        }else{
            foreach ($ths as $th=>$val){
                $htmlThs.="<th>$th</th>";
            }
            $htmlThs="\n<tr>$htmlThs</tr>";
        }
        foreach ($arr as $item){
            $htmlTds.="\n<tr>";
            foreach($item as $td){
                $htmlTds.="    \n<td>$td</td>";
            }
            $htmlTds.="\n</tr>";
        }
        $htmlTable="$htmlThs $htmlTds";
        return $htmlTable;
    }

    /**
     * 更聪明的echo
     * @param $var mixed
     * @param $filters string 过滤器
     */
    function smarterEcho($var,$filters='|e'){
        \Yuri2::smarterEcho($var,$filters);
    }

    /**
     * 生成编辑器的js代码 需要引用ueditor和naplesEditorFactory
     * @param $ID string
     * @param $isFull bool
     * @param $content string
     * @return string
     */
    function ueditor($ID='default',$isFull=false,$content=''){
        $urlSrv=$isFull?url("SysNaples/Ueditor/index/$ID"):'no_access';
        $style=$isFull?'min-height:280px':'min-height:100px';
        $isFull=$isFull?'true':'false';
        $rel="
            <script id='ue-box-$ID'  style='display:block;width:100 %;$style' name='ue-content' type='text/plain'>$content</script>
            <script>
                var naplesEditorHelper=new NaplesEditor();
                var ue_$ID=naplesEditorHelper.createEditor('ue-box-$ID',$isFull,'$urlSrv');
            </script>
";
        return $rel;
    }
    
    /**
     * 产生一个ueditor 渲染
     * @param $content string
     * @return string
     */
    function uparse($content){
        $pathUE=URL_PUBLIC.'/html/ueditor';
        $ranName=\Yuri2::uniqueID();
        $iframe=<<<EOT
<upares-box style=' width:100%;height:100%;overflow: auto;display:block' id='con_$ranName'>$content</upares-box>
<script>
    uParse('#con_$ranName',{rootPath : '$pathUE'})
</script>


EOT;
        return $iframe;
    }

    /**
     * 把数组转换为html代码，hidden表单隐藏域
     * @param $arr array
     * @return string
     */
    function arrToInputHidden($arr){
        $rel='<!-- 数组转表单隐藏域 begin -->'.RN;
        foreach ($arr as $k=>$v){
            if (is_string($v) or is_numeric($v)){
                $rel.="<input type='hidden' name='$k' value='$v' />".RN;
            }elseif(is_array($v)){
                foreach ($v as $vv){
                    $rel.="<input type='checkbox' name='{$k}[]' value='$vv' style='display: none' />.RN";
                }
            }
        }
        $rel.='<!-- 数组转表单隐藏域 end -->'.RN;
        return $rel;
    }

    function __toString()
    {
        return 'TplExtend obj naples';
    }
}

