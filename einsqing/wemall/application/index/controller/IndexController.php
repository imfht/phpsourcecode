<?php
namespace app\index\controller;
use think\Controller;
class IndexController extends Controller
{
    public function index()
    {
        $path = "application\\index\\controller\\index.php";

        // 定义输出文字
        $html = "<p>我是 [path] 文件的index方法</p>";

        // 调用temphook钩子, 实现钩子业务
        hook('temphook', ['data'=>$html]);

        // 替换path标签
        return str_replace('[path]', $path, $html);
    }
}
