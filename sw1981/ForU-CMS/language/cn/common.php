<?php
// common
$_lang['illegal'] = '非法访问页面!';
$_lang['404'] = '您访问的页面不存在!';
$_lang['NA'] = 'N/A';
$_lang['error_text'] = 'Error: ';
$_lang['keyword'] = '请输入要查找的关键字';
$_lang['time_limit'] = '短时间内请勿重复操作!';
$_lang['msg_success'] = '操作成功!';
$_lang['msg_failed'] = '获取数据失败,无法完成操作!';
$_lang['tryagain'] = '操作失败, 请稍后重试!';
$_lang['qrcode_faild'] = '对应二维码创建失败!';
$_lang['verifycode_faild'] = '请核对验证码并重试!';
$_lang['tpl_error'] = '对应模板文件不存在!';
$_lang['mkdir_error'] = '可能由于权限不足, 创建对应目录失败!';
// mail
$_lang['mail']['success'] = '邮件投递成功！';
$_lang['mail']['failed'] = '邮件投递失败！';
$_lang['mail']['subscribe'] = '邮件订阅成功，谢谢您对我们的支持！';
// reg
$_lang['reg']['name_required'] = '请填写姓名!';
$_lang['reg']['password_required'] = '请填写密码!';
$_lang['reg']['cpassword_required'] = '请核对确认密码并重试!';
$_lang['reg']['email_required'] = '请填写电子邮箱！';
$_lang['reg']['email_faild'] = '请核对电子邮箱并重试！';
$_lang['reg']['id_existing'] = '账户已存在，请修改后重试!';
$_lang['reg']['email_existing'] = 'Email已存在，请修改后重试!';
$_lang['reg']['email_error'] = 'Email格式不对，请修改后重试!';
$_lang['reg']['email_subject'] = '[system_name] 恭喜您注册成功！';
$_lang['reg']['email_body'] = '尊敬的用户，您好！<br>您的注册信息如下：<p>账户：[user_name]<br>密码：[user_password]</p>请保存好您的用户信息。感谢您对我们的支持！';
$_lang['reg']['success'] = '注册成功';
$_lang['reg']['failed'] = '注册失败';
// login
$_lang['login']['success'] = '恭喜您，登陆成功!';
$_lang['login']['not_match'] = '信息不匹配!';
$_lang['login']['tip'] = '请登陆后继续操作！';
// reset password
$_lang['reset_password']['email_subject'] = '[system_name]-重置密码 您可以通过正文中的链接重置密码';
$_lang['reset_password']['email_body'] = '尊敬的用户，您好！<br><p>您可以通过如下链接，重置密码：<br><a href="[system_domain]/user.php?act=psw_reset&u_email=[u_email]&rand=[randstr]#main" target="_blank">点击该链接进行密码重置</a></p>';
$_lang['admin_reset_password']['email_subject'] = '[system_name]-密码已重置 您可以通过正文中的查看密码';
$_lang['admin_reset_password']['email_body'] = '尊敬的用户，您好！<br><p>您的密码已重置为：[u_psw]<br>请您及时的登陆您的账号并修改密码</p>';
$_lang['reset_password']['success'] = '密码找回信息已发往您的邮箱，请注意查收!';
$_lang['reset_result']['success'] = '密码重置成功!';
$_lang['reset_result']['failed'] = '密码重置失败!';
// user center
$_lang['uc']['userinfor']['success'] = '用户信息编辑成功!';
$_lang['uc']['check']['failed'] = '请登陆后继续操作!';
$_lang['uc']['check']['conflict'] = '该用户已在其他地方登陆, 请确认情况后重新登录!';
$_lang['uc']['point']['error'] = '积分信息有误!';
$_lang['uc']['point']['cost_faild'] = '积分信息有误!';
$_lang['uc']['user_enable'] = '用户尚未通过审核,请与24小时后重试登陆!';
// order
$_lang['order']['remove_failed'] = '当前订单状态无法删除';
