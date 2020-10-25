<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                   aria-expanded="false" aria-controls="collapseOne">
                    查看规则
                    <i class="icon-hand-left"></i>
                </a>
            </h4>
            填写规则时，尽量保证唯一性、简短。每个规则不能超出２５５个字符。
        </div>
    </div>
</div>

<div class="panel panel-default" id="accordion" role="tablist" aria-multiselectable="true">

    <div class="panel-body">
        <form action="<?= site_url('/admin/collect_setting/add') ?>" method="post">

            <div class="btn-group pull-right" role="group">
                <button type="submit" class="btn btn-success">提交</button>
                <button type="reset" class="btn btn-info" onclick="BootstrapDialog.closeAll();">取消</button>
            </div>

            <div>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab"
                                                              data-toggle="tab">站点设置</a></li>
                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab"
                                               data-toggle="tab">小说信息</a></li>
                    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">章节内容</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <?php if (isset($collect)): ?>
                        <input type="hidden" name="id" value="<?= $collect['id'] ?>"/>
                    <?php endif; ?>
                    <div role="tabpanel" class="tab-pane active" id="home">
                        <div class="form-group">
                            <label for="titleLabel">站点标题</label>
                            <input type="text" class="form-control" id="site_title" name="site_title"
                                   placeholder="Title" value="<?= isset($collect) ? $collect['site_title'] : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">站点地址</label>
                            <input type="text" class="form-control" id="site_url" name="site_url" placeholder="Site URL"
                                   value="<?= isset($collect) ? $collect['site_url'] : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">小说地址</label>
                            <input type="text" class="form-control" id="book_url" name="book_url" placeholder="Book URL"
                                   value="<?= isset($collect) ? $collect['book_url'] : '' ?>">
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="profile">
                        <div class="form-group">
                            <label for="titleLabel">小说标题</label>
                            <input type="text" class="form-control" name="book_title"
                                   value="<?= isset($collect) ? htmlspecialchars($collect['book_title']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">小说作者</label>
                            <input type="text" class="form-control" name="book_author"
                                   value="<?= isset($collect) ? htmlspecialchars($collect['book_author']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">小说描述</label>
                            <input type="text" class="form-control" name="book_desc"
                                   value="<?= isset($collect) ? htmlspecialchars($collect['book_desc']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">小说图片</label>
                            <input type="text" class="form-control" name="book_img"
                                   value="<?= isset($collect) ? htmlspecialchars($collect['book_img']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">章节列表地址</label>
                            <input type="text" class="form-control" name="book_list"
                                   value="<?= isset($collect) ? htmlspecialchars($collect['book_list']) : '' ?>">
                        </div>

                    </div>
                    <div role="tabpanel" class="tab-pane" id="messages">

                        <div class="form-group">
                            <label for="titleLabel">章节地址及标题</label>
                            <input type="text" class="form-control" name="chapter_list"
                                   value="<?= isset($collect) ? htmlspecialchars($collect['chapter_list']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">章节内容地址前缀</label>
                            <input type="text" class="form-control" name="chapter_url"
                                   value="<?= isset($collect) ? htmlspecialchars($collect['chapter_url']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">章节内容</label>
                            <input type="text" class="form-control" name="chapter_content"
                                   value="<?= isset($collect) ? htmlspecialchars($collect['chapter_content']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="titleLabel">测试书号ID</label>
                            <input type="text" class="form-control" id="test_id" name="test_id" placeholder="Book URL"
                                   value="<?= isset($collect) ? $collect['test_id'] : '' ?>">
                        </div>
                    </div>

                </div>

            </div>


        </form>
    </div>
</div>