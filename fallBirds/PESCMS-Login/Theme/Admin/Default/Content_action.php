<!-- content start -->
<div class="admin-content">

    <div class="am-cf am-padding">
        <div class="am-fl am-cf">
            <a href="<?= $_GET['back_url'] ?>" class="am-margin-right-xs am-text-danger"><i class="am-icon-reply"></i>返回</a>
            <strong class="am-text-primary am-text-lg"><?= $title; ?></strong>
        </div>
    </div>
    <form class="am-form" action="<?= $url; ?>" method="post" data-am-validator>
        <input type="hidden" name="method" value="<?= $method ?>" />
        <input type="hidden" name="id" value="<?= $id ?>" />
        <input type="hidden" name="back_url" value="<?= $_GET['back_url'] ?>" />
        <div class="am-tabs am-margin">
            <ul class="am-tabs-nav am-nav am-nav-tabs">
                <li class="am-active"><a href="#tab1">基本信息</a></li>
            </ul>

            <div class="am-tabs-bd">
                <div class=" am-fade am-in am-active">

                    <?php foreach ($field as $key => $value) : ?>
                        <div class="am-g am-margin">
                            <div class="am-u-sm-4 am-u-md-2 am-text-right">
                                <?= $value['display_name'] ?>
                            </div>
                            <div class="am-u-sm-7 am-u-md-7">
                                <?= $form->formList($value); ?>
                            </div>
                            <div class="am-u-sm-3 am-u-md-3"><?= $value['field_required'] == '1' ? '*必填' : '' ?> <?= $value['field_explain']; ?></div>
                        </div>
                    <?php endforeach; ?>

                </div>

            </div>

        </div>

        <div class="am-margin">
            <button type="submit" class="am-btn am-btn-primary am-btn-xs">提交保存</button>
            <a href="<?= $label->url(GROUP . '-Model-fieldList', array('id' => $modelId)); ?>" class="am-btn am-btn-primary am-btn-xs">放弃保存</a>
        </div>
    </form>
</div>