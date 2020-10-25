<?php if (!defined('THINK_PATH')) exit();?>
<div class="col col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <div class="block">
                        
                        <div class="block-content-outer">
                        <div class="block-content-inner">
                            
<table id="modelObj" class="table table-bordered table-striped table-hover">
    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
            <td>
            <?php if(($id) == $v[id]): ?><input checked="checked"  type="radio" name="id" value="<?php echo ($v["id"]); ?>">选中
            <?php else: ?>
                <input  type="radio" name="id" value="<?php echo ($v["id"]); ?>">
                选中<?php endif; ?>
            </td>
            <?php if(is_array($menu[field])): $i = 0; $__LIST__ = $menu[field];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$f): $mod = ($i % 2 );++$i; if(($f[showList]) == "1"): ?><td><?php echo ($v[$f['name']]); ?></td><?php endif; endforeach; endif; else: echo "" ;endif; ?>

        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
                        </div>
                        </div>
                    </div>
                </div>