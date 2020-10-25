<?php if (!defined('THINK_PATH')) exit();?>
<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            <form action="<?php echo ($insertUrl); ?>">
<div class="row">
    <div class=" col-md-4 col-sm-4 col-xs-4 col-lg-4">
                    <div class="form-group">
                    <label for="field_rid">角色名称</label>
                <select name="rid" class="form-control">
            <?php echo roleOpt();?>
        </select>
                </div>
            </div>
    
    <div class=" col-md-4 col-sm-4 col-xs-4 col-lg-4">
                    <div class="form-group">
                    <label for="field_">所属菜单</label>
                <select name="menu_id" class="form-control">
            <?php if(is_array($Menus)): $i = 0; $__LIST__ = $Menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i; if(empty($m[items])): ?><option value="<?php echo ($m["id"]); ?>" ><?php echo ($m["name"]); ?></option>    
            <?php else: ?>
                <optgroup label="<?php echo ($m["name"]); ?>">
                    <?php if(is_array($m[items])): $i = 0; $__LIST__ = $m[items];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mm): $mod = ($i % 2 );++$i;?><option value="<?php echo ($mm["id"]); ?>" ><?php echo ($mm["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </optgroup><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </select>
                </div>
            </div>
</div>
</form>
                        </div>
                        </div>
                    </div>
                </div>