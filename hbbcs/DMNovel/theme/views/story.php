<?php include VIEWPATH . "header.php" ?>

<div class="stories">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="text-center"><?= $story['title'] ?>
                <span class="pull-right">
                    <div class="btn-group btn-group-sm" id="<?= $story['id'] ?>">

                        <a data-type="epub" class="btn btn-default download" title="EPUB下载">
                            <i class="icon-download"></i>
                            EPUB下载
                        </a>
                        <button type="button" class="btn btn-default" id="bookmark" title="收藏">
                            <i class="icon-bookmark"></i>
                            <span id="markUser"><?= $story['mark'] ?></span> 人收藏
                        </button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="<?= $story['vote'] ?> 人投票">
                            <i class="icon-star"></i>
                            平均分 <?= $story['average'] ?>
                            <span id="voteNum"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <input id="vote" value="<?= $story['average'] ?>">
                        </ul>
                    </div>
                </span>
            </h2>
        </div>
        <div class="panel-body">
            <img src="<?= site_url('/' . ($story['image'] ? $story['image'] : 'books/default.jpg')) ?>" width="160px" align="left" class="img-thumbnail"/>
            <h4 class="text-right">
                作者：<?= $story['author'] ?>
            </h4>

            <p style="margin-left: 50px;">
                <?= $story['desc'] ?>
            </p>

            <?php if (isset($last_read['id'])): ?>
                <div class="pull-right">您最后阅读章节：
                    <span id="last_read"><a href="<?= site_url('/chapter/' . $last_read['id']) ?>"><?= $last_read['title'] ?></a> </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Table -->
        <ul class="list-inline chapter-list">
            <?php foreach ($chapters as $c): ?>
                <li>
                    <a href="<?= site_url('/chapter/' . $c['id']) ?>"><?= $c['title'] ?></a>
                </li>
            <?php endforeach; ?>

        </ul>
    </div>
</div>

<link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/star-rating.min.css"/>

<script src="<?= THEMEPATH ?>js/star-rating.min.js"></script>
<script src="<?= THEMEPATH ?>js/star-rating-zh.js"></script>

<script type="text/javascript">
    $(function () {
        $('#bookmark').click(function () {
            $.get('<?=site_url('story/mark/'.$story['id'])?>', function (data) {
                if (data) {
                    show_error(data);
                } else {
                    show_error('已加入到您的书架中。');
                    var users = parseInt($('#markUser').text()) + 1;
                    $('#markUser').text(users);
                }
            });
        });

        $('.download').click(function () {
            var id = $(this).parent('.btn-group').attr('id');
            var type = $(this).data('type');

            $.post('<?=site_url('download')?>', {'id': id, 'type': type}, function (data) {
                var e = $.parseJSON(data);
                if (e.error) {
                    show_error(e.error);
                } else {
                    window.location.href = e.url;
                }
            });
        });

        $("#vote").rating({
            'showCaption': false,
            'showClear': false,
            'min': '0',
            'max': '5',
            'step': '1',
            'size': 'xs',
            filledStar: '<i class="icon-star"></i>',
            emptyStar: '<i class="icon-star-empty"></i>'
        });

        $('#vote').on('rating.change', function (event, value, caption) {
            var vote = $(this).val();
            console.log(vote);
            $.get('<?=site_url('story/vote/'.$story['id'])?>/' + vote, function (data) {
                if (!isNaN(data)) {
                    show_error('投票成功。');
                    $('#voteNum').text(data);
                } else {
                    show_error(data);
                }
            });
        });
    });
</script>

<?php include VIEWPATH . "footer.php" ?>
