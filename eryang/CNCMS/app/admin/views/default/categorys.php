<?php
echo $this->load->view ( 'header' );
?>
<!--头部开始-->
<div>
    <ul class="breadcrumb">
        <li><a href="<?php echo site_url('main'); ?>"><?php echo lang('set_system'); ?></a><span
                class="divider">/</span></li>
        <li><a
                href="<?php echo site_url($this -> config -> item('admin_folder').'category'); ?>"><?php echo $title; ?></a></li>
    </ul>
</div>
<!--头部结束-->
<?php
if ($this->session->flashdata ( 'message' )) {
    $message = $this->session->flashdata ( 'message' );
}
if ($this->session->flashdata ( 'error' )) {
    $error = $this->session->flashdata ( 'error' );
}
if (function_exists ( 'validation_errors' ) && validation_errors () != '') {
    $error = validation_errors ();
}
?>

<?php if (!empty($message)): ?>
    <div class="alert alert-info">
        <a class="close" data-dismiss="alert">×</a>
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert">×</a>
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<!--类别列表开始-->
<div class="row-fluid">
    <div class="box span12">
        <div class="box-header well">
            <h2>
                <i class="icon-info-sign"></i> <?php echo lang('category_list'); ?></h2>
            <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i
                        class="icon-chevron-up"></i></a> <a href="#"
                                                            class="btn btn-close btn-round"><i class="icon-remove"></i></a>
            </div>
        </div>
        <div class="box-content">
            <table
                class="table table-striped table-bordered bootstrap-datatable datatable">
                <thead>
                <tr>
                    <th><?php echo lang('category_status'); ?></th>
                    <th><?php echo lang('category_name'); ?></th>
                    <th><?php echo lang('category_type'); ?></th>
                    <th><?php echo lang('category_rank'); ?></th>
                    <th><?php echo lang('category_handle'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if($datas):?>
                    <?php foreach($datas as $n => $category_datas): ?>
                        <tr id="tr_category_<?php echo $category_datas['id']; ?>">
                            <td><?php if($category_datas['status'] == 1): ?><span
                                    title="<?php echo lang('category_status_open'); ?>"
                                    class="icon icon-unlocked"></span><?php else: ?><span
                                    title="<?php echo lang('category_status_close'); ?>"
                                    class="icon icon-locked"></span><?php endif; ?></td>
                            <td class="category_left" style="padding-left:<?php echo $category_datas['level']*24,'px'; ?>;">
                                <?php if(isset($datas[$n - 1])): ?>
                                    <?php if(isset($category_datas['last_one'])): ?>
                                        <?php if(isset($category_datas['have_child'])): ?>
                                            <span class="icon  icon-treeview-corner-minus"></span>
                                        <?php else: ?>
                                            <span class="icon  icon-treeview-corner"></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(isset($category_datas['have_child'])): ?>
                                            <span class="icon  icon-treeview-corner-minus"></span>
                                        <?php else: ?>
                                            <span class="icon  icon-treeview-vertical-line"></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if(isset($category_datas['last_one'])): ?>
                                        <?php if(isset($category_datas['have_child'])): ?>
                                            <span class="icon  icon-square-minus"></span>
                                        <?php else: ?>
                                            <span class="icon  icon-arrow-e"></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(isset($category_datas['have_child'])): ?>
                                            <span class="icon  icon-treeview-corner-minus"></span>
                                        <?php else: ?>
                                            <span class="icon icon-treeview-vertical-line"></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php echo $category_datas['name']; ?>
                            </td>
                            <td>   <?php if($category_datas['type']==1):?>
                                    <?php echo lang('category_type_list'); ?>
                                <?php elseif($category_datas['type']==2):?>
                                    <?php echo lang('category_type_page'); ?>
                                <?php endif;?>
                            </td>
                            <td><?php echo $category_datas['rank']; ?></td>

                            <td>
                                <?php if($category_datas['id']==1):?>
                                    <span title="<?php echo lang('category_unedit'); ?>"
                                          class="icon icon-gear"></span> <span
                                        title="<?php echo lang('category_undelete'); ?>"
                                        class="icon  icon-cross"></span>
                                <?php else:?>
                                    <a
                                        href="<?php echo site_url($this -> config -> item('admin_folder').'category/form/'.$category_datas['id']); ?>"
                                        title="<?php echo lang('category_edit'); ?>"><span
                                            title="<?php echo lang('category_edit'); ?>"
                                            class="icon  icon-wrench"></span></a>
                                    <?php if(isset($category_datas['have_child'])): ?><span
                                        title="<?php echo lang('category_undelete'); ?>"
                                        class="icon  icon-cross"></span>
                                    <?php else: ?>
                                        <?php /*<a href="javascript:void(0);" onclick="return show_dialog('删除信息','确定删除该记录吗？', 'del', '<?php echo site_url('category/del').'/'.$category_datas['id']; ?>', 'tr_category_<?php echo $category_datas['id']; ?>');" title="删除">*/?>
                                        <a href="javascript:void(0);"
                                           onclick="return show_delete_confirm('<?php echo lang('delete_message');?>','<?php echo lang('delete_message_confirm');?>', '<?php echo site_url($this -> config -> item('admin_folder').'category/del/'.$category_datas['id']); ?>');"
                                           title="<?php echo lang('category_delete'); ?>"> <span
                                                title="<?php echo lang('category_delete'); ?>"
                                                class="icon  icon icon-close"></span></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else:?>
                    <tr><td colspan="5"><div class="center"><h4>无任何数据</h4></div></td></tr>
                <?php endif;?>
            </table>
            <?php
            /*
            <?php if($pagination):?>

                <div class="pagination pagination-centered">
                    <?php echo $pagination; ?>

                </div>
            <?php endif;?>*/
            ?>
        </div>
    </div>
</div>
<!--类别列表结束-->
<?php
echo $this->load->view ( 'footer' );
?>
</body>
</html>
