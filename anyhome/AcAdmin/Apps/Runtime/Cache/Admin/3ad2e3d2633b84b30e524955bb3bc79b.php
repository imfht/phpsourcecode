<?php if (!defined('THINK_PATH')) exit();?>
<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form method="post" valid action="<?php echo ($updateUrl); ?>">
<input type="hidden" name="code" value="<?php echo ($vo["code"]); ?>">
<div class="row">
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_name">应用名称</label>
                <input  value="<?php echo ($vo["name"]); ?>"  name="name" type="text" placeholder="" id="field_name" class="form-control ">
                </div>
            </div>
    <div class=" col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <div class="form-group">
                    <label for="field_app_id">API_ID</label>
                <input  value="<?php echo ($vo["app_id"]); ?>"  name="app_id" type="text" placeholder="" id="field_app_id" class="form-control ">
                </div>
            </div>
    <div class=" col-md-6 col-sm-6 col-xs-6 col-lg-6">
                    <div class="form-group">
                    <label for="field_app_key">API_KEY</label>
                <input  value="<?php echo ($vo["app_key"]); ?>"  name="app_key" type="text" placeholder="" id="field_app_key" class="form-control ">
                </div>
            </div>    
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_intro">应用说明</label>
                <input  value="<?php echo ($vo["intro"]); ?>"  name="intro" type="text" placeholder="" id="field_intro" class="form-control ">
                </div>
            </div>
</div>

</form>
                        </div>
                        </div>
                    </div>
                </div>