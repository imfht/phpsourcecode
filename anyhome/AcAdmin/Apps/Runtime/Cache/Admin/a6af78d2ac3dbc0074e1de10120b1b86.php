<?php if (!defined('THINK_PATH')) exit();?>
<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form method="post" valid action="<?php echo ($insertUrl); ?>">
<div class="row">

    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_name">菜单名称</label>
                <input  value="<?php echo ($vo["name"]); ?>"  name="name" type="text" placeholder="" id="field_name" class="form-control ">
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_controller">控制器</label>
                <input  value="<?php echo ($vo["controller"]); ?>"  name="controller" type="text" placeholder="" id="field_controller" class="form-control ">
                </div>
            </div>
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_idx">排序</label>
                <input  value="<?php echo ($vo["idx"]); ?>"  name="idx" type="text" placeholder="" id="field_idx" class="form-control ">
                </div>
            </div>

    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_pid">上级菜单ID</label>
                <input  value="<?php echo ($vo["pid"]); ?>"  name="pid" type="text" placeholder="" id="field_pid" class="form-control ">
                </div>
            </div>
</div>

</form>
                        </div>
                        </div>
                    </div>
                </div>