(function(isay) {
  //var is_intopic = false;

  isay.actions.topic = function() {
    var node_textarea = isay.node_textarea;

    var t = node_textarea[0],
    len = t.value.length;

    t.focus();

    var val = t.value,
    selection = node_textarea.get_selection(),
    sel = selection.text;
    start = selection.start,
    end = selection.end;

    if (val.charAt(start - 1) == '#' && val.charAt(end) == '#') return isay.done();

    var rep = '#' + (sel || '话题') + '#';
    t.value = val.substring(0, start) + rep + val.substring(end, len);
    if (sel === '') {
      node_textarea.set_selection(start + 1, start + 3);
    }

    isay.done();
  };

  isay.initials.topic = function() {
    var node_textarea = isay.node_textarea;
    var t = node_textarea[0];

    // when press tab, goto the very end
    //node_textarea.bind('keydown', function(e) {
      //if (is_intopic && e.keyCode == 9) {
        //var end = t.value.length;
        //t.value += ' ';
        ////t.setSelectionRange && t.setSelectionRange(end, end);
        //e.preventDefault();
      //}
    //});
    if (document.selection) {
      node_textarea.bind('keyup mousedown mouseup focus', function(e) {
        this._saved_range = document.selection.createRange();
      });
    }
  };

  isay.action_settings.topic = {
    exclusive: false
  };
})(IK.ISay);
