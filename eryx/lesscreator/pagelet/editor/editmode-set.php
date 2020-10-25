
<style>
.x7o1tn {
    width: 100%;
}

.x7o1tn .itemimg {
    width: 64px;
    height: 64px;
    margin-bottom: 10px;
}

</style>

<div id="en8dfy" class="alert alert-info">
    <?php echo $this->T('Select one of your favorite editor mode')?>
</div>

<table class="x7o1tn">
<tr>
  <td>
    <img class="itemimg" src="/lesscreator/static/img/editor/mode-win-128.png" />
    <label class="radio">
      <input type="radio" name="cfg_editor_mode" value="win" onclick="_lc_editor_mode('win')" /> <?php echo $this->T('Default')?>
    </label>
  </td>
  <td>
    <img class="itemimg" src="/lesscreator/static/img/editor/mode-vim-128.png" />
    <label class="radio">
      <input type="radio" name="cfg_editor_mode" value="vim" onclick="_lc_editor_mode('vim')" /> <?php echo $this->T('Vim')?>
    </label>
  </td>
  <td>
    <img class="itemimg" src="/lesscreator/static/img/editor/mode-emacs-128.png" />
    <label class="radio">
      <input type="radio" name="cfg_editor_mode" value="emacs" onclick="_lc_editor_mode('emacs')" /> <?php echo $this->T('Emacs')?>
    </label>
  </td>
</tr>

</table>

<script>
lessModalButtonAdd("bf65gr", "<?php echo $this->T('Close')?>", "lessModalClose()", "");

function _lc_editor_mode(mode)
{
    switch (mode) {
    case "win":
        mode = null;
        break;
    case "vim":
        break;
    case "emacs":
        break;
    default:
        return;
    }

    console.log("mode:"+ mode);

    var icosrc = "/lesscreator/static/img/editor/mode-";

    if (h5cTabletFrame['w0'].editor != null) {

        if (mode == null) {
            h5cTabletFrame['w0'].editor.removeKeyMap("vim");
            h5cTabletFrame['w0'].editor.removeKeyMap("emacs");

            icosrc += "win";
        } else {

            h5cTabletFrame['w0'].editor.setOption("keyMap", mode);
            icosrc += mode;
        }
    }

    $('.lc-editor-editmode img').attr("src", icosrc +"-48.png");

    lcEditor.Config.EditMode = mode;
    lessLocalStorage.Set('editor_editmode', mode);

    if (mode == null) {
        mode = '<?php echo $this->T('Default')?>';
    }
    lessAlert('#en8dfy', 'alert-success', '<?php echo $this->T('Successfully switched to')?> '+ mode);
}

if (lcEditor.Config.EditMode != null) { 
    $(".x7o1tn input[name='cfg_editor_mode'][value='"+lcEditor.Config.EditMode+"']").attr('checked', true);
} else {
    $(".x7o1tn input[name='cfg_editor_mode'][value='win']").attr('checked', true);
}

</script>
