<div class="panel panel-default">
    <div class="panel-heading">
        <b>我的书架</b>
    </div>


    <ul class="list-group bookmark">
        <?php foreach ($bookmarks as $bookmark) {
            $last = $this->input->cookie($bookmark['story_id']) ? json_decode($this->input->cookie($bookmark['story_id']), true) : '';


            $last_update = $this->db->where('story_id', $bookmark['story_id'])->select('id,title')->limit(1, 0)->order_by('order', 'desc')->get('chapter')->row_array();
            ?>

            <li class="list-group-item" data-bookmark-id="<?= $bookmark['id'] ?>">
                <a href="<?= site_url('story/' . $bookmark['story_id']) ?>">
                    <img src="<?= site_url('/' . ($bookmark['story_image'] ? $bookmark['story_image'] : 'books/default.jpg')) ?>" width="85" align="left" class="img-thumbnail"/>
                </a>
                <h4 class="list-group-item-heading">
                    <a href="<?= site_url('story/' . $bookmark['story_id']) ?>">
                        <?= $bookmark['story_title'] ?>
                    </a>

                    <div class="btn-group btn-group-sm pull-right">
                        <button class="btn btn-default deleteBookmark" title="删除书签">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                </h4>


                <p class="list-group-item-text"><?= $bookmark['story_desc'] ?></p>
                最后更新：
                <a href="<?= site_url('/chapter/' . $last_update['id']) ?>"><?= $last_update['title'] ?></a>
                <?php if ($last): ?>
                    <div class="pull-right">您最后阅读章节：
                        <span id="last_read"><a href="<?= site_url('/chapter/' . $last['id']) ?>"><?= $last['title'] ?></a> </span>
                    </div>
                <?php endif; ?>
            </li>
        <?php } ?>
    </ul>

</div>

<script type="text/javascript">
    $(function () {
        $('.deleteBookmark').click(function () {
            var list = $(this).parents('.list-group-item');
            var id = list.data('bookmark-id');

            $.get('<?=site_url('user/bookmark_delete/')?>/' + id, function (data) {
                if (data) {
                    show_error(data);
                } else {
                    show_error('删除成功。');
                    list.remove();
                }
            })

        });
    });
</script>