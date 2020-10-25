<script type="text/javascript">
function loadComments(paged) {
  var listId = "<?php echo $this->list_id; ?>";
  var url = "<?php echo $this->url; ?>";
  var postId = "<?php echo $this->postid; ?>";
  var langs = {
    "response": "<?php echo $this->response; ?>",
    "prev": "<?php echo $this->prev; ?>",
    "next": "<?php echo $this->next; ?>",
  };

  Posts.Comments.load(listId, url, postId, paged, langs);
}

$(document).ready(function() {
  loadComments(1);
});
</script>