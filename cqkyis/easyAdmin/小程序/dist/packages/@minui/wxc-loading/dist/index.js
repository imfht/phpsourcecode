'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = Component({
  _timer: null,

  behaviors: [],
  properties: {
    isShow: {
      type: Boolean,
      value: false,
      observer: function observer(isShow) {
        if (isShow) {
          if (!getApp().globalData) {
            Object.assign(getApp(), { globalData: {} });
          }
          var globalData = getApp().globalData;
          var zIndex = (globalData._zIndex || 1000) + 1;
          globalData._zIndex = zIndex;
          this.setData({
            zIndex: zIndex
          });
        }
      }
    },
    type: {
      type: String,
      value: 'mgj'
    },
    image: {
      type: String,
      value: ''
    },
    slip: {
      type: String,
      value: ''
    }
  },
  data: {
    zIndex: 1000
  },
  methods: {
    show: function show() {
      var _this = this;

      if (this._timer) {
        clearTimeout(this._timer);
      }
      this._timer = setTimeout(function () {
        _this._timer = null;
        _this.setData({ isShow: true });
      }, 500);
    },
    hide: function hide() {
      if (this._timer) {
        clearTimeout(this._timer);
        this._timer = null;
      }
      this.setData({ isShow: false });
    }
  }
});
