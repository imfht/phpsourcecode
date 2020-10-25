<form class="form-horizontal" id="settingForm" action="<?= site_url('/admin/setting/edit') ?>" method="post">
    <div class="btn-group pull-right" role="group" aria-label="...">
        <button type="submit" class="btn btn-primary" id="submit"><i class="glyphicon glyphicon-floppy-disk"></i> 保存
        </button>
        <button type="reset" class="btn btn-info"><i class="glyphicon glyphicon-retweet"></i> 重置</button>
    </div>


    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab"
                                                      data-toggle="tab">必填项</a>
            </li>

        </ul>

        <input type="hidden" id="id" name="id" value="<?= isset($setting) ? $setting['id'] : "" ?>"/>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <div class="panel panel-default tabsContentBox">
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">名称</label>

                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="name" placeholder="Name" name="name"
                                       value="<?= isset($setting) ? $setting['name'] : "" ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">描述</label>

                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="desc" placeholder="Desc" name="desc"
                                       value="<?= isset($setting) ? $setting['desc'] : "" ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">值</label>

                            <div class="col-sm-9">
                                <textarea name="value" class="form-control"><?= isset($setting) ? $setting['value'] : "" ?></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script type="text/javascript">
    $(function () {

    });
</script>