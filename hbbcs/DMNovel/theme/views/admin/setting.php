<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">
        <h4>配置列表</h4>
    </div>
    <div class="panel-body">
        新建、修改配置，不可删除，双击配置值、描述可进行编辑。可使用右键菜单。<b>请谨慎操作。</b>

        <div class="btn-group pull-right" role="group" aria-label="...">
            <button type="button" class="btn btn-default btn-warning" title="全部保存"
                id="save_modify"><i
                    class="glyphicon glyphicon-floppy-disk"></i>
                全部保存
            </button>
            <button type="button" class="btn btn-default btn-primary" openDialog="<?= site_url('/admin/setting/create') ?>" title="新建配置项"
                id="create_menu"><i
                    class="glyphicon glyphicon-plus"></i>
                新建配置项
            </button>
        </div>

    </div>
    <table class="table table-striped table-hover" id="setting_table">
        <thead>
        <tr>
            <th>#</th>
            <th>名称</th>
            <th>描述</th>
            <th>值</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

</div>
<form class="form" action="<?= site_url('admin/setting/filter') ?>" method="post" id="filterForm">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <h5>内容过滤
                        <small>每一行为一项内容</small>
                    </h5>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" type="submit">
                        <i class="icon-save"></i>
                        保存
                    </button>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <h6>需要过滤的内容：
                        <small>支持正则表达式</small>
                    </h6>
                    <textarea class="form-control" rows="8" name="filter_t"><?= $filter_t ?></textarea>
                </div>
                <div class="col-sm-6">
                    <h6>转换为：</h6>
                    <textarea class="form-control" rows="8" name="filter_c"><?= $filter_c ?></textarea>
                </div>
            </div>
        </div>
    </div>
</form>

<link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/dataTables.bootstrap.min.css"/>

<script src="<?= THEMEPATH ?>/js/jquery.dataTables.min.js"></script>
<script src="<?= THEMEPATH ?>/js/dataTables.bootstrap.min.js"></script>
<script src="<?= THEMEPATH ?>/js/validator.js"></script>

<script type="text/javascript">
    $(function () {
        var table = $('#setting_table').DataTable({
            language: {
                'url': '<?=THEMEPATH?>/js/dataTables.zh-CN.json'
            },
            'stateSave': true,
            "processing": true,
            "serverSide": true,
            "ajax": "<?= site_url('/admin/setting/page/') ?>",
            "columns": [
                {"data": "id"},
                {"data": "title"},
                {"data": "desc"},
                {"data": "value"}
            ]
        });

        //提交表单
        $('#filterForm').formValidator({
            sending: {
                type: 'ajax',
                success: function (data) {
                    show_error(data);
                },
                error: function () {
                    show_error("提交失败！");
                }
            }
        });
        //双击td进行修改
        $('td[id]').dblclick(function () {
            if ($(this).find('div').length >= 1) {
                return;
            }

            id = $(this).parent('tr').attr('id');
            field = $(this).attr('id');
            value = $(this).text();
            //将td内容换成输入框
            str = '<div class="input-group" id="' + id + '"><input type="text" id="input_' + id + '" class="form-control" name="' + field + '" value="' + value + '" />' +
                '<span class="input-group-btn"><button class="btn  btn-warning" id="submit_modify_' + id + '">' +
                '<i class="glyphicon glyphicon-ok" /></button><button class="btn btn-warning cancel"><i class="glyphicon glyphicon-remove"></button> </span></div>';
            $(this).html(str);
            $('#input_' + id).focus().select();
            tdObj = $(this);
            //提交输入框内容
            $('#submit_modify_' + id).click(function () {
                //获取td对象
                $value = $(this).parent('span').prev('input').val();//获取修改后值
                $.post('setting/edit', {
                    id: id,//修改项的ID
                    field: field,//修改列的名称
                    value: $value//修改后的值
                }, function (data) {
                    if (data) {
                        alert(data);
                    } else {
                        tdObj.html($value);
                    }
                });
            });
            $('td').on('click', '.cancel', function () {
                tdObj.html(value);
            })
        });


        //点击全部保存进行修改
        $('#save_modify').click(function () {
            $('.input-group').each(function () {
                tdObj = $(this).parent('td');
                id = $(this).attr('id');
                field = $(this).children('input').attr('name');
                value = $(this).children('input').val();
                $.ajax({
                    type: "post",
                    url: "<?= site_url('/admin/setting/edit') ?>",
                    data: {
                        id: id,//修改项的ID
                        field: field,//修改列的名称
                        value: value//修改后的值
                    },
                    async: false,
                    success: function (data) {
                        if (data) {
                            alert(data);
                        } else {
                            tdObj.html(value);
                        }
                    }
                });
            });
        });
    });
</script>
