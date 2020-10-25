<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?= $book['title'] ?>
        </h3>

        <div class="btn-group-xs btn-group">
            <a class="btn btn-primary" href="javascript:void(0);" id="back">
                <i class="icon-cloud-download"></i>
                继续采集
            </a>
            <a class="btn btn-success" href="#" id="refresh">
                <i class="icon-refresh"></i>
                刷新失败
            </a>
        </div>
    </div>
    <div class="panel-body">
        <div class="collect" id="collectChapter">

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var data = <?=$chapter_list?>;
        var i = 0;
        $.each(data, function (key, ch) {
            html = $.ajax({
                url: '<?= site_url('/admin/collect/get_chapter') ?>',
                async: false,
                dataType: 'text',
                type: 'POST',
                data: {
                    url: '<?=$book['chapter_url']?>/' + ch.url,
                    title: ch.title,
                    collect_id: '<?=$book['collect_id']?>',
                    story_id: '<?=$book['story_id']?>',
                    order: ch.order ? ch.order : parseInt(<?=$order?>) + i
                }
            }).responseText;
            if (html == '失败') {
                $('#collectChapter').append($('<s>', {style: 'color:red;'}).append(ch.title + ' ====> ' + html + '&nbsp;&nbsp;'));
            } else {
                $('#collectChapter').append(ch.title + ' ====> ' + html + '&nbsp;&nbsp;');
            }
            $('#collectChapter').scrollTop($('#collectChapter')[0].scrollHeight);
            i++;
        });
        $('#collectChapter').append('采集完成.');

        $('#refresh').click(function() {
            var loader=$('<div>',{'class':'text-center'}).append($('<img>',{src:'<?=THEMEPATH?>/images/loading.gif'})).append('<br/>正在加载...请稍候...');
            $('#collect').html(loader);
            $.post('<?= site_url('/admin/collect/get') ?>',{
                collect_id:<?= $collect_id ?>,
                book_id:<?= $book_id ?>,
                category_id:<?= $category_id ?>
            },function(data) {
                $('#collect').html(data);
            });
        });

        $('#back').click(function () {
            $('#collect').load('<?= site_url('/admin/collect') ?>');
        })
    });
</script>

