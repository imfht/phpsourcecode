<div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#home" aria-controls="home" role="tab" data-toggle="tab">小说内容</a></li>
        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">章节列表</a></li>
        <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">章节内容</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="home">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1><?= $book['book_title'] ?></h1>
                    <img src="<?= $book['book_img'] ?>" alt="left" class="pull-left" width="120px"/>
                    <h4>作者：<?= $book['book_author'] ?></h4><br/>
                    <?= $book['book_desc'] ?>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="profile">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                    $num = count($chapter_list);
                    for ($i = $num - 1; $i > ($num - 5); $i--): ?>
                        <div class="col-md-3">
                            <a href="<?= $book['chapter_url'] . $chapter_list[$i]['url'] ?>"><?= $chapter_list[$i]['title'] ?></a>
                        </div>
                    <?php endfor; ?>
                    ...
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="messages">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h1><?= $chapter_list[0]['title'] ?></h1>
                    <?= $chapter ?>
                    <br/>
                    ...
                </div>
            </div>
        </div>
    </div>
    <?php if ($ajax == 0): ?>
        <div class="text-center">
            <div class="btn-group">
                <a class="btn btn-primary" href="javascript:void(0);" id="collectBook">
                    <i class="icon-cloud-download"></i>
                    开始采集
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    $(function () {
        $('#collectBook').click(function () {
            var loader=$('<div>',{'class':'text-center'}).append($('<img>',{src:'<?=THEMEPATH?>/images/loading.gif'})).append('<br/>正在加载...请稍候...');
            $('#collect').html(loader);
            $.post('<?= site_url('/admin/collect/get') ?>',{
                collect_id:<?= $collect_id ?>,
                book_id:<?= $book_id ?>,
                category_id:<?= $category_id ?>
            },function(data) {
                $('#collect').html(data);
            });
        })
    })
</script>