
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-5">
                    <h4>
                        <i class="icon-book"></i>
                        <?= $story['title'] ?>

                    </h4>
                </div>

                <div class="col-md-7 text-right">
                    <button type="submit" class="bg-primary btn" id="addChapter" title="增加新章节"
                        data-story-id="<?= $story['id'] ?>">
                        <i class="icon-plus-sign-alt"></i>
                        增加新章节
                    </button>
                    <!-- /input-group -->
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">

        <table class="table table-striped table-hover" width="100%" id="chapter_list_table">
            <thead>
            <tr>
                <th>排序</th>
                <th width="75%">标题</th>
                <th>操作</th>
            </tr>
            </thead>

        </table>

    </div>


    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/dataTables.bootstrap.min.css"/>

    <script src="<?= THEMEPATH ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?= THEMEPATH ?>/js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('#chapter_list_table').dataTable({
                language: {
                    'url': '<?=THEMEPATH?>/js/dataTables.zh-CN.json'
                },
                "stateSave": true,
                "processing": true,
                "serverSide": true,
                "order": [[0, "desc"]],
                "ajax": "<?= site_url('/admin/chapter/datatable/'.$story['id']) ?>",
                "columns": [
                    {"data": "order"},
                    {"data": "title"},
                    {"data": "action"}
                ]
            });
            //选择搜索类型
            $("#selectSearch a").click(function () {
                var id = $(this).parent('li').attr('id');
                var title = $(this).text();
                $('input[name=search]').attr('placeholder', '搜索章节 ' + title);
                $('#type').val(id);
                $('#selectSearchType').html($(this).text() + ' <span class="caret"></span>');
            })

            //增加章节
            $('#addChapter').click(function () {
                var id = $(this).attr('data-story-id');
                var chapter_btn = parent.$(window.parent.document).find("[data-addtab='chapter']");//触发父窗口按钮
                $(window.parent.document).find('#tab_tab_chapter').remove();
                $(window.parent.document).find('#tab_chapter').remove();
                chapter_btn.attr("url", '<?= site_url('/admin/chapter/') ?>/' + id);
                chapter_btn.trigger("click");
            });

            //编辑章节
            $('body').on('click', '.editChapter', function () {
                var chapter_id = $(this).parents('tr').attr('id');
                var chapter_title = $(this).parents('td').prev('td').text();
                var url = '<?= site_url('/admin/chapter/'.$story['id'])?>/' + chapter_id;
                ajax_dialog(chapter_title, url);
            });

            //删除章节
            $('body').on('click', '.deleteChapter', function () {
                if (!confirm('确认删除此章节？')) return false;
                var chapter = $(this).parents('tr');
                var chapter_id = chapter.attr('id');

                var url = '<?= site_url('/admin/chapter/delete/') ?>/' + chapter_id;
                $.get(url, function (data) {
                    if (!data) {
                        chapter.remove();
                    } else {
                        BootstrapDialog.show({
                            title: '发生错误',
                            message: data
                        });
                    }
                });
            });
            //双击打开章节
            $('body').on('dblclick', 'tr', function () {
                var id = $(this).attr('id');
                window.open('<?= site_url('/chapter/') ?>/' + id);
            });
        })
    </script>
