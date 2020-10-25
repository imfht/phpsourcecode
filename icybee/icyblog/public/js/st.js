(function(theWindow) {
  var Swiftype = theWindow.Swiftype || (theWindow.Swiftype = {});
  Swiftype.embedVersion = Swiftype.embedVersion || 'configurable';
  var commands = {
    install: function(installKey) {
      commands.loadScript("//s.swiftypecdn.com/install/c/widget.js?install=" + installKey);
    },
    loadScript: function(url, callback) {
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.async = true;
      script.src = url;

      var entry = document.getElementsByTagName('script')[0];
      entry.parentNode.insertBefore(script, entry);

      if (callback) {
        if (script.addEventListener) {
          script.addEventListener('load', callback, false);
        } else {
          script.attachEvent('onreadystatechange', function() {
            if (/complete|loaded/.test(script.readyState))
              callback();
          });
        }
      }
    },
    loadStyleSheet: function(url) {
      var link = document.createElement('link');
      link.rel = 'stylesheet';
      link.type = 'text/css';
      link.href = url;
      (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(link);
    }
  };

  function executeCommand() {
    if (arguments.length < 2) {
      return;
    }

    var commandName = arguments[0];
    var commandArgs = Array.prototype.slice.call(arguments, 1);

    if (commands.hasOwnProperty(commandName)) {
      commands[commandName].apply(null, commandArgs);
    } else {
    }
  }

  function drainQueueAndReplaceSwiftypeFunction() {
    var swiftypeObject = theWindow[theWindow['SwiftypeObject']];
    for (var i = 0; i < swiftypeObject.q.length; i++) {
      executeCommand.apply(null, swiftypeObject.q[i]);
    }

    // replace the SwiftypeObject function
    theWindow[theWindow['SwiftypeObject']] = executeCommand;
  }

  if (Swiftype.embedVersion === 'configurable') {
    drainQueueAndReplaceSwiftypeFunction();
  }
})(window);
