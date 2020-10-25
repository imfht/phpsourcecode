<!DOCTYPE HTML>
<html>
<head>
	<title>Debug-[<?=\nb\Request::ins()->host?>]</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<style type="text/css">
		*{padding: 0px;margin: 0px;}
		body{  font-family: '微软雅黑'; color: #333; font-size: 14px; }
		h2{margin-bottom: 5px;padding-top: 10px;}
		table{border-collapse:collapse;}
		td{border-right: 1px solid #CCC;border-bottom: 1px solid #CCC;background: #EEEFFF;padding: 2px 4px 2px 4px;min-width: 50px;}
        .content{margin-left: 80px;margin-right: 80px;}
        .breakall{word-break:break-all;}
		.nav{width:auto;height: auto;position: fixed!important;position: absolute;top:0px!important;top:0px;left:0px;padding-right:0px;top: expression(eval(document.compatMode && document.compatMode=='CSS1Compat') ? documentElement.scrollTop+(documentElement.clientHeight - this.clientHeight):document.body.scrollTop+(document.body.clientHeight - this.clientHeight));}
		.nav ul{list-style: none;}
		.nav ul li{padding:5px; background: #ccc;border-bottom: 1px solid #AAA;padding-right:10px}
		.nav ul li:hover{background: #BBB;}
		.nav a{text-decoration: none;color: #505050}
		.copyright{ padding: 12px 0px; color: #999;}
		.copyright a{ color: #000; text-decoration: none; }
		.model { border-bottom: 1px solid #00a0e9; }
		.model h1{ margin-top: 3px;}
		.model h3{ margin: 3px; cursor: pointer;}
		.small{font-size: 12px;}
	</style>
</head>
<body>
<script>
	function show(id) {
		if (document.getElementById(id).style.display == "none") {
			document.getElementById(id).style.display = ""; //展开
		}
		else {
			document.getElementById(id).style.display = "none"; //隐藏
		}
	}
</script>
<div class="nav">
    <ul>
        <li><a href="/debug/">refresh</a></li>
        <?php foreach ($result as $k=>$v){?>
            <li><a href="#<?=$k?>" title="<?=$v['url'] ?>"><?php echo date("H:i:s",$v['start'])?></a></li>
        <?php }?>
    </ul>
</div>
<div class="content">
    <?php
    foreach($result as $key => $val) {
        if(!empty($val['e'])){
            $color = '#FF4040';
        }
        else{
            $color = '#595959';
        }
        ?>
        <div class="model">
            <div  style="padding-left: 5px;padding-bottom: 5px;" id="<?=$key?>">
                <h1 style="color: <?=$color?>"><?php echo date("Y-m-d H:i:s",$val['start'])?></h1>
                <div><?=$val['url'] ?></div>
                <div>Spend: <?=$val['spend']?>s &nbsp;&nbsp;Mem: <?=$val['mem']?>&nbsp;&nbsp;Method: <?=$val['method']?> &nbsp;&nbsp;ip: <?=$val['ip']?></div>
            </div>

            <?php if(!empty($val['log'])) {?>
                <h3 onclick="show('log-<?=$key?>')">Log</h3>
                <div id="log-<?=$key?>">
                    <table>
                        <?php foreach ($val['log'] as $k=>$v ){ ?>
                            <tr>
                                <td style="cursor: pointer;" valign="top" onclick="show('log-<?=$key?>-<?=$k?>')"><?=\nb\Debug::optimize($v['k']) ?></td>
                                <td class="breakall" id="log-<?=$key?>-<?=$k?>"><?=\nb\Debug::optimize($v['v']) ?></td>
                            </tr>
                        <?php }?>
                    </table>
                </div>
            <?php }?>

            <?php if(!empty($val['get'])) {?>
                <h3 onclick="show('get-<?=$key?>')">Get</h3>
                <div id="get-<?=$key?>">
                    <table>
                        <?php foreach ($val['get'] as $k => $v ){ ?>
                            <tr><td><?=$k ?></td><td class="breakall"><?=is_array($v)?\nb\Debug::e($v):htmlspecialchars($v) ?></td></tr>
                        <?php }?>
                    </table>
                </div>
            <?php }?>

            <?php if(!empty($val['post'])) {?>
                <h3 onclick="show('post-<?=$key?>')">Post</h3>
                <div id="post-<?=$key?>">
                    <table>
                        <?php foreach ($val['post'] as $k => $v ){ ?>
                            <tr><td><?=$k ?></td><td class="breakall"><?=is_array($v)?\nb\Debug::optimize($v):htmlspecialchars($v) ?></td></tr>
                        <?php }?>
                    </table>
                </div>
            <?php }?>

            <?php if(!empty($val['file'])) {?>
                <h3 onclick="show('file-<?=$key?>')">File</h3>
                <div id="get-<?=$key?>">
                    <table>
                        <?php foreach ($val['file'] as $k => $v ){ ?>
                            <tr><td><?=$k ?></td><td class="breakall"><?=is_array($v)?\nb\Debug::optimize($v):htmlspecialchars($v) ?></td></tr>
                        <?php }?>
                    </table>
                </div>
            <?php }?>

            <?php if(!empty($val['e'])){?>
                <h3 onclick="show('e-<?=$key?>')">Error</h3>
                <div id="e-<?=$key?>">
                    <table>
                        <tr>
                        <?php foreach ($val['e'] as $k => $v ){ ?>
                            <?php if($v['type'] != 8){ ?>
                            <td class="breakall" style="background: #FFB6C1;">[<?=$v['type']?>]&nbsp;<?=$v['message']?>&nbsp;(<?=$v['file']?>: <?=$v['line']?>)</td></tr>
                            <?php }else{?>
                            <td class="breakall">[<?=$v['type']?>]&nbsp;<?=$v['message']?>&nbsp;(<?=$v['file']?>: <?=$v['line']?>)</td></tr>
                            <?php }?>
                        <?php }?>
                        </tr>
                    </table>
                </div>
            <?php }?>

            <?php if(!empty($val['sql'])){?>
                <h3 onclick="show('sql-<?=$key?>')">Sql<span class="small"> count: <?=count($val['sql'])?></span></h3>
                <div id="sql-<?=$key?>">
                    <table>
                        <?php foreach ($val['sql'] as $v){?>
                            <tr><td colspan="2"><?=htmlspecialchars($v['sql']) ?></td></tr>
                            <?php
                            $i = 1;
                            if(!empty($v['param']))
                                foreach ($v['param'] as $v){
                                    ?>
                                    <tr><td width="10px;"><?=$i++; ?></td><td class="breakall"><?=htmlspecialchars($v)?></td></tr>
                            <?php }}?>
                    </table>
                </div>
            <?php }?>

            <?php if(!empty($val['session'])) {?>
                <h3 onclick="show('session-<?=$key?>')">Session</h3>
                <div id="session-<?=$key?>" style="display: none">
                    <table>
                        <?php foreach ($val['session'] as $k => $v ){ ?>
                            <tr><td><?=$k ?></td><td class="breakall"><?=\nb\Debug::optimize($v) ?></td></tr>
                        <?php }?>
                    </table>
                </div>
            <?php }?>

            <?php if(!empty($val['server'])){?>
                <h3 onclick="show('server-<?=$key?>')">Server</h3>
                <div id="server-<?=$key?>" style="display: none">
                    <table>
                        <?php foreach ($val['server'] as $k => $v ){ ?>
                            <tr ><td><?=$k ?></td><td class="breakall"><?=\nb\Debug::optimize($v) ?></td></tr>
                        <?php }?>
                    </table>
                </div>
            <?php }?>

            <?php if(!empty($val['cookie'])) {?>
                <h3 onclick="show('cookie-<?=$key?>')">Cookie</h3>
                <div id="cookie-<?=$key?>">
                    <table>
                        <?php foreach ($val['cookie'] as $k => $v ){ ?>
                            <tr><td><?=$k ?></td><td class="breakall"><?=\nb\Debug::optimize($v) ?></td></tr>
                        <?php }?>
                    </table>
                </div>
            <?php }?>



            <?php if(!empty($val['runfile'])){?>
                <h3 onclick="show('runfile-<?=$key?>')">Trace</h3>
                <div id="runfile-<?=$key?>">
                    <table>
                        <?php foreach ($val['runfile'] as  $v ){ ?>
                            <tr><td><?=$v ?></td></tr>
                        <?php }?>
                    </table>
                </div>
            <?php }?>

            <div style="height: 20px;"></div>
        </div>
    <?php }?>

    <div class="copyright">
        <p><a title="官方网站" href="https://nb.cx" target="_blank">NB Framework</a><sup><?=__VER__;?> <?=__PHASE__;?></sup> { Fast & Simple OOP PHP Framework } -- [ We Can Do It Just NB Framework ]</p>
    </div>
</div>

</body>
</html>