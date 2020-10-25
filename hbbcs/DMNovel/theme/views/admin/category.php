
<div class="panel panel-default" id="accordion" role="tablist" aria-multiselectable="true">

    <div class="panel-heading">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#createCategory" aria-expanded="false" aria-controls="createCategory">
            <i class="icon-plus-sign-alt"></i>
            建立新分类
        </a>
    </div>

    <div id="createCategory" class="panel-collapse collapse" role="tabpanel" aria-labelledby="createCategory">
        <div class="panel-body">
            <form class="form-horizontal" action="<?= site_url('/admin/category/add') ?>" method="post">

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">标题</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="title" id="title" placeholder="Title">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success">增加</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<table class="table table-hover">
    <thead>
    <th>ID</th>
    <th width="80%">分类标题</th>
    <th width="10%">操作</th>
    </thead>
    <tbody>
    <?php foreach ($categorys as $c): ?>
        <tr id="<?= $c['id'] ?>">
            <td><?= $c['id'] ?></td>
            <td><?= $c['title'] ?></td>
            <td>
                <div class="btn-group btn-group-sm" role="group" aria-label="...">
                    <button class="btn btn-primary editCategory" title="编辑"><i class="icon-edit"></i></button>
                    <button class="btn btn-success deleteCategory" title="删除"><i class="icon-trash"></i></button>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript">
    $(function () {

        $('.editCategory').click(function () {
            var $title_td = $(this).parents('td').prev(); //需要编辑的标题所在td
            var $id = $(this).parents('tr').attr('id'); //需要编辑的分类ID
            var $title = $title_td.text(); //需要编辑的分类title


            var $form = '<form class="form-inline" action="<?= site_url('/admin/category/add') ?>" method="post">' +
                '<div class="form-group">' +
                '<div class="input-group">' +
                '<input type = "text" class = "form-control" name="title" value="' + $title + '" />' +

                '<span class="input-group-btn">' +
                '<button class="btn btn-success" type="submit"><i class="icon-ok"></i></button>' +
                '</span></div>' +
                '<input type="hidden" name="id" value="' + $id + '" />' +
                '</div></form>';

            $title_td.html($form);
        });

        $('.deleteCategory').click(function() {
            if (!confirm('分类删除后，可能导致一些小说无法正常浏览。\n是否确定删除此分类！！！')) return;
            var id = $(this).parents('tr').attr('id'); //需要编辑的分类ID
            $.get('<?= site_url('/admin/category/delete/') ?>'+id,function(data){
                if(data) {
                    alert(data);
                } else {
                    $('#'+id).fadeOut('3000').remove();
                }
            })
        })
    });
</script>

