<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>添加视频</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<div class="container-layout">
	<div class="bg border margin-little-bottom margin-top padding-left padding-top padding-bottom">
		<button type="button" onClick="location.href='<?php echo U('show',array('cid'=> $hd['get']['cid']));?>'" class="button">
			<i class="icon-th-list"></i>
			视频列表
		</button>
		<button type="button" onClick="location.href='<?php echo U('add', array('cid'=> $hd['get']['cid']));?>'" class="button bg">
			<i class="icon-edit"></i>
			添加视频
		</button>
	</div>
</div>
<div class="container-layout">
	<form action="<?php echo U('add');?>" method="POST" enctype="multipart/form-data">
		<div class="line">
			<!-- 左侧 -->
			<div class="x9">
				<table class="table table-hover table-bordered">
					<tr>
						<td width="150" align="right">视频标题</td>
						<td>
							<input type="text" name="title" />
							<label><input type="checkbox" name="new_window" checked="checked" value="1" />是否新窗口</label>
						</td>
					</tr>
					<tr>
						<td align="right">频道栏目</td>
						<td>
							<select name="cid">
								        <?php
        //初始化
        $hd['list']['vo'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($cate)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($cate as $vo) {
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
                $hd['list'][vo]['index']++;
                //第1个值
                $hd['list'][vo]['first']=($listId == 0);
                //最后一个值
                $hd['list'][vo]['last']= (count($cate)-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
									<option value="<?php echo $vo['cid'];?>"     <?php if($vo['cid']==$hd['get']['cid']){ ?>selected="selected"<?php } ?>><?php echo $vo['_name'];?></option>
								<?php }}?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right">发布者</td>
						<td>
							<input type="text" name="uid" value="<?php echo $_SESSION['username'];?>"/>
							<span>请输入用户账户,不要输入昵称。</span>
						</td>
					</tr>
					<tr>
						<td align="right">SEO标题</td>
						<td><input type="text" name="seo_title"/></td>
					</tr>
					<tr>
						<td align="right">关键词</td>
						<td><input type="text" name="keywords"/></td>
					</tr>
					<tr>
						<td align="right">视频上传</td>
						<td><input type="file" name="video_path" /></td>
					</tr>
					<tr>
						<td align="right">视频简介</td>
						<td><textarea name="content" rows="8" style="width:100%;"></textarea></td>
					</tr>
					<tr>
						<td align="right">Tag</td>
						<td><input type="text" name="tag" /></td>
					</tr>
					<tr>
						<td align="right">推荐位</td>
						<td>
							        <?php
        //初始化
        $hd['list']['vo'] = array(
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
            foreach ($flag as $vo) {
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
                $hd['list'][vo]['index']++;
                //第1个值
                $hd['list'][vo]['first']=($listId == 0);
                //最后一个值
                $hd['list'][vo]['last']= (count($flag)-1 <= $listId);
                //总数
                $hd['list'][vo]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
								<label><input type="checkbox" name="fid[]" value="<?php echo $vo['fid'];?>"/> <?php echo $vo['fname'];?></label>
							<?php }}?>
						</td>
					</tr>
					<tr>
						<td align="right">访问方式</td>
						<td>
							<label><input type="radio" name="url_type" value="1">静态访问</label>
							<label><input type="radio" name="url_type" value="2">动态访问</label>
							<label><input type="radio" name="url_type" checked="checked" value="3">继承栏目</label>
						</td>
					</tr>
				</table>
			</div>
			<!-- 左侧 End -->

			<!-- 右侧 -->
			<div class="x3">
				<table class="table table-hover table-bordered">
					<tr class="bg">
						<td>缩略图</td>
					</tr>
					<tr>
						<td>
							<img src="http://localhost/PHPUnion/Static/image/upload_pic.png" width="150" height="100" />
							<input type="file" name="thumb"/>
						</td>
					</tr>
					<tr class="bg">
						<td>转向链接</td>
					</tr>
					<tr>
						<td><input type="text" name="redirecturl"/></td>
					</tr>
					<tr class="bg">
						<td>排序</td>
					</tr>
					<tr>
						<td><input type="text" name="arc_sort" value="0"/></td>
					</tr>
					<tr class="bg">
						<td>播放次数</td>
					</tr>
					<tr>
						<td><input type="text" name="click" value="0"/></td>
					</tr>
					<tr class="bg">
						<td>html文件名</td>
					</tr>
					<tr>
						<td><input type="text" name="html_path"/></td>
					</tr>
					<tr class="bg">
						<td>状态</td>
					</tr>
					<tr>
						<td>
							<label><input type="radio" name="content_status" value="0"/> 未审核</label>
							<label><input type="radio" name="content_status" value="1" checked="checked"/> 已审核</label>
							<label><input type="radio" name="content_status" value="2"/> 草稿</label>
						</td>
					</tr>
					<tr class="bg">
						<td>发布时间</td>
					</tr>
					<tr>
						<td><input type="text" name="addtime" /></td>
					</tr>
				</table>
			</div>
			<!-- 右侧 End -->
		</div>
		<div class="border margin-little-top padding">
			<button type="submit" class="button bg-main">
				提交保存
			</button>
		</div>
	</form>
</div>
</body>
</html>