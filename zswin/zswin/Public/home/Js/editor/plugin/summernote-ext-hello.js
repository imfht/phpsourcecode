(function ($) {
  // template, editor
  var tmpl = $.summernote.renderer.getTemplate();
  var editor = $.summernote.eventHandler.getEditor();

  // add plugin
  $.summernote.addPlugin({
    name: '代码高亮', // name of plugin
    buttons: { // buttons
      hello: function () {

        return tmpl.iconButton('icon-lightbulb', {
          event : 'hello',
          title: '代码高亮',
          hide: true
        });
      },
      code: function () {

          return tmpl.iconButton('icon-code-fork', {
            event : 'code',
            title: '内联代码',
            hide: true
          });
        },
      helloDropdown: function () {


        var list = '<li><a data-event="helloDropdown" href="#" data-value="插入高亮代码">插入高亮代码</a></li>';
      
        var dropdown = '<ul class="dropdown-menu">' + list + '</ul>';

        return tmpl.iconButton('icon-lightbulb', {
          title: '代码高亮',
          hide: true,
          dropdown : dropdown
        });
      }

    },

    events: { // events
      hello: function (layoutInfo) {
        // Get current editable node
        var $editable = layoutInfo.editable();

        // Call insertText with 'hello'
        $editable.append('<pre><code data-language="php" class="rainbow"> 在这里插入高亮代码</code></pre><br>');
      },
      code: function (layoutInfo) {
          // Get current editable node
          var $editable = layoutInfo.editable();

          // Call insertText with 'hello'
          $editable.append('<p><code>插入内联代码</code></p><br>');
        },
      helloDropdown: function (layoutInfo, value) {
        // Get current editable node
        var $editable = layoutInfo.editable();

        // Call insertText with 'hello'
$editable.append('<pre><code data-language="php"> ' + value + '</code></pre>');
      }
    }
  });
})(jQuery);
