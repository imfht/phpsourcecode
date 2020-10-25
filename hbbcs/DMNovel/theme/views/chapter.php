<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">


    <title><?= $title ?></title>

    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>css/<?= $style ?>.css" id="bootstrapStyle"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/MyPagination.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/front.css"/>

    <script src="<?= THEMEPATH ?>/js/jquery.min.js"></script>
    <script src="<?= THEMEPATH ?>/js/jquery.cookie.js"></script>
    <script src="<?= THEMEPATH ?>/js/bootstrap.min.js"></script>
    <script src="<?= THEMEPATH ?>/js/custom.js"></script>

    <!--[if lt IE 9]>
    <script src="<?=THEMEPATH?>/js/html5shiv.min.js"></script>
    <script src="<?=THEMEPATH?>/js/respond.min.js"></script>
    <script src="<?=THEMEPATH?>/css/font-awesome-ie7.min.js"></script>
    <![endif]-->

</head>
<body>

<div class="maskLayer">
    <img src="<?= THEMEPATH ?>images/loading.gif">
</div>

<ol class="breadcrumb">
    <li><a href="<?= SITEPATH ?>">首页</a></li>
    <li><a href="<?= site_url('/category/' . $category['id']) ?>"><?= $category['title'] ?></a></li>
    <li><a href=" <?= site_url('/story/' . $prev_next['story_id']) ?>"><?= $story['title'] ?></a></li>
    <li class="active"><b><?= $chapter['title'] ?></b></li>

    <div class="btn-group btn-group-sm pull-right" role="group" aria-label="...">
        <a class="btn btn-default"
            href="<?= $prev_next['prev'] ? site_url('/chapter/' . $prev_next['prev']) : site_url('/story/' . $prev_next['story_id']) ?>"
            id="prev_url" title="上一章">
            <i class="icon-hand-left"></i>
        </a>
        <button type="button" class="btn btn-default" id="chapter_list" title="目录">
            <i class="icon-list-ul"></i>
        </button>
        <button class="btn btn-default dropdown-toggle" type="button"
        " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="icon-desktop"></i>
        </button>
        <ul class="dropdown-menu" id="changeStyle">
            <li>
                <a href="#" id="">
                    <img src="<?= THEMEPATH ?>images/style/default_thumb.png" width="60" title="Default"/>
                </a>
            </li>
            <li>
                <a href="#" id="Cosmo">
                    <img src="<?= THEMEPATH ?>images/style/cosmor_thumb.png" width="60" title="Cosmo"/>
                </a>
            </li>
            <li>
                <a href="#" id="Cyborg">
                    <img src="<?= THEMEPATH ?>images/style/cyborg_thumb.png" width="60" title="Cyborg"/>
                </a>
            </li>
            <li>
                <a href="#" id="Darkly">
                    <img src="<?= THEMEPATH ?>images/style/darkly_thumb.png" width="60" title="Darkly"/>
                </a>
            </li>
            <li>
                <a href="#" id="Slate">
                    <img src="<?= THEMEPATH ?>images/style/slate_thumb.png" width="60" title="Slate"/>
                </a>
            </li>
            <li>
                <a href="#" id="Superhero">
                    <img src="<?= THEMEPATH ?>images/style/superhero_thumb.png" width="60" title="Superhero"/>
                </a>
            </li>
            <li>
                <a href="#" id="Yeti">
                    <img src="<?= THEMEPATH ?>images/style/yeti_thumb.png" width="60" title="Yeti"/>
                </a>
            </li>

        </ul>
        <a class="btn btn-default"
            href="<?= $prev_next['next'] ? site_url('/chapter/' . $prev_next['next']) : site_url('/story/' . $prev_next['story_id']) ?>"
            id="next_url" title="下一章">
            <i class="icon-hand-right"></i>
        </a>
    </div>
</ol>

<div class="chapter-list">
    <div class="panel panel-default">
        <div class="list-group">
            <?php foreach ($chapters as $c): ?>
                <a href="<?= site_url('/chapter/' . $c['id']) ?>" class="list-group-item <?= $c['id'] == $chapter['id'] ? 'active' : '' ?>">
                  <?= $c['title'] ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="chapter">
    <div class="panel panel-default">
        <div class="panel-body">
            <div id="content">
                <?= $chapter['content'] ?>
            </div>
            <div class="pagination">
                <dd id="prev"></dd>
                <dd id="cent"></dd>
                <dd id="next"></dd>
            </div>
        </div>
        <div id="cPage" class="text-right"></div>

    </div>
</div>

<link rel="stylesheet" type="text/css" media="screen" href="<?= THEMEPATH ?>/css/MyPagination.css"/>
<script src="<?= THEMEPATH ?>/js/MyPagination.js"></script>

<script type="text/javascript">
    $(function () {
        $('.maskLayer').height($(window).height());

        $('#changeStyle a').click(function () {
            var style = $(this).attr('id');
            var styleUrl = (style == '') ? "<?= THEMEPATH ?>css/bootstrap.min.css" : "<?= THEMEPATH ?>css/bootstrap/" + style + ".css"

            $('#bootstrapStyle').attr('href', styleUrl);
            $.cookie('style', style,{ expires: 365, path: '<?=site_url('/')?>' });
        });

        var height = parseInt($(window).height()) - 120;
        var width = parseInt($(window).width()) > 980 ? 980 : parseInt($(window).width());
        $('.chapter-list').height(height - 52);
        $('.chapter').width(width);
        $('.pagination').width(width);
        $('#content').MyPagination({
            height: height,
            width: width,
            cookieid:'chapter_<?=$chapter['id']?>',
            'cookieurl': '<?=site_url('/')?>'
        });

        var container = $('.chapter-list'),
            scrollTo = $('.chapter-list .active');

        container.css('height', height);
        container.scrollTop(
            scrollTo.offset().top
        );

        $('#chapter_list,#cent').click(function () {
            var chapter_list = $('.chapter-list');
            if (chapter_list.offset().left < 0) {
                chapter_list.animate({left: '10px'});
            } else {
                chapter_list.animate({left: '-250px'});
            }
        });



        $('.maskLayer').remove();

    });
</script>
<?php include VIEWPATH . "footer.php" ?>
