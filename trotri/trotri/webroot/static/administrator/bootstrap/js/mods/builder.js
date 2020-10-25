$(document).ready(function() {
  if (g_ctrl == "validators" && (g_act == "create" || g_act == "modify")) {
    $("select[name='validator_name']").change(function() {
      Builder.loadMessageByValidatorName();
    });
    if (g_act == "create") {
      Builder.loadMessageByValidatorName();
    }
  }
  if (g_ctrl == "fields" && (g_act == "create" || g_act == "modify")) {
    $("select[name='form_prompt_examples']").change(function() {
      var htmlLabel = $(":text[name='html_label']").val();
      var formPromptExamples = $(this).find("option:selected").text();
      $(":text[name='form_prompt']").val(formPromptExamples.replace("{field}", htmlLabel));
    });
  }
});

/**
 * Builder
 * @author songhuan <trotri@yeah.net>
 * @version $Id: builder.js 1 2013-10-16 18:38:00Z $
 */
Builder = {
  /**
   * 新增或编辑字段验证管理，选择“验证类名”时改变“出错提示消息”
   * @return void
   */
  loadMessageByValidatorName: function() {
    var validatorName = $("select[name='validator_name']").val();
    var fieldName = $(":text[name='field_name']").val();

    var optionCategory = messageEnum[validatorName]['option_category'];
    var optionCategoryLang = optionCategoryEnum[optionCategory];
    var message = messageEnum[validatorName]['message'].replace("{field}", fieldName);

    $(":radio[name='option_category']").each(function() {
      if ($(this).val() == optionCategory) {
        $(this).iCheck("check");
      }
    });

    $(":text[name='options']").parent().next("span").html("Suggest: " + optionCategoryLang);
    $(":text[name='message']").val(message);
  }
}
