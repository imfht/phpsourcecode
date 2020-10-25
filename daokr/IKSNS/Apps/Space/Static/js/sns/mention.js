(function(isay) {
  isay.initials.mention = function() {
    Do.check_js(function() {
      return $.fn.tagsug && window.Mustache;
    }, function() {
      var highlighter = isay.root.find('p.mention-highlighter');
      isay.node_textarea.tagsug({
        url: 'https://api.douban.com/shuo/in/complete?alt=xd&count={max}&callback=?&word=',
        highlight: true,
        highlighter: highlighter
      });
      var reg_blank = /&nbsp;/g;
      isay.node_textarea.parents('form').submit(function(e) {
        var textarea_clone = isay.textarea_clone;
        var end_val = textarea_clone.val();
        var mentions = highlighter.find('code');
        mentions.each(function(i, item) {
          end_val = end_val.replace($(item).text(), '@' + item.getAttribute('data-id'));
        });
        //isay.node_textarea.val(end_val);
        textarea_clone.val(end_val);
      });
    });
  };
})(Do.ISay);
