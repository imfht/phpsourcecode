<script type="text/javascript">
/**
 * 选中第一个有错误验证的Tab
 * @return void
 */
$("#<?php echo $this->id; ?>").find(".form-group").each(function() {
  if ($(this).hasClass("has-error")) {
    if ($(this).parent().hasClass("active in")) {
      return false;
    }

    $(this).parent().addClass("active in");
    $(this).parent().siblings().each(function() {
      $(this).removeClass("active in");
    });

    var id = "#" + $(this).parent().attr("id");
    $(this).parent().parent().siblings("ul").find("li").each(function() {
      if ($(this).find("a").attr("href") == id) {
        $(this).addClass("active");
      }
      else {
        $(this).removeClass("active");
      }
    });

    return false;
  }
});
</script>