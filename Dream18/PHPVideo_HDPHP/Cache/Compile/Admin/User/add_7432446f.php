<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>添加用户</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
	<script src="http://localhost/PHPUnion/Static/uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/uploadify/uploadify.css">
</head>
<body>
	<form action="http://localhost/PHPUnion/index.php?m=Admin&c=User&a=add" method="POST" enctype="multipart/form-data">
	<p class="bg-main bg-inverse text-center margin-little-bottom padding-small-top padding-small-bottom" >
		用户添加
	</p>
	<table class="table table-bordered table-hover table-condensed table-responsive">
		<tr>
			<td align="right">账户</td>
			<td><input type="text" name="username" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">昵称</td>
			<td><input type="text" name="nickname" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">用户组</td>
			<td>
				<select name="rid">
                            <?php
        //初始化
        $hd['list']['r'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($role)) {
            echo '';
        } else {
            $listId = 0;
            $listShowNum=0;
            $listNextId=0;
            foreach ($role as $r) {
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
                $hd['list'][r]['index']++;
                //第1个值
                $hd['list'][r]['first']=($listId == 0);
                //最后一个值
                $hd['list'][r]['last']= (count($role)-1 <= $listId);
                //总数
                $hd['list'][r]['total']++;
                //增加数
                $listId++;
                $listShowNum++;
                $listNextId+=1
                ?>
                        <option value="<?php echo $r['rid'];?>"><?php echo $r['rname'];?></option>
                    <?php }}?>
            	</select>
			</td>
		</tr>
		<tr>
			<td align="right">密码</td>
			<td><input type="password" name="password" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">确认密码</td>
			<td><input type="password" name="password_c" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">头像</td>
			<td>
				<input id="file" type="file" multiple="true">
				<img id="catimage" width="100" height="60" id="uploadify" style="display:none;"/>
				<!--上传头像按钮 Start-->
				<script type="text/javascript">
					$(function() {
						$('#file').uploadify({
	                        'formData'     : {//POST数据
	                            '<?php echo session_name();?>' : '<?php echo session_id();?>',
	                        },
	                        'fileTypeDesc' : '上传头像',//上传描述
	                        'fileTypeExts' : '*.gif; *.jpg; *.png',
	                        'swf'      : 'http://localhost/PHPUnion/Static/uploadify/uploadify.swf',
	                        'uploader' : '<?php echo U("avatar");?>',
	                        'buttonText':'上传头像',
	                        'fileSizeLimit' : '1024KB',
	                        'uploadLimit' : 1,//上传文件数
	                        'width':65,
	                        'height':25,
	                        'successTimeout':10,//等待服务器响应时间
	                        'removeTimeout' : 0.5,//成功显示时间
	                        'onUploadSuccess' : function(file, data, response) {
	                            data=$.parseJSON(data);
	                            $("[name='catimage']").val(data.path);
	                            $("#catimage").attr('src',data.url).show();

	                        }
                    	});
                    });
                </script>
			</td>
			<td>请上传头像</td>
		</tr>
		<tr>
			<td align="right">状态</td>
			<td>
				<label><input type="radio" name="user_status" value="1" checked />正常</label>
				<label><input type="radio" name="user_status" value="0" />锁定</label>
			</td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">锁定日期</td>
			<td>
				<input type="text" name="lock_time" value="<?php echo date('Y-m-d h:i:s');?>" />
			</td>
			<td>超过当前时间设置时间，用户自动解锁</td>
		</tr>
		<tr>
			<td align="right">年龄</td>
			<td><input type="text" name="age" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">积分</td>
			<td><input type="text" name="credits" value="0"/></td>
		</tr>
		<tr>
			<td align="right">性别</td>
			<td>
				<div class="form-group button-group radio">
					<label class="button active">
						<input name="sex" value="1" checked="checked" type="radio">
						<span class="icon icon-male"></span>
						男
					</label>
					<label class="button">
						<input name="sex" value="2" type="radio">
						<span class="icon icon-female"></span>
						女
					</label>
					<label class="button">
						<input name="sex" value="3" type="radio">
						<span class="icon icon-child"></span>
						保密
					</label>
				</div>
			</td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">邮箱</td>
			<td><input type="text" name="email" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">手机</td>
			<td><input type="text" name="mobile" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">Q Q</td>
			<td><input type="text" name="qq" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">签名</td>
			<td><textarea name="sign" cols="20" rows="2">这个人很懒,什么都没有留下！</textarea></td>
			<td>提示</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<button type="submit" class="button bg-sub">
					<i class="icon-check-square-o"></i>
					提交保存
				</button>
				<button type="button" class="button bg-main" onClick="location.href='<?php echo U('index');?>'">
					<i class="icon-th-list"></i>
					用户列表
				</button>
			</td>
		</tr>
	</table>
</form>
</body>
</html>