
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
            <?php foreach ($captures as $c): ?>
                <tr id="<?= $c['id'] ?>">
                    <td><?= $c['id'] ?></td>
                    <td><?= $c['site_title'] ?></td>
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
                message: $('<div></div>').load('<?= site_url('/admin/capture/test/') ?>' + id)
            });
        });
        
         $('#addCapture').click(function () {           
            BootstrapDialog.show({
                title: '测试采集配置',
                message: $('<div></div>').load('<?= site_url('/admin/capture/edit/') ?>')
            });
        });
        
        $('.editCapture').click(function () {
            var id = $(this).parents('tr').attr('id');
            var title = $(this).parents('td').prev('td').text();
            BootstrapDialog.show({
                title: '编辑采集配置 - ' + title,
                message: $('<div></div>').load('<?= site_url('/admin/capture/edit/') ?>' + id)
            });
        });
    });
</script>

