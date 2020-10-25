<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>推荐位列表</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
    <div class="container-layout">
        <div class="bg border margin-little-bottom padding-left padding-top padding-bottom">
            <button type="button" onClick="location.href='<?php echo U('index',array('mid'=> $_REQUEST['mid']));?>'" class="button bg">
                <i class="icon-th-list"></i>
                属性列表
            </button>
            <button type="button" onClick="location.href='<?php echo U('add',array('mid'=>$_REQUEST['mid']));?>'" class="button">
                <i class="icon-edit"></i>
                添加属性
            </button>
            <button type="button" onClick="location.href='<?php echo U('updateCache',array('mid'=> $hd['request']['mid']));?>'" class="button">
                <i class="icon-eraser"></i>
                更新缓存
            </button>
        </div>
    </div>

<div class="container-layout">
    <div class="bg border border padding margin-top">
        当前模型
        <select name="mid">
                    <?php
        //初始化
        $hd['list']['m'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($model)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($model as $m) {
                //开始值
                if ($listId<0) {
                    $listId++;
                    continue;
                }
                //步长
                if($listId!=$listNextId){$listId++;continue;}
                //显示条数
                if($listShowNum>=100)break;
                //第几个值
                $hd['list'][m]['index']++;
                //第1个值
                $hd['list'][m]['first']=($listId == 0);
                //最后一个值
                $hd['list'][m]['last']= (count($model)-1 <= $listId);
                //总数
                $hd['list'][m]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
            <option value="<?php echo $m['mid'];?>"     <?php if($hd['request']['mid']==$m['mid']){ ?>selected=''<?php } ?>><?php echo $m['model_name'];?></option>
            <?php }}?>
        </select>
    </div>
    <script>
        $("[name='mid']").change(function()
        {
            var mid = $(this).val();
           location.href="<?php echo U('index');?>&mid="+mid;
        })
    </script>
    <form method="POST" action="<?php echo U('edit');?>">
        <table class="table table-bordered table-hover table-condensed margin-top">
            <tr class="bg">
                <td class="hd-w100">fid</td>
                <td>属性名称</td>
                <td class="hd-w50">操作</td>
            </tr>
                    <?php
        //初始化
        $hd['list']['name'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($flag)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($flag as $name) {
                //开始值
                if ($listId<0) {
                    $listId++;
                    continue;
                }
                //步长
                if($listId!=$listNextId){$listId++;continue;}
                //显示条数
                if($listShowNum>=100)break;
                //第几个值
                $hd['list'][name]['index']++;
                //第1个值
                $hd['list'][name]['first']=($listId == 0);
                //最后一个值
                $hd['list'][name]['last']= (count($flag)-1 <= $listId);
                //总数
                $hd['list'][name]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
                <tr>
                    <td>
                        <?php echo $name;?>
                    </td>
                    <td>
                        <input type="text" name="flag[]" value="<?php echo $name;?>"/>
                    </td>
                    <td>
                       <a href="javascript:;" onclick="del(<?php echo $hd['get']['mid'];?>,<?php echo $hd['list']['name']['index'] - 1; ?>);">删除
                               </a>
                    </td>
                </tr>
            <?php }}?>
            <tr>
                <td colspan="3">
                    <input type="hidden" name="mid" value="<?php echo $hd['request']['mid'];?>" />
                    <button type="submit" class="button bg-sub">
                        <i class="icon-check-square-o"></i>
                        提交保存
                    </button>
                </td>
            </tr>
        </table>
</form>
</div>
</body>
</html>