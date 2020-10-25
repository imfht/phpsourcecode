<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="Keywords" content="代码工厂">
        <meta name="Description" content="每天自学一点点，每天进步一点点">
        <meta name="author" content="Clmao">
        <meta http-equiv="Cache-Control" content="no-transform" />
        <meta http-equiv="Cache-Control" content="no-siteapp"/>
        <title>ClmaoBlog V1.0 安装</title>
        <link rel="apple-touch-icon" href="/Public/appicon.png">
        <link rel="shortcut icon" href="/Public/appicon.png">
        <link href="/Public/zui/zui.min.css" rel="stylesheet">
        <script src="/Public/js/jquery.min.js"></script>
        <script src="/Public/zui/zui.min.js"></script>
        <style>
            .panel{margin: 0 auto;max-width: 80%;}
            .btn{margin-right: 20px;}
            .input-group{margin-bottom: 10px;max-width: 80%;}
        </style>
    </head>
    
   
 <body>
        <nav class="navbar navbar-default" role="navigation">
            <ul class="nav navbar-nav nav-justified">
                <li class="cat-item nav-system-home active"><a href="/">安装协议</a></li>
                <li class="cat-item active"><a href="javascript:;">环境检测</a></li>
                <li class="cat-item"><a href="javascript:;">创建数据库</a></li>
                <li class="cat-item"><a href="javascript:;">安装</a></li>
                <li class="cat-item"><a href="javascript:;">完成</a></li>
            </ul>
        </nav>
        <div class="panel panel-success">
            <div class="panel-heading">ClmaoBlog环境检测</div>
            <div class="panel-body">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr><th colspan="3" class="space text-primary">运行环境检测</th></tr>
                        <tr>
                            <th>项目</th>
                            <th>所需配置</th>
                            <th>当前配置</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(is_array($env)): $i = 0; $__LIST__ = $env;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($item[0]); ?></td>
                            <td><?php echo ($item[1]); ?></td>
                            <td class="text-<?php echo ($item[4]); ?>"><?php echo ($item[3]); ?></td>       
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                   
                    </tbody>
                </table>
                
                 <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr><th colspan="3" class="space text-primary">目录、文件权限检查</th></tr>
                        <tr>
                            <th>目录/文件</th>
                            <th>所需状态</th>
                            <th>当前状态</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php if(is_array($dirfile)): $i = 0; $__LIST__ = $dirfile;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                                <td><?php echo ($item[3]); ?></td>
                                <td>可写</td>
                                <td class="text-<?php echo ($item[2]); ?>"><?php echo ($item[1]); ?></td>   
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>

                    </tbody>
                </table>
                
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr><th colspan="3" class="space text-primary">函数依赖性检查</th></tr>
                        <tr>
                            <th>函数名称</th>
                            <th>检查结果</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(is_array($func)): $i = 0; $__LIST__ = $func;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($item[0]); ?>()</td>
                            <td class="text-<?php echo ($item[2]); ?>"><?php echo ($item[1]); ?></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>

                    </tbody>
                </table>
                
                <p><a href="<?php echo U('Index/index');?>" class="btn btn-default">上一步</a><a href="<?php echo U('Install/step2');?>" class="btn btn-primary">下一步</a></p>
            </div>
        </div>

    </body>
</html>