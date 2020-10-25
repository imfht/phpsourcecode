Do._isay = function(){
  @import /js/lib/jquery.iframe-post-form.js
  @import /js/lib/jquery.text-selection.js
  @import ./isay/base.js
  @import ./isay/plugin/mention.js
  @import ./isay/plugin/subject.booter.js
  @import ./isay/plugin/share.js
  @import ./isay/plugin/pic.js
  @import ./isay/plugin/topic.js
};
// fix dependency
if ('add' in Do) Do('common', function() { Do._isay(); });
else Do._isay();
