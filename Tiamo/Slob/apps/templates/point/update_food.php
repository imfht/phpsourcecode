<link href="/assets/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
<h2 class='contentTitle'><?php echo $title; ?></h2>
<div class='pageContent'>
    <form method='post' action='<?= URL('point/updatepoint'); ?>' class='pageForm required-validate'
          enctype="multipart/form-data" onsubmit="return iframeCallback(this);">
        <div class='pageFormContent nowrap' layoutH='97'>
            <dl>
                <dt>名称：</dt>
                <dd>
                    <input type='text' name='name' maxlength='255' class='required' value='<?= $data['name']; ?>'/>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>地区:</dt>
                <dd>
                    <select class="combox" name="city_id">
                        <?php foreach ($citys as $key => $name) { ?>
                            <?php if ($data['city_id'] == $key) { ?>
                                <option selected value="<?= $key; ?>"><?= $name; ?></option>
                            <?php } else { ?>
                                <option value="<?= $key; ?>"><?= $name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
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
                <dt>人均费用：</dt>
                <dd>
                    <input type='text' name='fee' maxlength='255' value='<?= $data['fee']; ?>'/>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>交通：</dt>
                <dd>
                    <div class='unit'>
                        <textarea class='editor' name='traffic' rows='10' cols='50'
                                  tools='simple'><?= $data['traffic']; ?></textarea>
                    </div>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>视频：</dt>
                <dd>
                    <input type='text' name='video' maxlength='255' value='<?= $data['video']; ?>'/>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>描述：</dt>
                <dd>
                    <div class='unit'>
                        <textarea class='editor' name='des' rows='10' cols='50'
                                  tools='simple'><?= $data['des']; ?></textarea>
                    </div>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>分数：</dt>
                <dd>
                    <input type='text' name='score' maxlength='255' value='<?= $data['score']; ?>'/>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>经营时间：</dt>
                <dd>
                    <input type='text' name='open_time' maxlength='11' class='required'
                           value='<?= $data['open_time']; ?>'/>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>电话：</dt>
                <dd>
                    <input type='text' name='phone' maxlength='11' class='required' value='<?= $data['phone']; ?>'/>
                    <span class='info'></span>
                </dd>
            </dl>
            <dl>
                <dt>小贴士：</dt>
                <dd>
                    <div class='unit'>
                        <textarea class='editor' name='tip' rows='10' cols='50'
                                  tools='simple'><?= $data['tip']; ?></textarea>
                    </div>
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
                            <input type='hidden' name='type' maxlength='100' value="food"/>
                            <input type='hidden' name='id' value="<?= $data['id']; ?>"/>
                            <button type='button' class='close'>取消</button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </form>
</div>