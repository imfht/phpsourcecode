<div class="ct">
    <div class="clearfix">
        <h1 class="mt"><?php echo $lang['Common Setting'];?> > <?php echo $lang['Modify Corp Info'];?></h1>
    </div>
    <div>
        <!-- 企业信息 start -->
        <div class="ctb">
            <div class="">
                <form action="<?php echo $this->createUrl('unit/index'); ?>" method="post" enctype="multipart/form-data"
                      class="enterprise-info-form form-horizontal">
                    <div class="control-group">
                        <label class="control-label"><?php echo $lang['Enterprise logo'] ?></label>
                        <div class="controls">
                            <?php if (!empty($unit['logourl'])): ?>
                                <div class="showlogo pull-left">
                                    <img src="<?php echo $unit['logourl']; ?>"
                                         alt="<?php echo $lang['Enterprise logo'] ?>" class="custom-logo"
                                         id="upload_img">
                                    <input type="hidden" id='logoUrl' name="logourl"
                                           value="<?php echo $unit['logourl']; ?>">
                                </div>
                                <div class="showupload pull-left" style="display: none;">
                                    <input type="file" name="logo" />
                                </div>
                                <button id="switchLogo" type="button" class="btn btn-mini">更换logo</button>
                                <label>(建议尺寸为138*40)</label>
                            <?php else: ?>
                                <input type="file" name="logo" />
                                <label>(建议尺寸为138*40)</label>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="control-group" <?php if (ENGINE == 'SAAS'):?>style="display: none"<?php endif;?>>
                        <label class="control-label"><?php echo $lang['Enterprise corpcode'] ?></label>
                        <div class="controls">
                            <input type="text" name="corpcode" value="<?php
                            if (isset($unit['corpcode'])) {
                                echo $unit['corpcode'];
                            }
                            ?>"
                                   <?php if (isset($unit['corpcode']) && !empty($unit['corpcode'])): ?>readonly <?php endif; ?>>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><?php echo $lang['Enterprise fullname'] ?></label>
                        <div class="controls">
                            <?php if (!empty($license)): ?><?php echo $unit['fullname']; ?>
                            <?php else: ?>
                                <input type="text" name="fullname" value="<?php echo $unit['fullname']; ?>"
                                       <?php if (defined('LICENCE_VER') && LICENCE_VER != 'Vol' && ENGINE != 'SAAS' ): ?>readonly<?php endif; ?>>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="control-group" >
                        <label class="control-label"><?php echo $lang['Enterprise shortname'] ?></label>
                        <div class="controls">
                            <?php if (!empty($license)): ?><?php echo $unit['shortname']; ?>
                            <?php else: ?>
                                <input type="text" name="shortname" value="<?php echo $unit['shortname']; ?>"
                                       <?php if (defined('LICENCE_VER') && LICENCE_VER != 'Vol' && ENGINE != 'SAAS' ): ?>readonly<?php endif; ?>>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="control-group" <?php if (ENGINE == 'SAAS'):?>style="display: none"<?php endif;?>>
                        <label class="control-label"><?php echo $lang['System url'] ?></label>
                        <div class="controls">
                            <?php if (!empty($license)): ?><?php echo $unit['systemurl']; ?>
                            <?php else: ?>
                                <input type="text" name="systemurl"
                                       value="<?php echo isset($unit['systemurl']) ? $unit['systemurl'] : ""; ?>"
                                       <?php if (defined('LICENCE_VER') && LICENCE_VER != 'Vol'): ?>readonly<?php endif; ?>>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (ENGINE != 'SAAS') :?>
                        <div class="control-group">
                            <label class="control-label"><?php echo $lang['Admin email'] ?></label>
                            <div class="controls">
                                <input type="text" name="adminemail" value="<?php echo $unit['adminemail']; ?>">
                            </div>
                        </div>
                    <?php endif;?>
                    <div class="control-group">
                        <label class="control-label"><?php echo $lang['Phone'] ?></label>
                        <div class="controls">
                            <input type="text" name="phone" value="<?php echo $unit['phone']; ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="" class="control-label"><?php echo $lang['Address'] ?></label>
                        <div class="controls">
                            <textarea name="address" rows="3"><?php echo $unit['address']; ?></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="" class="control-label"><?php echo $lang['Cancel account']; ?></label>
                        <div class="controls">
                            <button name="cancelCorp" id="cancelCorp" type="button"
                                    class="btn btn-middle"><?php echo $lang['Cancel']; ?></button>
                            <p>注销后，系统内的所有数据会被清空，请谨慎操作。</p>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"></label>
                        <div class="controls">
                            <button name="unitSubmit" type="submit"
                                    class="btn btn-primary btn-large btn-submit"><?php echo $lang['Submit']; ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        $('#switchLogo').on('click', function () {
            $('.showlogo').hide().siblings('.showupload').show();
            $(this).hide();
        });
        $('#cancelCorp').click(function () {
            Ui.confirm('确定要注销企业吗?', function () {
                $.get(Ibos.app.url('dashboard/unit/cancel'), function (data) {
                    if (data.isSuccess){
                        window.top.location.href = '/cancel.html'
                    }else {
                        Ui.tip(data.msg, "danger");
                    }
                });
            });
        })
    })();
</script>
