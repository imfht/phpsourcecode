<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>修改用户</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
</head>
<body>
	<form action="<?php echo U('edit');?>" method="POST">
	<p class="bg-main bg-inverse text-center margin-little-bottom padding-small-top padding-small-bottom" >用户编辑</p>
	<table class="table table-bordered table-hover table-condensed table-responsive">
		<tr>
			<td align="right">账户</td>
			<td><input type="text" name="username" value="<?php echo $field['username'];?>" readOnly="true" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">昵称</td>
			<td><input type="text" name="nickname" value="<?php echo $field['nickname'];?>" /></td>
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
                        <option value="<?php echo $r['rid'];?>"     <?php if($r['rid']==$field['rid']){ ?>selected="selected"<?php } ?>><?php echo $r['rname'];?></option>
                    <?php }}?>
            	</select>
        	</td>
		</tr>
		<tr>
			<td align="right">密码</td>
			<td><input type="password" name="password" /></td>
			<td>不填写密码则不修改密码！</td>
		</tr>
		<tr>
			<td align="right">密码确认</td>
			<td><input type="password" name="password_c" /></td>
			<td>不填写密码则不修改密码！</td>
		</tr>
		<tr>
			<td align="right">头像</td>
			<td>
				<a class="button bg-sub input-file" href="javascript:void(0);">
					+ 选择图像<input size="100" type="file" name="avatar" />
				</a>
			</td>
			<td>请上传头像！</td>
		</tr>
		<tr>
			<td align="right">状态</td>
			<td>
				<label><input type="radio" name="user_status" value="1"     <?php if($field['user_status']==1){ ?>checked<?php } ?> />正常</label>
				<label><input type="radio" name="user_status" value="0"     <?php if($field['user_status']==0){ ?>checked<?php } ?> />锁定</label>
			</td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">锁定日期</td>
			<td>
				<input type="text" name="lock_time" value="<?php echo $field['lock_time'];?>" />
			</td>
			<td>超过当前时间设置时间，用户自动解锁</td>
		</tr>
		<tr>
			<td align="right">年龄</td>
			<td><input type="text" name="age" value="<?php echo $field['age'];?>" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">积分</td>
			<td><input type="text" name="credits" value="<?php echo $field['credits'];?>" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">性别</td>
			<td>
				<div class="form-group button-group radio">
					<label class="button     <?php if($field['sex']==1){ ?>active<?php } ?>">
						<input name="sex" value="1"     <?php if($field['sex']==1){ ?>checked<?php } ?> type="radio">
						<span class="icon icon-male"></span>
						男
					</label>
					<label class="button     <?php if($field['sex']==2){ ?>active<?php } ?>">
						<input name="sex" value="2"     <?php if($field['sex']==2){ ?>checked<?php } ?> type="radio">
						<span class="icon icon-female"></span>
						女
					</label>
					<label class="button     <?php if($field['sex']==3){ ?>active<?php } ?>">
						<input name="sex" value="3"     <?php if($field['sex']==3){ ?>checked<?php } ?> type="radio">
						<span class="icon icon-child"></span>
						保密
					</label>
				</div>
			</td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">邮箱</td>
			<td><input type="text" name="email" value="<?php echo $field['email'];?>" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">手机</td>
			<td><input type="text" name="mobile" value="<?php echo $field['mobile'];?>" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">Q Q</td>
			<td><input type="text" name="qq" value="<?php echo $field['qq'];?>" /></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">签名</td>
			<td><textarea name="sign" cols="20" rows="2"><?php echo $field['sign'];?></textarea></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">注册时IP</td>
			<td><?php echo $field['regip'];?></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">最后登录IP</td>
			<td><?php echo $field['lastip'];?></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">空间访问数</td>
			<td><?php echo $field['spec_num'];?></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">最后登录时间</td>
			<td><?php echo date('Y-m-d H:i:s',$field['logintime']);?></td>
			<td>提示</td>
		</tr>
		<tr>
			<td align="right">注册时间</td>
			<td><?php echo date('Y-m-d H:i:s',$field['regtime']);?></td>
			<td>提示</td>
		</tr>
		<tr>
			<td><input type="hidden" name="uid" value="<?php echo $hd['get']['uid'];?>" /></td>
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