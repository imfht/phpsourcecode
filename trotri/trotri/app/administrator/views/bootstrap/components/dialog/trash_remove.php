<!-- Modal Dialog Trash|Remove -->
<div class="modal fade" id="dialog_trash_remove" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <h2 id="dialog_trash_remove_view_body">...</h2>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="dialog_trash_remove_url">
        <input type="hidden" name="dialog_trash_remove_is_batch" value="0">
        <input type="hidden" name="dialog_trash_remove_ids" value="">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->CFG_SYSTEM_GLOBAL_CANCEL; ?></button>
        <button type="button" class="btn btn-primary" onclick="return Core.afterDialogTrashRemove();"><?php echo $this->CFG_SYSTEM_GLOBAL_CONFIRM; ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal fade -->
<!-- /Modal Dialog Trash|Remove -->
