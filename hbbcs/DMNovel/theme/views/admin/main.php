<?php include VIEWPATH . "admin/header.php" ?>

<div class="row">

    <div class="col-md-2">
        <div class="list-group" id="menu">
            <a href="#" class="list-group-item active">
                <i class="icon-home"></i>
                管理首页
            </a>
            <a href="#" class="list-group-item" data-addtab="setting" data-url="<?= site_url('/admin/setting') ?>">
                <i class="icon-cogs"></i>
                系统设置
            </a>
            <a href="#" class="list-group-item" data-addtab="category" data-url="<?= site_url('/admin/category') ?>">
                <i class="icon-folder-open"></i>
                分类设置
            </a>
            <a href="#" class="list-group-item" data-addtab="users" data-url="<?= site_url('/admin/users') ?>">
                <i class="icon-user"></i>
                用户管理
            </a>
            <a href="#" class="list-group-item" data-addtab="story" data-url="<?= site_url('/admin/story') ?>">
                <i class="icon-book"></i>
                小说列表
            </a>
            <a href="#" class="list-group-item" data-addtab="chapter_list" data-url="<?= site_url('/admin/chapter/list/') ?>">
                <i class="icon-list-alt"></i>
                章节列表
            </a>
            <a href="#" class="list-group-item" data-addtab="chapter" data-url="<?= site_url('/admin/chapter/') ?>">
                <i class="icon-file-text-alt"></i>
                发布章节
            </a>
            <a href="#" class="list-group-item" data-addtab="capture" data-url="<?= site_url('/admin/collect_setting') ?>">
                <i class="icon-cog"></i>
                采集设置
            </a>
            <a href="#" class="list-group-item" data-addtab="capture_book" data-url="<?= site_url('/admin/collect') ?>">
                <i class="icon-cloud-download"></i>
                采集小说
            </a>
        </div>
    </div>

    <div class="col-md-10">

        <div id="addtabs">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active" id="adminHomeTab">
                    <a href="#adminHome" aria-controls="home" role="tab" data-toggle="tab">
                        <i class="icon-home"></i>
                        管理首页
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="adminHome">
                    <p></p>

                    <div class="col-md-7">

                        <div class="panel">
                            <div class="panel-body">

                                <strong>硬盘使用情况：</strong><br/>

                                <canvas id="HD_chart" width="550" height="140">
                                    总共：<?= $dirSize['total'] ?>GB <br/>
                                    已用：<?= $dirSize['data'][1]['data'] ?>GB <br/>
                                    空闲：<?= $dirSize['data'][0]['data'] ?>GB <br/>
                                    DMNovel项目占用：<?= $dirSize['data'][2]['data'] ?>GB<br/>

                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="<?= $dirSize['PCT'] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $dirSize['PCT'] ?>%;">
                                            <?= $dirSize['PCT'] ?>%
                                        </div>
                                    </div>
                                </canvas>
                                数据库占用：<?= $sqlSize ?>


                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>待审批小说</h4>
                            </div>

                            <table class="table table-striped table-hover" width="100%" id="storyApproveTable">
                                <thead>
                                <tr>
                                    <th width="30%">书名</th>
                                    <th>作者</th>
                                    <th>创建时间</th>
                                    <th>上传用户</th>
                                    <th width="40px">操作</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="panel">
                            <div class="panel-body">
                                <strong>日志：</strong><br/>

                                <div class="input-group">
                                    <select class="form-control" id="logs">
                                        <?php foreach ($logs as $log) { ?>
                                            <option value="<?= $log ?>"><?= $log ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="input-group-btn ">
                                        <button class="btn btn-warning" type="button" id="deleteLog">
                                            <i class="icon-trash"></i></button>
                                    </span>
                                </div>
                                <!-- /input-group -->
                                <div>
                                    <pre class="log-content">
                                    <?= $log_content ?>
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/bootstrap.addtabs.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/dataTables.bootstrap.min.css"/>

<script src="<?= THEMEPATH ?>/js/jquery.dataTables.min.js"></script>
<script src="<?= THEMEPATH ?>/js/dataTables.bootstrap.min.js"></script>
<script src="<?= THEMEPATH ?>/js/bootstrap.addtabs.min.js"></script>
<script src="<?= THEMEPATH ?>/js/chart.js"></script>

<script type="text/javascript">
    $(function () {

        $('#HD_chart').chart({
            data:<?=json_encode($dirSize['data']);?>,
            total:<?=$dirSize['total']?>,
            unitText: 'GB'
        });

        $('#logs').change(function () {
            var file = $(this).val();
            $('.log-content').load("<?=site_url('admin/main/logs/')?>/" + file);
        });

        $('#menu a').click(function () {
            $('#menu').find('a.active').removeClass('active');
            $(this).addClass('active');
        });

        $('#addtabs').addtabs({monitor: '#menu','iframeUse':false});

        $('#deleteLog').click(function () {
            if (!confirm("确认删除此日志？")) return;
            var file = $('#logs').val();
            $.get("<?=site_url('admin/main/deletelog')?>/" + file, function (data) {
                var e = $.parseJSON(data);
                if (e.error) {
                    show_error({'message': e.error});
                } else {
                    show_error({'message': e.success});
                    $('#logs').children('option[value=' + file + ']').remove();
                    $('.log-content').html('');
                }
            });
        });

        //点击首页显示
        $('#menu a:first').click(function () {
            $('.nav-tabs').find('li.active').removeClass('active');
            $('.tab-content').find('div.active').removeClass('active');
            $('#adminHome').addClass('active');
            $('#adminHomeTab').addClass('active');
        });

        var table = $('#storyApproveTable').DataTable({
            language: {
                'url': '<?=THEMEPATH?>/js/dataTables.zh-CN.json'
            },
            "processing": true,
            "serverSide": true,
            "order": [[3, "desc"]],
            'searching': false,
            'lengthChange': false,
            "ajax": "<?= site_url('/admin/main/story_approve/') ?>",
            "columns": [
                {"data": "title"},
                {"data": "author"},
                {"data": "time"},
                {"data": "user_name"},
                {"data": "action"}
            ],
            "createdRow": function (row, data, index) {
                var url = $('<a>', {
                    'href': '<?= SITEPATH ?>/story/' + data.id,
                    'target': '_blank',
                    'text': $('td', row).eq(0).text(),
                    'title': $('td', row).eq(0).text()
                });
                $('td', row).eq(0).html(url);
            }
        });

        $('#storyApproveTable').on('click','.approveStory,.unApproveStory',function () {
            var approve=$(this).data('approve')
            var id=$(this).parents('tr').attr('id');
            var tr=$(this).parents('tr');
            $.get('<?=site_url('admin/story/approve')?>/'+id+'/'+approve,function(data) {
                if (data) {
                    show_error(data);
                }
                tr.remove();
                table.dataTable().deleteRow()
            })
        })
    });

</script>

<?php include VIEWPATH . "footer.php" ?>
