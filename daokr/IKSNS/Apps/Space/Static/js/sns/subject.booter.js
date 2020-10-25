(function(isay) {

  isay.initials.subject = function() {
    if (!isay.root.find('a[data-action=subject]').length) return;
  };
  isay.actions.subject = function() {
    // load subject share js
    Do.add_js(Do.isay_subject_src);
    isay.fieldMsg('waiting', '加载中...');
  };
  isay.action_settings.subject = {
    fieldPos: 'up',
    restartable: true,
    freshInput: true,
    labelText: '对影评的简短评论...',
    buttonText: '分享'
  };

  // debug code
  //setTimeout(function() {
    //console.log($('a[data-action=subject]').click());
  //}, 0);

})(Do.ISay);
