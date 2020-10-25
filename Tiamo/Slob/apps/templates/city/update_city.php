<link href="<?php echo ASSETS;?>/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
<h2 class='contentTitle'><?php echo $title; ?></h2>
<div class='pageContent'>
    <form method='post' action='<?= URL('city/updatecity'); ?>' class='pageForm required-validate'
          enctype="multipart/form-data" onsubmit="return iframeCallback(this);">
        <div class='pageFormContent nowrap' layoutH='97'>
            <input type='hidden' name='id' value='<?= $data['id']; ?>'/>
            <dl>
                <dt>城市名称：</dt>
                <dd>
                    <input type='text' name='name' maxlength='255' class='required' value='<?= $data['name']; ?>'/>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>图片：</dt>
            </dl>
            <dl>
                <div class="unit">
                    <ul id="upload-preview" class="upload-preview">
                        <?php if ($data['imgs']) { ?>
                            <?php foreach ($data['imgs'] as $img) { ?>
                                <li class="thumbnail"><img src="<?= $img ?>" data-width="0" data-height="0" width="80">
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                    <div class="upload-wrap" rel="#upload-preview">
                        <input type="file" name="imgs[]" accept="image/*" multiple="multiple" style="left: 0px;">
                    </div>
                </div>
            </dl>
            <dl>
                <dt>描述：</dt>
                <dd>
                    <div class='unit'>
                        <textarea class='editor' name='desc' rows='30' cols='50'
                                  tools='simple'><?= $data['desc']; ?></textarea>
                    </div>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>邮编：</dt>
                <dd>
                    <input type='text' name='code' maxlength='10' class='required' value='<?= $data['code']; ?>'/>
                    <span class='info'></span>
                </dd>
            </dl>
            <div class='divider'></div>
        </div>
        <div class='formBar'>
            <ul>
                <input type='hidden' name='isPost' value='1'/>
                <li>
                    <div class='buttonActive'>
                        <div class='buttonContent'>
                            <button type='submit'>提交</button>
                        </div>
                    </div>
                </li>
                <li>
                    <div class='button'>
                        <div class='buttonContent'>
                            <button type='button' class='close'>取消</button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </form>
</div>