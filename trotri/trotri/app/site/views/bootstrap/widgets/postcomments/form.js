<script type="text/javascript">
$("#<?php echo $this->form_id; ?> :button[name='_button_save_']").click(function() {
  var formId = "<?php echo $this->form_id; ?>";
  var isPublish = <?php echo $this->is_publish; ?>;
  var langs = {
    "just_now": "<?php echo $this->just_now; ?>",
    "response": "<?php echo $this->response; ?>",
    "auditing": "<?php echo $this->auditing; ?>",
  };
  Posts.Comments.save(formId, isPublish, langs);
});
</script>