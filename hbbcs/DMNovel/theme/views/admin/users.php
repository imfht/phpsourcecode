
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-5">
                    <h4>
                        <i class="icon-book"></i>
                        用户管理

                    </h4>
                </div>

                <div class="col-md-7 text-right">
                    <button type="submit" class="bg-primary btn" id="addChapter" title="增加新用户">
                        <i class="icon-plus-sign-alt"></i>

                    </button>
                    <!-- /input-group -->
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">

        <table class="table table-striped table-hover" width="100%" id="users_list_table">
            <thead>
            <tr>
                <th width="20px">ID</th>
                <th>用户名</th>
                <th>邮箱</th>
                <th>等级</th>
                <th width="100px">操作</th>
            </tr>
            </thead>

        </table>

    </div>


    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/dataTables.bootstrap.min.css"/>

    <script src="<?= THEMEPATH ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?= THEMEPATH ?>/js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('#users_list_table').dataTable({
                language: {
                    'url': '<?=THEMEPATH?>/js/dataTables.zh-CN.json'
                },
                "stateSave": false,
                "processing": true,
                "serverSide": true,
                "order": [[0, "asc"],[3,'desc']],
                "ajax": "<?= site_url('/admin/users/datatable/') ?>",
                "columns": [
                    {"data": "id"},
                    {"data": "name"},
                    {"data": "mail"},
                    {"data": "level"},
                    {"data": "action"}
                ],
                "createdRow": function (row, data) {
                    switch (data.level) {
                        case '1':
                            $('td', row).eq(3).text("普通用户");
                            break;
                        case '7':
                            $('td', row).eq(3).text("作者");
                            break;
                        case '9':
                            $('td', row).eq(3).text("管理员");
                            break;
                    }
                }
            });

        })
    </script>
