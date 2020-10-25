<!-- 发布框 -->
<form action="javascript:;">
    <div>
        <div class="am-edit-publish mb">
            <input type="text" name="subject" value="">
        </div>
        <div class="row mb">
            <div class="span4">
                <input type="text" name="chargeuid" id="am_edit_charge" value="<?php echo $chargeuid; ?>">
            </div>
            <div class="span4">
                <div class="input-group datepicker" id="am_edit_starttime">
                    <span class="input-group-addon"><?php echo $lang['From']; ?></span>
                    <a href="javascript:;" class="datepicker-btn"></a>
                    <input type="text" class="datepicker-input" name="starttime" value="<?php echo $nowtime; ?>"
                           placeholder="<?php echo $lang['When to start']; ?>">
                </div>
            </div>
            <div class="span4">
                <div class="input-group datepicker" id="am_edit_endtime">
                    <span class="input-group-addon"><?php echo $lang['To']; ?></span>
                    <a href="javascript:;" class="datepicker-btn"></a>
                    <input type="text" class="datepicker-input" name="endtime" value="<?php echo $nowtime; ?>"
                           placeholder="<?php echo $lang['When to end']; ?>">
                </div>
            </div>
        </div>
        <div class="row mb">
            <div class="span12">
                <input type="text" name="participantuid" id="am_edit_participant"
                       value="">
            </div>
        </div>
        <div class="mb">
            <textarea name="description" rows="4" id="am_edit_description"
                      placeholder="<?php echo $lang['Description']; ?>"></textarea>
        </div>
        <div class="posr mbs clearfix">
            <div class="am-att-upload">
                <div id="am_edit_att_upload"></div>
            </div>
            <button class="btn btn-icon">
                <i class="o-paperclip"></i>
            </button>
            <div class="pull-right">
                <span id="am_edit_description_charcount" class="am-desc-charcount"></span>
                <button type="button" class="btn btn-primary" data-action="addPopupTask">
                    <?php echo $lang['Release']; ?>
                </button>
            </div>
        </div>
        <div id="am_edit_att_list">
        </div>
        <input type="hidden" name="attachmentid" id="am_edit_attachmentid" value="">
        <input type="hidden" name="eventparam" id="event_param" value='<?php echo $eventParam;?>'>
</form>

<script src="<?php echo STATICURL; ?>/js/app/ibos.charCount.js?<?php echo VERHASH; ?>"></script>
<script src="<?php echo $assetUrl; ?>/js/assignment_default_edit.js"></script>
