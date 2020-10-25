<?php if (!defined('THINK_PATH')) exit();?>
<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form method="post" valid action="<?php echo ($insertUrl); ?>">
<div class="row">
    <div class=" col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <div class="form-group">
                    <label for="field_account">登陆账号</label>
                <input  value="<?php echo ($vo["account"]); ?>"  name="account" type="text" placeholder="" id="field_account" class="form-control ">
                </div>
            </div>
    <div class=" col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <div class="form-group">
                    <label for="field_password">登陆密码</label>
                <input  value="<?php echo ($vo["password"]); ?>"  name="password" type="text" placeholder="" id="field_password" class="form-control ">
                </div>
            </div>
    <div class=" col-md-3 col-sm-3 col-xs-3 col-lg-3">
                    <div class="form-group">
                    <label for="field_name">姓名</label>
                <input  value="<?php echo ($vo["name"]); ?>"  name="name" type="text" placeholder="" id="field_name" class="form-control ">
                </div>
            </div>
    <div class=" col-md-3 col-sm-3 col-xs-3 col-lg-3">
                    <div class="form-group">
                    <label for="field_sex">性别</label>
                <select class="form-control" name="sex">
            <option value="1">男</option>
            <option value="2">女</option>
        </select>
                </div>
            </div>
    <div class=" col-md-3 col-sm-3 col-xs-3 col-lg-3">
                    <div class="form-group">
                    <label for="field_email">邮箱</label>
                <input  value="<?php echo ($vo["email"]); ?>"  name="email" type="text" placeholder="" id="field_email" class="form-control ">
                </div>
            </div>
    <div class=" col-md-3 col-sm-3 col-xs-3 col-lg-3">
                    <div class="form-group">
                    <label for="field_mobile">电话</label>
                <input  value="<?php echo ($vo["mobile"]); ?>"  name="mobile" type="text" placeholder="" id="field_mobile" class="form-control ">
                </div>
            </div>
    <div class=" col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <div class="form-group">
                    <label for="field_rid">角色</label>
                <select class="form-control" name="sex">
            <?php echo roleOpt();?>
        </select>
                </div>
            </div>
    <div class=" col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <div class="form-group">
                    <label for="field_area_code">地区代码</label>
                <input  value="<?php echo ($vo["area_code"]); ?>"  name="area_code" type="text" placeholder="" id="field_area_code" class="form-control ">
                </div>
            </div>
</div>

</form>
                        </div>
                        </div>
                    </div>
                </div>