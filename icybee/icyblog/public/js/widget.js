(function(theWindow) {
  var Swiftype = theWindow.Swiftype || (theWindow.Swiftype = {});
  Swiftype.root_url = Swiftype.root_url || "//search-api.swiftype.com";
  Swiftype.key = "oJGz2P4RRC4-Kh2RK9Up";
  Swiftype.inputElement = null;
  Swiftype.attachElement = null;
  Swiftype.renderStyle = "tab";
  Swiftype.searchPerPage = 10;
  Swiftype.autocompleteResultLimit = 10;

  // Unset optional configuration that may have been set by the old embed or attempted customization
  Swiftype.resultPageURL = undefined;
  Swiftype.resultContainingElement = null;
  Swiftype.disableAutocomplete = false;




  var executeCommand = theWindow[theWindow["SwiftypeObject"]];

  executeCommand("loadStyleSheet", "//s.swiftypecdn.com/assets/swiftype_nocode-862cc0feac61f00e170fbcc6360aeeb7.css");
  executeCommand("loadScript", "//s.swiftypecdn.com/assets/swiftype_nocode-5190fccc6a3c7dcff96a2c6aad0e53dd.js");

})(window);
