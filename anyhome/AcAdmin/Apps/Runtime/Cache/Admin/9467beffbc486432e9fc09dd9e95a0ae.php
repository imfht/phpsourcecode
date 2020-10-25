<?php if (!defined('THINK_PATH')) exit();?>
<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form method="post" valid action="<?php echo ($insertUrl); ?>">
<div class="row">

    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_name">角色名称</label>
                <input  value="<?php echo ($vo["name"]); ?>"  name="name" type="text" placeholder="" id="field_name" class="form-control ">
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_controller">角色说明</label>
                <input  value="<?php echo ($vo["controller"]); ?>"  name="controller" type="text" placeholder="" id="field_controller" class="form-control ">
                </div>
            </div>
</div>

</form>
                        </div>
                        </div>
                    </div>
                </div>