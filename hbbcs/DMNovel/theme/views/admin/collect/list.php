<?php include VIEWPATH . "admin/iframe_header.php" ?>

<div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6">

                <i class="icon-cog"></i>
                采集设置

            </div>
            <div class="col-md-6 text-right">
                <button class="btn btn-xs btn-info" id="addCapture">
                    <i class="icon-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th width="70%">采集站点</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($collects as $c): ?>
            <tr id="<?= $c['id'] ?>">
                <td><?= $c['id'] ?></td>
                <td><a href="<?= $c['site_url'] ?>" target="_blank"><?= $c['site_title'] ?></a></td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="...">
                        <button type="button" class="btn btn-primary editCapture" title="编辑">
                            <i class="icon-edit"></i>
                        </button>
                        <button type="button" class="btn btn-success testCapture" title="测试">
                            <i class="icon-info-sign"></i>
                        </button>
                        <button type="button" class="btn btn-danger deleteCapture" title="删除">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(function () {
        $('.testCapture').click(function () {
            var id = $(this).parents('tr').attr('id');
            var title = $(this).parents('td').prev('td').text();
            BootstrapDialog.show({
                title: '测试采集 - ' + title,
                message: $('<div></div>').load('<?= site_url('/admin/collect_setting/test/') ?>/' + id)
            });
        });

        $('#addCapture').click(function () {
            BootstrapDialog.show({
                title: '测试采集配置',
                message: $('<div></div>').load('<?= site_url('/admin/collect_setting/edit/') ?>')
            });
        });

        $('.editCapture').click(function () {
            var id = $(this).parents('tr').attr('id');
            var title = $(this).parents('td').prev('td').text();
            BootstrapDialog.show({
                title: '编辑采集配置 - ' + title,
                message: $('<div></div>').load('<?= site_url('/admin/collect_setting/edit/') ?>/' + id)
            });
        });

        $('.deleteCapture').click(function () {
            if (!confirm('确认删除？？')) return;
            var tr = $(this).parents('tr');
            var id = $(this).parents('tr').attr('id');
            $.get('<?= site_url('/admin/collect_setting/delete/') ?>/' + id, function () {
                tr.remove();
            });
        });
    });
</script>


<?php include VIEWPATH . "admin/iframe_footer.php" ?>
