
<div class="panel panel-default">
    <div class="panel-heading">
        <h4><?= $item['title'] ?> </h4>
    </div>
    <div class="panel-body">
        <img src="<?= $item['img'] ?>" align="left" height="200px"/>

        <p class="">
            <?= $item['author'] ?><br/><br/>
            <?= $item['desc'] ?>
        </p>
    </div>
    <div class="panel-footer">
        <p>
        <span class="pull-right">
            <a href="history.go(-1);">返回</a>
            <a href="<?= site_url('/admin/capture/get_chapter/') ?><?= $id ?>">开始采集</a>
        </span>
        </p>
    </div>
</div>

