<!-- content start -->
<div class="admin-content">

    <div class="am-cf am-padding">
        <div class="am-fl am-cf">
            <a href="<?= $_GET['back_url'] ?>" class="am-margin-right-xs am-text-danger"><i class="am-icon-reply"></i>返回</a>
            <strong class="am-text-primary am-text-lg"><?= $title; ?></strong> / <small>后台菜单</small>
        </div>
    </div>
    <form class="am-form" action="<?= $url; ?>" method="post" data-am-validator>
        <input type="hidden" name="method" value="<?= $method ?>" />
        <input type="hidden" name="field_id" value="<?= $field_id ?>" />
        <input type="hidden" name="model_id" value="<?= $modelId ?>" />
        <div class="am-tabs am-margin">
            <ul class="am-tabs-nav am-nav am-nav-tabs">
                <li class="am-active"><a href="#tab1">基本信息</a></li>
            </ul>

            <div class="am-tabs-bd">
                <div class="am-tab-panel am-fade am-in am-active">

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">
                            字段类型
                        </div>
                        <div class="am-u-sm-8 am-u-md-3">
                            <select name="field_type" id="menu-pid" <?= $method == 'PUT' ? 'disabled="disabled"' : '' ?> required>
                                <option value="">请选择</option>
                                <?php foreach ($fieldTypeList as $key => $value) : ?>
                                    <option value="<?= $key; ?>" <?= $field_type == $key ? 'selected="selected"' : '' ?>><?= $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="am-hide-sm-only am-u-md-6">*必填</div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">
                            字段名称
                        </div>
                        <div class="am-u-sm-8 am-u-md-4">
                            <input type="text" class="am-input-sm" name="field_name" value="<?= $field_name ?>" <?= $method == 'PUT' ? 'disabled="disabled"' : 'required' ?>>
                        </div>
                        <div class="am-hide-sm-only am-u-md-6">*必填，仅限英文下划线</div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">
                            显示名称
                        </div>
                        <div class="am-u-sm-8 am-u-md-4">
                            <input type="text" class="am-input-sm" name="display_name" value="<?= $display_name ?>" required>
                        </div>
                        <div class="am-hide-sm-only am-u-md-6">*必填</div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">
                            字段说明
                        </div>
                        <div class="am-u-sm-8 am-u-md-4">
                            <textarea rows="4" name="field_explain" ><?= $field_explain; ?></textarea>
                        </div>
                        <div class="am-hide-sm-only am-u-md-6">描述该字段作用</div>
                    </div>


                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">
                            字段选项值
                        </div>
                        <div class="am-u-sm-8 am-u-md-4">
                            <textarea rows="4" name="field_option" ><?= $label->fieldOption($field_option); ?></textarea>
                        </div>
                        <div class="am-hide-sm-only am-u-md-6">选填， 选填， 此处若没有特殊说明，必须 名称|值 填写、且一行一个选项值，否则将导致数据异常! <br/>注意:目前选项适用于单选，复选，下拉菜单。其余功能填写也不会产生任何实际效果。</div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">
                            字段默认值
                        </div>
                        <div class="am-u-sm-8 am-u-md-4">
                            <input type="text" class="am-input-sm" name="field_default" value="<?= $field_default ?>">
                        </div>
                        <div class="am-hide-sm-only am-u-md-6"></div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">是否必填项</div>
                        <div class="am-u-sm-8 am-u-md-10">
                            <div class="am-form-group am-margin-bottom-0">
                                <label class="am-radio-inline">
                                    <input type="radio"  value="1" name="field_required" <?= $field_required == '1' ? 'checked="checked"' : '' ?> required> 是
                                </label>
                                <label class="am-radio-inline">
                                    <input type="radio" value="0" name="field_required" <?= $field_required == '0' ? 'checked="checked"' : '' ?>> 否
                                </label>
                            </div>

                        </div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">字段状态</div>
                        <div class="am-u-sm-8 am-u-md-10">
                            <div class="am-form-group am-margin-bottom-0">
                                <label class="am-radio-inline">
                                    <input type="radio"  value="1" name="field_status" <?= $field_status == '1' ? 'checked="checked"' : '' ?> required> 启用
                                </label>
                                <label class="am-radio-inline">
                                    <input type="radio" value="0" name="field_status" <?= $field_status == '0' ? 'checked="checked"' : '' ?>> 禁用
                                </label>
                            </div>
                        </div>
                    </div>


                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">显示列表</div>
                        <div class="am-u-sm-4 am-u-md-4">
                            <div class="am-form-group am-margin-bottom-0">
                                <label class="am-radio-inline">
                                    <input type="radio"  value="1" name="field_list" <?= $field_list == '1' ? 'checked="checked"' : '' ?> required> 显示
                                </label>
                                <label class="am-radio-inline">
                                    <input type="radio" value="0" name="field_list" <?= $field_list == '0' ? 'checked="checked"' : '' ?>> 隐藏
                                </label>
                            </div>
                        </div>
                        <div class="am-hide-sm-only am-u-md-6">想在列表中显示该字段,请勾选显示</div>
                    </div>

                    <div class="am-g am-margin-top">
                        <div class="am-u-sm-4 am-u-md-2 am-text-right">
                            字段排序
                        </div>
                        <div class="am-u-sm-8 am-u-md-4">
                            <input type="text" class="am-input-sm" name="field_listsort" value="<?= $field_listsort ?>">
                        </div>
                        <div class="am-hide-sm-only am-u-md-6"></div>
                    </div>

                </div>

            </div>

        </div>

        <div class="am-margin">
            <button type="submit" class="am-btn am-btn-primary am-btn-xs">提交保存</button>
            <a href="<?= $label->url(GROUP.'-Model-fieldList', array('id' => $modelId)); ?>" class="am-btn am-btn-primary am-btn-xs">放弃保存</a>
        </div>
    </form>
</div>
<!-- content end -->