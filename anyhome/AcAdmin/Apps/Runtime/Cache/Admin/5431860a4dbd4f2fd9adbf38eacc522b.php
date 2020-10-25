<?php if (!defined('THINK_PATH')) exit();?>
<div class="col-md-12">
    <div class="alert alert-danger">模型名称必须和ApiCloud后台,云开发,database中的类名保持一致</div>
<div class="alert alert-danger">并且请向模型添加至少一条数据</div>
</div>

<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form method="post" valid action="<?php echo ($insertUrl); ?>">
<div class="row">
    <div class=" col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="form-group">
                    <label for="field_name">模型名称</label>
                <input  value="<?php echo ($vo["name"]); ?>"  name="name" type="text" placeholder="" id="field_name" class="form-control ">
                </div>
            </div>
</div>

</form>
                        </div>
                        </div>
                    </div>
                </div>