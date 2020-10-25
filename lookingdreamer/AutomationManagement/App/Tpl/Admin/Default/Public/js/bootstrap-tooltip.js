!function ($) {

  "use strict"; // jshint ;_;


/* TOOLTIP PUBLIC CLASS DEFINITION 
  * =============================== */

// 定义一个名字为Tooltip的类，element 为传入的HTML节点元素,options为显示效果的选项（代码最后有介绍） 
  var Tooltip = function (element, options) { 

    this.init('tooltip', element, options) 
  }

  Tooltip.prototype = {

    constructor: Tooltip

  , init: function (type, element, options) { 
      var eventIn 
        , eventOut

      this.type = type  /* 将’tooltip’赋值给type变量 */ 
      this.$element = $(element) /* 将DOM节点对应的JQuery对象赋值给$element变量 */ 
      this.options = this.getOptions(options) /* 得到参数 */ 
      this.enabled = true

      if (this.options.trigger == 'click') { /* 如果触发条件是单击 */ 
        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this)) /* 将toggle函数设定为单击事件处理函数 */ 
      } else if (this.options.trigger != 'manual') { /* 如果触发事件不是由自己手动指定，比如默认选项里的‘hover’ */ 
        eventIn = this.options.trigger == 'hover' ? 'mouseenter' : 'focus' // eventIn为进入事件 
        eventOut = this.options.trigger == 'hover' ? 'mouseleave' : 'blur' // eventOut为离开事件

        /* 将enter函数设定为mouseenter事件的处理函数 */ 
        this.$element.on(eventIn + '.' + this.type, this.options.selector, $.proxy(this.enter, this))

        /* 将leave函数设定为mouseleave事件的处理函数 */ 
        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this)) 
      }

      /* 如果选项里有CSS选择符 */

      this.options.selector ? 
        (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) : 
        this.fixTitle() 
    }

  , getOptions: function (options) { 
      options = $.extend({}, $.fn[this.type].defaults, options, this.$element.data())

      if (options.delay && typeof options.delay == 'number') { 
        options.delay = { 
          show: options.delay 
        , hide: options.delay 
        } 
      }

      return options 
    }

  , enter: function (e) { 
      var self = $(e.currentTarget)[this.type](this._options).data(this.type)

      if (!self.options.delay || !self.options.delay.show) return self.show()

      clearTimeout(this.timeout) 
      self.hoverState = 'in' 
      this.timeout = setTimeout(function() { 
        if (self.hoverState == 'in') self.show() 
      }, self.options.delay.show) 
    }

  , leave: function (e) { 
      var self = $(e.currentTarget)[this.type](this._options).data(this.type)

      if (this.timeout) clearTimeout(this.timeout) 
      if (!self.options.delay || !self.options.delay.hide) return self.hide()

      self.hoverState = 'out' 
      this.timeout = setTimeout(function() { 
        if (self.hoverState == 'out') self.hide() 
      }, self.options.delay.hide) 
    }

  , show: function () { /* 显示tip */ 
      var $tip 
        , inside 
        , pos 
        , actualWidth 
        , actualHeight 
        , placement 
        , tp

      if (this.hasContent() && this.enabled) { 
        $tip = this.tip() 
        this.setContent()

        if (this.options.animation) { 
          $tip.addClass('fade') 
        }

        placement = typeof this.options.placement == 'function' ? 
          this.options.placement.call(this, $tip[0], this.$element[0]) : 
          this.options.placement

        inside = /in/.test(placement)

        $tip 
          .detach() 
          .css({ top: 0, left: 0, display: 'block' }) 
          .insertAfter(this.$element)

        pos = this.getPosition(inside)

        actualWidth = $tip[0].offsetWidth 
        actualHeight = $tip[0].offsetHeight

        switch (inside ? placement.split(' ')[1] : placement) { 
          case 'bottom': 
            tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2} 
            break 
          case 'top': 
            tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2} 
            break 
          case 'left': 
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth} 
            break 
          case 'right': 
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width} 
            break 
        }

        $tip 
          .offset(tp) 
          .addClass(placement) 
          .addClass('in') 
      } 
    }

  , setContent: function () { 
      var $tip = this.tip() 
        , title = this.getTitle()

      $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title) 
      $tip.removeClass('fade in top bottom left right') 
    }

  , hide: function () { 
      var that = this 
        , $tip = this.tip()

      $tip.removeClass('in')

      function removeWithAnimation() { 
        var timeout = setTimeout(function () { 
          $tip.off($.support.transition.end).detach() 
        }, 500)

        $tip.one($.support.transition.end, function () { 
          clearTimeout(timeout) 
          $tip.detach() 
        }) 
      }

      $.support.transition && this.$tip.hasClass('fade') ? 
        removeWithAnimation() : 
        $tip.detach()

      return this 
    }

  , fixTitle: function () { 
      var $e = this.$element /* 将DOM节点对应的JQuery对象赋值给变量$e */ 
      if ($e.attr('title') || typeof($e.attr('data-original-title')) != 'string') { 
        $e.attr('data-original-title', $e.attr('title') || '').removeAttr('title') 
      } 
    }

  , hasContent: function () { 
      return this.getTitle() 
    }

  , getPosition: function (inside) { 
      return $.extend({}, (inside ? {top: 0, left: 0} : this.$element.offset()), { 
        width: this.$element[0].offsetWidth 
      , height: this.$element[0].offsetHeight 
      }) 
    }

  , getTitle: function () { 
      var title 
        , $e = this.$element 
        , o = this.options

      title = $e.attr('data-original-title') 
        || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

      return title 
    }

  , tip: function () { 
      return this.$tip = this.$tip || $(this.options.template) 
    }

  , validate: function () { 
      if (!this.$element[0].parentNode) { 
        this.hide() 
        this.$element = null 
        this.options = null 
      } 
    }

  , enable: function () { 
      this.enabled = true 
    }

  , disable: function () { 
      this.enabled = false 
    }

  , toggleEnabled: function () { 
      this.enabled = !this.enabled 
    }

  , toggle: function (e) { 
      var self = $(e.currentTarget)[this.type](this._options).data(this.type) 
      self[self.tip().hasClass('in') ? 'hide' : 'show']() 
    }

  , destroy: function () { 
      this.hide().$element.off('.' + this.type).removeData(this.type) 
    }

  }


/* TOOLTIP PLUGIN DEFINITION 
  * ========================= */

  $.fn.tooltip = function ( option ) { 
    return this.each(function () { 
      var $this = $(this) /* 将节点的JQuery对象赋值给变量$this */ 
        , data = $this.data('tooltip') /* 取出JQuery对象中key值为’tooltip’的value值 */ 
        , options = typeof option == 'object' && option /* 取得调用函数时的选项值 */ 
      if (!data) $this.data('tooltip', (data = new Tooltip(this, options))) /* 如果对象字典里没有tooltip 作为key的项，则新建一个Tooltip对象 */ 
      if (typeof option == 'string') data[option]() /* 调用相应的成员函数，用于: ‘show’,’hide’,’toggle’做参数的调用 */ 
    }) 
  }

  $.fn.tooltip.Constructor = Tooltip  /* 构造函数 */

  $.fn.tooltip.defaults = { /* 默认参数 ， fn就是prototype */ 
    animation: true 
  , placement: 'top' 
  , selector: false 
  , template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>' 
  , trigger: 'hover' 
  , title: '' 
  , delay: 0 
  , html: false 
  }

}(window.jQuery);