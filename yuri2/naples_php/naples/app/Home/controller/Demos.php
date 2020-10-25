<?php 
/** 
 * 该控制器文件由naples脚手架生成
 * 创建时间 2017/02/28 15:15:46
 */
namespace naples\app\Home\controller;

use naples\lib\base\Controller;

class Demos extends Controller
{ //class Demos begin

    /** 构造函数 */
    function __construct(){
//        config('debug',false);
    }

    /** 主页 */
    public function index(){
        $this->render();
        return ;
    }
                    
    /**
     * debug工具
     */
    public function debug(){
        config('debug',true);
        config('show_debug_btn',true);
        $arr=['title'=>'我是二维数组','content'=>['A','B','C']];
        $this->assign('arr',$arr);
        trace('测试数组',$arr);
        $this->render();
        return ;
    }
                
    /**
     * 错误捕捉
     */
    public function errorCatch(){
        config('debug',true);
        config('show_debug_btn',true);
        $this->render();
        return ;
    }
                
    /**
     * 注释引擎
     * @method get
     */
    public function docEngine(){
        $this->render();
        return ;
    }
                
    /**
     * 富文本编辑器
     */
    public function editor(){
        config('debug',true);
        $token='demo_ueditor';
        $content='Hello,naples!';
        \Yuri2::delDir(PATH_UE_UPLOAD.DS.$token);
        $parse=<<<ETO
        
<pre class="brush:html;toolbar:false">&lt;!--extend SysNaples/Index/base_bootstrap--&gt;
&lt;block_title&gt;富文本编辑器的支持&lt;/block_title&gt;
&lt;block_head&gt;
    {{inc SysNaples/Index/editor}}
    &lt;style&gt;body{background-color: rgb(238,238,238)}&lt;/style&gt;
&lt;/block_head&gt;
&lt;block_body&gt;
    &lt;div class=&quot;jumbotron&quot;&gt;
       &lt;div class=&quot;container&quot;&gt;
            &lt;h1&gt;集成富文本编辑器&lt;/h1&gt;&lt;br/&gt;
            &lt;div class=&quot;row&quot;&gt;
               &lt;div class=&quot;col-md-6&quot;&gt;
                  &lt;!-- 编辑器本体 --&gt;  
                  {{ue-full Yuri2}}
               &lt;/div&gt;
               &lt;div class=&quot;col-md-6&quot;
                style=&quot;height: 450px;&quot;&gt;
                  &lt;!-- 编辑内容渲染 --&gt;  
                  {{up parse}}
               &lt;/div&gt;
            &lt;/div&gt;
       &lt;/div&gt;
    &lt;/div&gt;
&lt;/block_body&gt;</pre>

ETO;
        $this->assign('parse',$parse);
        $this->assign('token',$token);
        $this->assign('content',$content);
        $this->render();
        return ;
    }
                
    /**
     * 二维码生成
     */
    public function qrcode(){
        $content=get('content')?get('content'):'Hello,naples!';
        $this->assign('content',$content);
        $this->render();
        return ;
    }
                
    /**
     * 验证码生成
     */
    public function captcha(){
        $cap_right=$this->checkCaptcha(get('cap'));
        $this->assign('cap_right',$cap_right);
        $this->render();
        return ;
    }
                
    /**
     * 表单令牌
     */
    public function formToken(){
        if (get('content')){
            $is_sub=true;
            $token_right=$this->checkToken();
            $this->assign('token_right',$token_right);
        }else{
            $is_sub=false;
        }
        $this->assign('is_sub',$is_sub);
        $this->render();
        return ;
    }

} //class Demos end