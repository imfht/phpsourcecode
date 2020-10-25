(function(isay) {
  var node_upload, node_upload_inp,
  MAX_IMAGE_SIZE = 3000000,
  cls_acting = 'acting';

  // to enable upload same picture again and again..
  function replace_inp() {
    node_upload_inp.replaceWith(node_upload_inp.clone(false));
    node_upload_inp = $('#isay-upload-inp');
    node_upload_inp.change(function() {
      if (this.value !== '') {
        node_upload.submit();
        node_upload[0].submit();
        replace_inp();
      }
    });
  }

  isay.initials.pic = function() {
    //var node_upload = $($('#tmpl-isay-upload').html()).appendTo(isay.root);
    node_upload = $('#isay-upload');
    node_upload_inp = $('#isay-upload-inp');
    node_upload.iframePostForm({
      json: true,
      // start upload
      post: function() {
        isay.fieldMsg('waiting', '正在上传...');
      },
      complete: function(data) {
        node_upload_inp.val('');
        var url = data && data.url;
        if (!url) {
          set_msg(data.msg || '上传失败');
          return;
        }
        isay.setField('<div class="waiting" style="padding-left:0;"><img src="{src}"></div><input type="hidden" name="uploaded" value="{src}">'.replace(/{src}/g, url));
        isay.done();
        isay.validate();
      }
    });
    node_upload_inp.change(function() {
      var files = this.files;
      if (files && files.length) {
        var file = files[0];
        // 3M
        if (file.size > MAX_IMAGE_SIZE) {
          set_msg('图片超过3M');
          return;
        }
      }
      if (this.value !== '') {
        node_upload.submit();
        node_upload[0].submit();
        isay.node_textarea.focus();
        replace_inp();
      }
    });

    function set_msg(msg) {
      isay.fieldMsg('error', msg + '，<a href="javascript:;" data-action="pic">点此重试</a>');
      node_upload.css({
        left: msg.length + 1.7 + 'em',
        top: -40 + 'px'
      });
    }
  };
  isay.actions.pic = function(elem) {
    var self = isay;
    isay.hideField();
    node_upload.css({ left: '', top: '' });
    if (elem && elem.id == 'isay-upload-inp') {
     if (elem.value) {
        node_upload.submit();
        node_upload[0].submit();
        replace_inp();
      }
    } else {
      node_upload_inp.click();
    }
  };

  isay.action_settings.pic = {
    oncancel: function() {
      $('#iframe-post-form').unbind('load').attr('src', 'javascript:void(0);');

      var xhr = isay.imageUploadXHR;
      if (xhr && xhr.abort) {
        xhr.abort();
        isay.imageUploadXHR = null;
      }
      // remove uploaded pic
      $('input[name=uploaded]', isay.root).remove();
    }
  };
})(Do.ISay);

// @import "./pic.upload_datauri.js";
// @import "./pic.paste.js";
// @import "./pic.dragdrop.js";
