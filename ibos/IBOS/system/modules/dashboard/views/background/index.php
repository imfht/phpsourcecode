<div class="ct">
    <div class="clearfix">
        <h1 class="mt"><?php echo $lang['Common Setting'];?> > <?php echo $lang['Nav Blendent'];?></h1>
    </div>
    <div class="ctb">
        <div>
            <ul class="grid-list pic-list clearfix" id="bgstyle_select_list">
                <li>
                    <img src="<?php echo $this->getAssetUrl(); ?>/image/bg_black.png">
                    <div class="pic-item-operate bg-item-operate">
                        <div class="pull-left operate-wrap">
                            <label class="radio">
                                <input type="radio" name="bgstyle"
                                       <?php if ($skin == 'black' || empty($skin)): ?>checked<?php endif; ?>
                                       value="black"/>
                                <span>酷炫黑（适合配搭浅色LOGO）</span>
                            </label>
                        </div>
                    </div>
                </li>
                <li>
                    <img src="<?php echo $this->getAssetUrl(); ?>/image/bg_white.png">
                    <div class="pic-item-operate bg-item-operate">
                        <div class="pull-left operate-wrap">
                            <label class="radio">
                                <input type="radio" name="bgstyle" <?php if ($skin == 'white'): ?>checked<?php endif; ?>
                                       value="white"/>
                                <span>闪耀白（适合配搭深色LOGO）</span>
                            </label>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="form__submit-btn">
            <button type="button" class="btn btn-primary btn-large btn-submit" id="background_save">提交</button>
        </div>
    </div>
</div>
<script src="<?php echo $assetUrl; ?>/js/db_background.js?<?php echo VERHASH; ?>"></script>