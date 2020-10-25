(function() {
  var max = 140,
  fnd_dlg = '#dialog',
  fnd_textarea = 'textarea',
  fnd_rec = '.msg a',
  fnd_counter = '#isay-counter',
  fnd_bn_section = '.btn',
  fnd_bn_group = '.btn-group',
  fnd_cta = '.btn .bn-flat',
  fnd_field = '#isay-act-field',
  cls_disable = 'isay-disable',
  cls_processing = 'isay-processing',
  cls_processed = 'isay-processed',
  cls_active = 'active',
  cls_focus = 'focus',
  cls_acting = 'acting',
  cls_error = 'error',
  cls_field_up = 'field-up',
  textarea_clone;

  var tmpl_field = '<div class="field">{hd}<div class="bd">{bd}</div>' +
    '<a href="javascript:void(0);" class="bn-x isay-cancel">&times;</a>' +
    '</div>';

  var node_root,
  node_textarea,
  node_label,
  node_submit,
  node_field,
  node_errorhighlight;

  function makeField(bd, hd) {
    return tmpl_field.replace('{bd}', bd).replace('{hd}', (hd || ''));
  }

  function defaults(a, b) {
    return $.extend({}, b, a);
  }

  var default_settings = {
    fieldPos: 'down',
    labelText: '来分享吧...',
    exclusive: true, // whether to cancel other before act
    freshInput: false, // whether to clear textarea before act
    restartable: false, // could restart or not when cancel
    buttonText: '我说'
  };

  // {{{
  var exports = IK.ISay = {
    // 执行动作
    actions: {},
    // 动作初始化事件绑定
    initials: {},
    // 动作的通用配置项
    action_settings: {},
    // set action field
    setField: function(bd, hd) {
      return node_root.addClass(cls_acting).find(fnd_field)
      .html(makeField(bd, hd));
    },
    moveField: function(pos) {
      var field = node_root.find(fnd_field);
      var ref = field.siblings('.item');
      // move action field
      if (pos === 'up') {
        field.insertBefore(ref);
        node_root.addClass(cls_field_up);
      } else {
        field.insertAfter(ref);
        node_root.removeClass(cls_field_up);
      }
    },
    hideField: function() {
      return node_root.removeClass(cls_acting);
    },
    fieldMsg: function(cls, msg) {
      return this.setField('<div class="' + cls + '">' + msg + '</div>');
    },
    doAct: function(act, elem) {
      var self = this;
      var actions = self.actions;

      self._acting_elem = elem;

      if (act && act in actions) {
        var settings = defaults(self.action_settings[act], default_settings);

        self._upcoming_act = act;

        if (self._acting && self._acting != act && settings.exclusive) {
          self.cancel(true);
        }

        node_root.addClass(cls_active);
        node_root.addClass('act-' + act);

        // move field to according position
        self.moveField(settings.fieldPos);

        // set lable text
        self.updateText(settings);

        self._acted = false;
        self._acting = act;

        actions[act](elem);
      }
    },
    updateText: function(settings) {
      node_label.text(settings.labelText);
      node_submit.val(settings.buttonText);
    },
    done: function(act) {
      this._acted = true;
      //delete this._acting;
    },
    // cancel action
    cancel: function(from_do) {
      var self = this;

      var act = self._acting;
      var settings = defaults(self.action_settings[act], default_settings);

      if (settings.oncancel) {
        var ret = settings.oncancel();
        if (ret === false) return;
      }
      node_root.removeClass(cls_acting);
      node_root.removeClass('act-' + act);

      delete self._acting;

      if (settings.freshInput) {
        node_textarea.val('').blur();
      } else {
        // reset validation field
        validate();
      }

      if (!from_do && settings.restartable) {
        self.doAct(act);
      }

    },
    // close the whole
    close: function() {
      this.cancel();
      node_textarea[0]._closed = true;
      node_textarea.height('').val('');
      node_label.show();
      node_root.removeClass(cls_active);
      $(fnd_counter).html('');
    },
    // check content height and validation
    check: check,
    validate: validate,
    enable: enable_this,
    disable: disable_this,
    init: function(trigger, etype) {
      var self = this;
      var root = $('#db-isay');
      var elem = root[0];

      if (!elem || elem._inited) return;
      elem._inited = true;

      self.root = node_root = root;
      self.node_label = node_label = root.find('label'),
      self.node_textarea = node_textarea = root.find(fnd_textarea),
      self.node_submit = node_submit = root.find(':submit');
      self.node_errorhighlight = node_errorhighlight = root.find('p.error-highlighter');
      self.node_field = node_field = root.find(fnd_field);

      // init plugins
      for (var i in this.initials) {
        this.initials[i](trigger, etype);
      }

      // make clone
      self.textarea_clone = textarea_clone = node_textarea.clone().addClass('abs-out').attr('tabindex', '-1');
      node_textarea.removeAttr('name');
      textarea_clone.attr('id', 'isay-cont-clone').appendTo(node_textarea.parent());

      check();

      // disable submit button
      root.find('form').submit(function(e) {
        validate();
        if (root.hasClass(cls_disable) ||
        (exports._acting && !exports._acted)) {
          e.preventDefault();
          return;
        }
        disable_this();
      });

      // 输入时的事件
      node_textarea.bind('keyup input', check).focus(function() {
        node_label.hide();
        node_textarea[0]._closed = false;
        root.addClass(cls_active).addClass(cls_focus);
      }).blur(function(e) {
        root.removeClass(cls_focus);
        check(e);
      }).focus();

      //var init_val = $.trim(node_textarea.val());

      //if (init_val) {
        //root.removeClass(cls_disable).addClass(cls_active);
      //}

      if (trigger) do_action(trigger);

      root.delegate('.isay-close', 'click', function(e) {
        exports.close();
        return false;
      }).delegate('.isay-cancel', 'click', function(e) {
        // cancel act
        exports.cancel();
        e.preventDefault();
      }).delegate('a', 'click', function(e) {
        var elem = e.currentTarget;
        var href = elem.getAttribute('href');
        if (href === '#' || href === 'javascript:;' || !href) e.preventDefault();
        do_action(elem);
      }).delegate('label', 'click', function(e) {
        var t = $(e.currentTarget).attr('for');
        var elem = t && document.getElementById(t);
        elem && elem.focus && elem.focus();
      });
    }
  };
  //}}}

  // main action
  exports.actions.main = function() {
    exports.cancel();
    node_textarea.focus();
  };

  function enable_this() {
    if (exports.acting && !exports._acted) return;
    node_root.removeClass(cls_disable);
    node_submit.removeAttr('disabled');
  }
  function disable_this() {
    node_root.addClass(cls_disable);
    node_submit.attr('disabled', true);
  }

  var reg_space = /\s/g,
  reg_lt = /</g,
  reg_gt = />/g,
  ie_old = $.browser.msie && $.browser.version < 9;

  // 根据字数统计和动作完成情况，判断是否可发布信息
  // 更新错误消息
  function validate(val) {
    if (typeof val == 'undefined') val = get_val();
    //var n = Math.ceil(blength(val) / 2);
    var n = val.replace(/(^|[^"'(=])((http|https)\:\/\/[\.\-\_\/a-zA-Z0-9\~\?\%\#\=\@\:\&\;\*\+]+\b[\?\#\/\*]*)/ig, 'http://dou.bz/XXXXXX').length;
    var is_acting = '_acting' in exports;
    var is_acting_processed = exports._acted === true;
    var msg = $(fnd_counter);
    if (n > max || (!is_acting && n < 1) ||
    (is_acting && !is_acting_processed)) {
      disable_this();
    } else {
      enable_this();
    }

    // 字数超出
    var tleft = max - n;
    if (tleft < 0) {
      msg.html('<strong>' + tleft + '</strong>');
      var high_txt = val.substring(max).replace(reg_lt, '&lt;').replace(reg_gt, '&gt;');
      if (ie_old) {
        high_txt = high_txt.replace(reg_space, '&nbsp;');
      }
      high_txt = '<code>' + high_txt + '</code>';
      node_errorhighlight
        .html(val.substring(0, max) + high_txt)
        .css('marginTop', -node_textarea.scrollTop);
    } else {
      node_errorhighlight.html('');
      if (tleft < 11) {
        msg.html(tleft);
      } else {
        msg.html('');
      }
    }
  }

  // 检查输入内容的长度 调整输入框高度
  function check(e) {
    var inp = node_textarea, h;
    var val = get_val(e && e.target);
    var min_height = inp.attr('data-minheight') || 36;
    var scrollDelta;

    validate(val);

    if (val || node_root.hasClass(cls_focus)) {
      node_label.hide();
    } else {
      node_label.show();
    }
    h = textarea_clone.val(val).height(0).scrollTop(10000).scrollTop();
    inp.css('height', Math.min(Math.max(h, min_height), 250));
    if ((scrollDelta = inp.scrollTop()) > 0) {
      node_errorhighlight.css('top', -scrollDelta + 'px');
    } else {
      node_errorhighlight.css('top', '');
    }
  }

  function get_val(elem) {
    var val = elem ? elem.value : node_textarea[0].value;
    return val ? $.trim(val) : '';
  }

  //function blength(val) {
    //if (typeof val != 'string') {
      //val = (val && val.toString()) || '';
    //}
    //return val.replace(/\n\r/g, '\n').replace(/[^\x00-\xff]/g, 'xx').length;
  //}

  function do_action(elem) {
    var act = elem.getAttribute('data-action');
    act && exports.doAct(act, elem);
  }

})();
