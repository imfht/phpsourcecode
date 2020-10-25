<div class="container">

<div class="col-xs-12 col-sm-10">
  <div class="row">
  <?php echo $this->getHtml()->openForm('#', 'post', array('class' => 'form-horizontal', 'id' => 'social')); ?>

    <h2 class="form-signin-heading"><?php echo $this->CFG_SYSTEM_GLOBAL_SOCIAL; ?><small class="alert"></small></h2>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_REALNAME_LABEL; ?></label>
      <div class="col-lg-6">
        <input class="form-control input-sm" type="text" name="realname" value="<?php echo $this->realname; ?>" />
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_SEX_LABEL; ?></label>
      <?php foreach ($this->sex_enum as $key => $value) : ?>
      <label class="checkbox-inline"><input type="radio" name="sex" value="<?php echo $key; ?>" <?php if ($this->sex === $key) : ?>checked<?php endif; ?> />&nbsp;<?php echo $value; ?></label>
      <?php endforeach; ?>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_BIRTH_YMD_LABEL; ?></label>
      <div class="col-lg-6">
        <select class="input-sm" name="birth_y">
          <option value=""><?php echo $this->CFG_SYSTEM_GLOBAL_YEAR; ?></option>
        <?php for ($year = date('Y'); $year > 1901; $year--) : ?>
          <option value="<?php echo $year; ?>" <?php if ($year == $this->birth_y) : ?>selected<?php endif; ?> ><?php echo $year; ?></option>
        <?php endfor; ?>
        </select>
        <select class="input-sm" name="birth_m">
          <option value=""><?php echo $this->CFG_SYSTEM_GLOBAL_MONTH; ?></option>
        <?php for ($month = 1; $month < 13; $month++) : ?>
          <?php if ($month < 10) { $month = '0' . $month; } ?>
          <option value="<?php echo $month; ?>" <?php if ($month == $this->birth_m) : ?>selected<?php endif; ?> ><?php echo $month; ?></option>
        <?php endfor; ?>
        </select>
        <select class="input-sm" name="birth_d">
          <option value=""><?php echo $this->CFG_SYSTEM_GLOBAL_DAY; ?></option>
        <?php $year = (int) $this->birth_y; $month = (int) $this->birth_m; $maxDay = 31; if (in_array($month, array(4, 6, 9, 11))) { $maxDay = 30; } if ($month === 2) { $maxDay = 28; if (($year%4 === 0) && (($year%100 !== 0) || ($year%400 === 0))) { $maxDay = 29; } } ?>
        <?php for ($day = 1; $day <= $maxDay; $day++) : ?>
          <?php if ($day < 10) { $day = '0' . $day; } ?>
          <option value="<?php echo $day; ?>" <?php if ($day == $this->birth_d) : ?>selected<?php endif; ?> ><?php echo $day; ?></option>
        <?php endfor; ?>
        </select>
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_IS_PUB_BIRTH_LABEL; ?></label>
      <div class="col-lg-6">
        <input type="checkbox" name="is_pub_birth" value="y" <?php if ($this->is_pub_birth === 'y') : ?>checked<?php endif; ?> />
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_HEAD_PORTRAIT_LABEL; ?></label>
      <div class="col-lg-6">
        <input class="form-control input-sm" type="text" name="head_portrait" value="<?php echo $this->head_portrait; ?>" />
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"></label>
      <div class="col-lg-6">
        <div id="head_portrait_file"><?php echo $this->CFG_SYSTEM_GLOBAL_UPLOAD; ?></div>
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_INTERESTS_LABEL; ?></label>
      <div class="col-lg-8">
        <?php foreach ($this->interests_enum as $key => $value) : ?>
        <label class="checkbox-inline"><input type="checkbox" name="interests[]" value="<?php echo $key; ?>" <?php if (in_array($key, $this->interests)) : ?>checked<?php endif; ?> />&nbsp;<?php echo $value; ?></label>
        <?php endforeach; ?>
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_IS_PUB_INTERESTS_LABEL; ?></label>
      <div class="col-lg-6">
        <input type="checkbox" name="is_pub_interests" value="y" <?php if ($this->is_pub_interests === 'y') : ?>checked<?php endif; ?> />
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_QQ_LABEL; ?></label>
      <div class="col-lg-6">
        <input class="form-control input-sm" type="text" name="qq" value="<?php echo $this->qq; ?>" />
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_ADDRESS_LIVE_LABEL; ?></label>
      <div class="col-lg-6">
        <select class="input-sm" name="live_province_id"></select>
        <select class="input-sm" name="live_city_id"></select>
        <select class="input-sm" name="live_district_id"></select>
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_ADDRESS_ADDRESS_LABEL; ?></label>
      <div class="col-lg-6">
        <select class="input-sm" name="address_province_id"></select>
        <select class="input-sm" name="address_city_id"></select>
        <select class="input-sm" name="address_district_id"></select>
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label"><?php echo $this->MOD_MEMBER_SOCIAL_INTRODUCE_LABEL; ?></label>
      <div class="col-lg-6">
        <textarea class="form-control input-sm" rows="8" name="introduce"><?php echo $this->introduce; ?></textarea>
      </div>
      <span class="control-label"></span>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">&nbsp;&nbsp;</label>
      <div class="col-lg-4">
        <?php echo $this->getHtml()->button($this->CFG_SYSTEM_GLOBAL_CONFIRM, 'social_button', array('class' => 'btn btn-lg btn-primary btn-block', 'onclick' => 'return Member.ajaxSocial();')); ?>
      </div>
      <span class="control-label"></span>
    </div>

  <?php echo $this->getHtml()->closeForm(); ?>
  </div>
</div>

</div><!-- /.container -->

<?php echo $this->getHtml()->cssFile($this->static_url . '/plugins/jquery-upload-file/uploadpreviewimg.css'); ?>
<?php echo $this->getHtml()->jsFile($this->static_url . '/plugins/jquery-upload-file/jquery.uploadfile.min.js'); ?>

<?php echo $this->getHtml()->js('var g_data = ' . json_encode(array(
	'live_country_id' => $this->live_country_id,
	'live_province_id' => $this->live_province_id,
	'live_city_id' => $this->live_city_id,
	'live_district_id' => $this->live_district_id,
	'address_country_id' => $this->address_country_id,
	'address_province_id' => $this->address_province_id,
	'address_city_id' => $this->address_city_id,
	'address_district_id' => $this->address_district_id
)) . ';'); ?>
