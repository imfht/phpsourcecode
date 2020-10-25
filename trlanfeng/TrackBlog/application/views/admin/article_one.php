<!-- content start -->
<div class="admin-content">

    <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg">表单</strong> /
            <small>form</small>
        </div>
    </div>
    <?php if ($controlType == "add") { ?>
    <form class="am-form" action="/index.php/admin/article/add" method="post">
    <?php } else if ($controlType == "edit") { ?>
    <form class="am-form" action="/index.php/admin/article/edit/<?php echo $id; ?>" method="post">
    <?php } ?>
        <div class="am-tabs am-margin" data-am-tabs>

            <ul class="am-tabs-nav am-nav am-nav-tabs">
                <li class="am-active"><a href="#tab1">文章信息</a></li>
                <li><a href="#tab2">其他选项</a></li>
            </ul>
            <div class="am-tabs-bd">

                <div class="am-tab-panel am-fade am-in am-active" id="tab1">

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right  admin-form-text">所属类别</div>
                        <div class="am-u-sm-12 am-u-md-10">
                            <select data-am-selected="{btnSize: 'sm'}" name="cat">
                                <?php foreach ($catlist as $row): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php if($cat == $row['id']) echo 'selected'; ?>><?php echo $row['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right admin-form-text">
                            文章标题
                        </div>
                        <div class="am-u-sm-12 am-u-md-6">
                            <input type="text" name="name" class="am-input-sm" value="<?php echo $name; ?>">
                        </div>
                        <div class="am-u-md-4"></div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right admin-form-text">
                            关键字
                        </div>
                        <div class="am-u-sm-12 am-u-md-6 am-u-end col-end">
                            <input type="text" name="keywords" class="am-input-sm" value="<?php echo $keywords; ?>">
                        </div>
                        <div class="am-u-md-4"></div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right admin-form-text">
                            描述
                        </div>
                        <div class="am-u-sm-12 am-u-md-6">
                            <textarea rows="3" name="description"><?php echo $description; ?></textarea>
                        </div>
                        <div class="am-u-md-4">不填写则自动截取内容前255字符</div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right admin-form-text">
                            缩略图
                        </div>
                        <div class="am-u-sm-12 am-u-md-6 am-u-end col-end">
                            <input type="text" name="thumbpic" class="am-input-sm">
                        </div>
                        <div class="am-u-md-4"></div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right admin-form-text">
                            内容
                        </div>
                        <div class="am-u-sm-12 am-u-md-8">
                            <script id="container" name="content" type="text/plain"><?php echo $content; ?></script>
                            <script type="text/javascript" src="/public/UEditor/ueditor.config.js"></script>
                            <script type="text/javascript" src="/public/UEditor/ueditor.all.js"></script>
                            <script type="text/javascript">
                                var editor = UE.getEditor('container');
                            </script>
                        </div>
                        <div class="am-u-md-2"></div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right admin-form-text">
                            标签
                        </div>
                        <div class="am-u-sm-12 am-u-md-6 am-u-end col-end">
                            <input type="text" class="am-input-sm" name="tags" value="<?php echo $tags; ?>">
                        </div>
                        <div class="am-u-md-4"></div>
                    </div>

                </div>

                <div class="am-tab-panel am-fade" id="tab2">
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-3 am-u-md-2 am-text-right">显示状态</div>
                        <div class="am-u-sm-9 am-u-md-10">
                            <div class="am-btn-group" data-am-button>
                                <label class="am-btn am-btn-default am-btn-xs <?php if($status == 1) echo 'am-active'; ?>">
                                    <input name="status" type="radio" value="1" <?php if($status == 1) echo 'checked="checked"'; ?>> 正常
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs <?php if($status == 9) echo 'am-active'; ?>">
                                    <input name="status" type="radio" value="9" <?php if($status == 9) echo 'checked="checked"'; ?>> 待审核
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs <?php if($status == 0) echo 'am-active'; ?>">
                                    <input name="status" type="radio" value="0" <?php if($status == 0) echo 'checked="checked"'; ?>> 不显示
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-3 am-u-md-2 am-text-right">评论开关</div>
                        <div class="am-u-sm-9 am-u-md-10">
                            <div class="am-btn-group" data-am-button>
                                <label class="am-btn am-btn-default am-btn-xs <?php if($allowcmt == 1) echo 'am-active'; ?>">
                                    <input name="allowcmt" type="radio" value="1" <?php if($status == 1) echo 'checked="checked"'; ?>> 开启
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs <?php if($allowcmt == 0) echo 'am-active'; ?>">
                                    <input name="allowcmt" type="radio" value="0" <?php if($status == 0) echo 'checked="checked"'; ?>> 关闭
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-3 am-u-md-2 am-text-right">推荐类型</div>
                        <div class="am-u-sm-9 am-u-md-10">
                            <div class="am-btn-group" data-am-button>
                                <label class="am-btn am-btn-default am-btn-xs">
                                    <input name="slug[]" value="t" type="checkbox" <?php if (in_array('t',$slug)) { echo 'checked="checked"'; } ?>> 置顶
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs">
                                    <input name="slug[]" value="r" type="checkbox" <?php if (in_array('r',$slug)) { echo 'checked="checked"'; } ?>> 推荐
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs">
                                    <input name="slug[]" value="h" type="checkbox" <?php if (in_array('h',$slug)) { echo 'checked="checked"'; } ?>> 热门
                                </label>
                                <label class="am-btn am-btn-default am-btn-xs">
                                    <input name="slug[]" value="s" type="checkbox" <?php if (in_array('s',$slug)) { echo 'checked="checked"'; } ?>> 轮播图
                                </label>

                            </div>
                        </div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right admin-form-text">
                            跳转地址
                        </div>
                        <div class="am-u-sm-12 am-u-md-6 am-u-end col-end">
                            <input type="text" name="link" class="am-input-sm" value="<?php echo $link; ?>">
                        </div>
                        <div class="am-u-md-4"></div>
                    </div>
                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-12 am-u-md-2 am-text-right admin-form-text">
                            排序
                        </div>
                        <div class="am-u-sm-12 am-u-md-6 am-u-end col-end">
                            <input type="text" name="orders" class="am-input-sm" value="<?php echo $orders; ?>">
                        </div>
                        <div class="am-u-md-4"></div>
                    </div>
                    <!--                    <div class="am-g am-margin-top">-->
                    <!--                        <div class="am-u-sm-3 am-u-md-2 am-text-right">-->
                    <!--                            发布时间-->
                    <!--                        </div>-->
                    <!--                        <div class="am-u-sm-9 am-u-md-6">-->
                    <!--                            <div class="am-input-group am-datepicker-date" data-am-datepicker="{format: 'yyyy-mm-dd'}">-->
                    <!--                                <input type="text" name="times" class="am-form-field am-input-sm" placeholder="日历组件" readonly>-->
                    <!--                                <span class="am-input-group-btn am-datepicker-add-on">-->
                    <!--                                    <button class="am-btn am-btn-default am-btn-sm" type="button">-->
                    <!--                                        <span class="am-icon-calendar"></span>-->
                    <!--                                    </button>-->
                    <!--                                </span>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                        <div class="am-u-md-4"></div>-->
                    <!--                    </div>-->

                </div>

            </div>
        </div>
        <div class="am-margin">
            <input type="submit" name="submit" value="提交保存" class="am-btn am-btn-primary am-btn-xs">
            <input type="reset" name="reset" value="放弃保存" class="am-btn am-btn-danger am-btn-xs">
        </div>
    </form>
</div>
<!-- content end -->
