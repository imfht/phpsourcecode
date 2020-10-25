<div id="x17kwr" class="hide"></div>

<table class="lc-editor-set-form" width="100%">

  <tr class="l">
    <td width="260px" class="t"><?php echo $this->T('Tab Stops')?></td>
    <td>
      <div class="input-prepend">
        <span class="add-on"><?php echo $this->T('Tab width')?></span>
        <input class="span1" id="tabSize" type="text" value="4" onchange="_lc_editorset_save('<?php echo $this->T('Tab width')?>')">
      </div>
      <label class="checkbox">
        <input type="checkbox" id="tabs2spaces" value="1" onchange="_lc_editorset_save('<?php echo $this->T('Insert spaces instead of tabs')?>')" />
       <?php echo $this->T('Insert spaces instead of tabs')?>
      </label>
    </td>
  </tr>
  
  <tr class="l">
    <td class="t"><?php echo $this->T('Automatic Indentation')?></td>
    <td>
      <label class="checkbox">
        <input type="checkbox" id="smartIndent" value="1" onchange="_lc_editorset_save('<?php echo $this->T('Enable automatic indentation')?>')" />
       <?php echo $this->T('Enable automatic indentation')?>
      </label>
    </td>
  </tr>
  
  <tr class="l">
    <td class="t"><?php echo $this->T('Text Wrapping')?></td>
    <td>
      <label class="checkbox">
        <input type="checkbox" id="lineWrapping" value="1" onchange="_lc_editorset_save('<?php echo $this->T('Enable text wrapping')?>')" />
       <?php echo $this->T('Enable text wrapping')?>
      </label>
    </td>
  </tr>

  <tr class="l">
    <td class="t"><?php echo $this->T('Code Folding')?></td>
    <td>
      <label class="checkbox">
        <input type="checkbox" id="codeFolding" value="1" onchange="_lc_editorset_save('<?php echo $this->T('Enable Code Folding')?>')" />
       <?php echo $this->T('Enable Code Folding')?>
      </label>
    </td>
  </tr>

  <tr class="l">
    <td class="t"><?php echo $this->T('Code Autocomplete')?></td>
    <td>
      <label><?php echo $this->T('Press `shift + space` to activate autocompletion')?></label>
      <p class="alert alert-info"><?php echo $this->T('editor-autocomplete-desc')?></p>
    </td>
  </tr>

  <tr class="l">
    <td class="t"><?php echo $this->T('Font Size')?></td>
    <td>
      <div class="input-append">
        <input class="span1" id="fontSize" type="text" value="13">
        <span class="add-on">px</span>
      </div>
    </td>
  </tr>

  <tr class="">
    <td class="t"><?php echo $this->T('Color Scheme')?></td>
    <td>
      <select id="editor_theme" onchange="_lc_editorset_theme(this)">
        <option value="default" selected="">classic</option>
        <option value="monokai">monokai</option>
        <option value="ambiance">ambiance</option>
        <option value="blackboard">blackboard</option>
        <option value="eclipse">eclipse</option>
        <option value="erlang-dark">erlang-dark</option>
        <option value="lesser-dark">lesser-dark</option>
        <option value="rubyblue">rubyblue</option>
        <option value="twilight">twilight</option>             
      </select> 
    </td>
  </tr>
  
</table>


<script>
lessModalButtonAdd("ytibxk", "<?php echo $this->T('Save and Close')?>", "_lc_editorset_close()", "btn-inverse");
lessModalButtonAdd("bf65gr", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

function _lc_editorset_close()
{
    _lc_editorset_save("");
    setTimeout(lessModalClose, 600);
}

function _lc_editorset_save(title)
{
    lcEditor.Config.tabSize = parseInt($("#tabSize").val());
    if (lcEditor.Config.tabSize > 12 || lcEditor.Config.tabSize < 1) {
        lcEditor.Config.tabSize = 4;
    }

    lcEditor.Config.fontSize = parseInt($("#fontSize").val());
    if (lcEditor.Config.fontSize > 50) {
        lcEditor.Config.fontSize = 50;
    }
    if (lcEditor.Config.fontSize < 8) {
        lcEditor.Config.fontSize = 8;
    }
    lessCookie.SetByDay('editor_fontSize', lcEditor.Config.fontSize, 365);
    $("#fontSize").val(lcEditor.Config.fontSize);
    $(".CodeMirror-lines").css({"font-size": lcEditor.Config.fontSize+"px"});

    lcEditor.Config.tabs2spaces = $("#tabs2spaces").prop('checked') ? true : false;
    lessCookie.SetByDay('editor_tabs2spaces', lcEditor.Config.tabs2spaces, 365);
    
    lcEditor.Config.smartIndent = $("#smartIndent").prop('checked') ? true : false;
    lessCookie.SetByDay('editor_smartIndent', lcEditor.Config.smartIndent, 365);
    
    lcEditor.Config.lineWrapping = $("#lineWrapping").prop('checked') ? true : false;
    lessCookie.SetByDay('editor_lineWrapping', lcEditor.Config.lineWrapping, 365);

    lcEditor.Config.codeFolding = $("#codeFolding").prop('checked') ? true : false;
    lessCookie.SetByDay('editor_codeFolding', lcEditor.Config.codeFolding, 365);
    
    if (title.length > 0) {
        title = '"'+title+'"';
    }
    lessAlert('#x17kwr', 'alert-success', '<?php echo $this->T('Successfully Saved')?> '+title);
}

function _lc_editorset_theme(node)
{
    var theme = node.options[node.selectedIndex].value;
    lcEditor.Theme(theme);
}

function _lc_editorset_init()
{
    $("#tabSize").val(lcEditor.Config.tabSize);
    $("#fontSize").val(lcEditor.Config.fontSize);

    if (lcEditor.Config.tabs2spaces) {

        $("#tabs2spaces").prop("checked", true);
    }
    
    if (lcEditor.Config.smartIndent) {
        $("#smartIndent").prop("checked", true);
    }

    if (lcEditor.Config.lineWrapping) {
        $("#lineWrapping").prop("checked", true);
    }

    if (lcEditor.Config.codeFolding) {
        $("#codeFolding").prop("checked", true);
    }

    var theme = lessCookie.Get('editor_theme');
    $("#editor_theme option:contains('"+theme+"')").prop("selected", true);       
}

_lc_editorset_init();
</script>
