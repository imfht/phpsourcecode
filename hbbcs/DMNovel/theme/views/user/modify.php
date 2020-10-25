<form class="form-horizontal" role="form" id="modifyForm" action="<?= site_url("user/modify") ?>" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">
            <b>修改密码、邮箱</b>
        </div>

        <div class="panel-body">

            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#password" role="tab" data-toggle="tab">密码</a></li>
                <li role="presentation"><a href="#mail" role="tab" data-toggle="tab">邮箱</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="password">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">原密码:</label>

                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="old_password" placeholder="PASSWORD">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">新密码:</label>

                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="new_password" placeholder="PASSWORD">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">重复新密码:</label>

                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="re_password" placeholder="PASSWORD">
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="mail">
                    <div class="well">
                        您的原邮箱为：<strong><?= $user['mail'] ?></strong><br/>
                        设置邮箱地址是为了 找回密码，或者您收藏的小说在更新后及时通知您。
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">邮箱:</label>

                        <div class="col-sm-6">
                            <input type="email" class="form-control" name="mail" placeholder="EMAIL">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"> 有更新通过邮件通知我。
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="panel-footer text-center">
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">保 存</button>
                <button type="reset" class="btn btn-default">重 置</button>
            </div>
        </div>
    </div>

</form>


<script src="<?= THEMEPATH ?>/js/validator.js"></script>

<script type="text/javascript">
    $(function () {
        //提交表单
        $('#modifyForm').formValidator({
            sending: {
                type: 'ajax',
                success: function (data) {
                    var e = $.parseJSON(data);
                    if (e.error) {
                        show_error({'message': e.error, 'color': 'danger'});
                    } else {
                        show_error(e.success);

                    }
                },
                error: function () {
                    show_error("提交失败！");
                }
            }
        });

    });
</script>