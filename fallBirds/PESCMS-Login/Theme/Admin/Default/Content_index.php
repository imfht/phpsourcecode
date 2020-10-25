<!-- content start -->
<div class="admin-content">

    <div class="am-cf am-padding">
        <div class="am-fl am-cf"><strong class="am-text-primary am-text-lg"><?= $title; ?></strong> / <small>列表</small></div>
    </div>

    <div class="am-g">
        <div class="am-u-sm-12 am-u-md-6">
            <div class="am-btn-toolbar">
                <div class="am-btn-group am-btn-group-xs">
                    <a href="<?= $label->url(GROUP . '-' . MODULE . '-action'); ?>" class="am-btn am-btn-default"><span class="am-icon-plus"></span> 新增</a>
                </div>
            </div>
        </div>


        <div class="am-u-sm-12 am-u-md-3">
            <form>
                <div class="am-input-group am-input-group-sm">
                    <input type="hidden" name="g" value="<?= GROUP; ?>" />
                    <input type="hidden" name="m" value="<?= MODULE ?>" />
                    <input type="hidden" name="a" value="<?= ACTION ?>" />
                    <input type="text" name="keyword" value="<?= $_GET['keyword'] ?>" class="am-form-field">
                    <span class="am-input-group-btn">
                        <input class="am-btn am-btn-default" type="submit" value="搜索"/>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <div class="am-g">
        <div class="am-u-sm-12">
            <?php if (empty($list)): ?>
                <div class="am-alert am-alert-secondary am-margin-top am-margin-bottom am-text-center" data-am-alert>
                    <p>本页面没有数据 :-(</p>
                </div>
            <?php else: ?>
                <form class="am-form" action="<?= $label->url(GROUP . '-' . MODULE . '-listsort'); ?>" method="POST">
                    <input type="hidden" name="method" value="PUT" />
                    <table class="am-table am-table-striped am-table-hover table-main">
                        <thead>
                            <tr>
                                <?php if ($listsort): ?>
                                    <th class="table-sort">排序</th>
                                <?php endif; ?>
                                <th class="table-set">ID</th>
                                <?php foreach ($field as $value) : ?>
                                    <?php if ($value['field_name'] == 'status'): ?>
                                        <?php $class = 'table-set'; ?>
                                    <?php else: ?>
                                        <?php $class = 'table-title'; ?>
                                    <?php endif; ?>
                                    <th class="<?= $class ?>"><?= $value['display_name']; ?></th>
                                <?php endforeach; ?>
                                <th class="table-set">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list as $key => $value) : ?>
                                <tr>
                                    <?php if ($listsort): ?>
                                        <td><input type="text" name="id[<?= $value["{$fieldPrefix}id"]; ?>]" value="<?= $value["{$fieldPrefix}listsort"]; ?>" ></td>
                                    <?php endif; ?>
                                    <td><?= $value["{$fieldPrefix}id"]; ?></td>
                                    <?php foreach ($field as $fv) : ?>
                                        <td>
                                            <?php if ($fv['field_type'] == 'date'): ?>
                                                <?= date('Y-m-d H:i', $value[$fieldPrefix . $fv['field_name']]); ?>
                                            <?php elseif (in_array($fv['field_type'], array('radio', 'checkbox', 'select'))): ?>
                                                <?= $label->getFieldOptionToMatch($fv['field_id'], $value[$fieldPrefix . $fv['field_name']]); ?>
                                            <?php else: ?>
                                                <?= $value[$fieldPrefix . $fv['field_name']]; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>


                                    <td>
                                        <?php include dirname(__FILE__) . '/Content_operate.php'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                    <ul class="am-pagination am-pagination-right am-text-sm">
                        <?= $page; ?>
                    </ul>
                    <div class="am-margin">
                        <button type="submit" class="am-btn am-btn-primary am-btn-xs">排序</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

    </div>
</div>
<!-- content end -->