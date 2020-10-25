
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="icon-plus"></i>
                <?= isset($chapter['id']) ? '编辑' : '增加新' ?>章节<?= isset($story['title']) ? ' - 《' . $story['title'] . '》' : '' ?>
            </h3>
        </div>
        <div class="panel-body">
            <form id="chapterForm" class="form-horizontal" action="<?= site_url('/admin/chapter/add') ?>" method="post">
                <?php if (!isset($story)) { ?>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">小说</label>

                        <div class="col-sm-10">
                            <select class="form-control" name="story_id">
                                <?php foreach ($storys as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['title'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php } else { ?>
                    <input type="hidden" name="story_id" value="<?= isset($story['id']) ? $story['id'] : 0 ?>"/>
                <?php } ?>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">章节标题</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="title" data-required="true" name="title" placeholder="章节标题" value="<?= isset($chapter['id']) ? $chapter['title'] : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">章节排序</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="order" name="order" placeholder="章节排序" value="<?= isset($chapter['order']) ? $chapter['order'] : $order ?>">
                    </div>
                </div>

                <div class="">
                    <textarea class="form-control" data-required="true" placeholder="章节内容" id="chapterContent" name="content"><?= isset($chapter['id']) ? $chapter['content'] : '' ?></textarea>
                </div>
                <?php if (isset($chapter['id'])): ?>
                    <input type="hidden" name="id" value="<?= $chapter['id'] ?>"/>
                    <input type="hidden" name="type" value="list"/>
                <?php endif; ?>
                <div class="form-group">
                    <div class="col-sm-12 text-center btn-group">
                        <button type="submit" class="btn btn-primary"><?= isset($chapter) ? '编辑' : '增加' ?></button>
                        <button type="reset" class="btn btn-success" onclick="BootstrapDialog.closeAll();">取消</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/summernote.css"/>

    <script src="<?= THEMEPATH ?>/js/summernote.min.js"></script>
    <script src="<?= THEMEPATH ?>/js/validator.js"></script>
    <script src="<?= THEMEPATH ?>/js/summernote-zh-CN.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $('#chapterContent').summernote({
                'lang': 'zh-CN',
                'height': 300,
                'toolbar': [
                    ['misc', ['fullscreen', 'codeview']],
                    ['imagesize', ['picture', 'imageSize100', 'imageSize50', 'imageSize25']],
                ],
                callbacks: {
                    'onInit': function () {
                        $('<label>', {'class': 'pull-right', 'id': 'word'}).append('0 字 ').appendTo('.note-toolbar');
                    },
                    'onChange': function (contents) {
                        var word = $(contents.replace(/(\s+)|(&nbsp;)+/g, "")).text().length;
                        $('#word').text(word + ' 字 ');
                    }
                },
                'onImageUpload': function (files, editor, welEditable) {
                    sendFile(files[0], editor, welEditable);
                }
            });

            function sendFile(file, editor, welEditable) {
                data = new FormData();
                data.append("file", file);
                $.ajax({
                    data: data,
                    type: "POST",
                    url: "<?= site_url('/admin/upload') ?>",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (url) {
                        editor.insertImage(welEditable, url);
                    }
                });
            }

            $('#chapterForm').formValidator({
                sending: {
                    type: 'ajax',
                    success: function (data) {
                        var e = $.parseJSON(data);
                        if (e.error) {
                            show_error({'message': e.error, 'color': 'danger'});
                        } else {
                            show_error('增加成功。');
                            $('#title').val('');
                            $('#order').val(parseInt($('#order').val())+1);
                            $('#chapterContent').summernote('reset');
                        }
                    },
                    error: function () {
                        show_error("提交失败！");
                    }
                }
            })
        });
    </script>

