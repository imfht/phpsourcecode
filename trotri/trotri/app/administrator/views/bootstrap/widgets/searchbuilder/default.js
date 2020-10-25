<script type="text/javascript">
/**
 * 提交查询表单
 * @return void
 */
$("#<?php echo $this->id; ?>").find(":button[name='_button_search_']").click(function() {
  var f = $("#<?php echo $this->id; ?>");
  var a = f.attr("action");
  var q = "";
  f.find("input").each(function() {
    if ($(this).val() != "") {
      q += "&" + $(this).attr("name") + "=" + $(this).val();
    }
  });
  f.find("select").each(function() {
    if ($(this).val() != "") {
      q += "&" + $(this).attr("name") + "=" + $(this).val();
    }
  });
  var u = a + q;
  Trotri.href(u);
});
</script>
