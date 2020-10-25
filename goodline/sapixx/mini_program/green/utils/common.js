/**
 * 验证码倒计
 */
var countdown = 60;
var settime = function (that) {
  if (countdown == 0) {
    that.setData({
      disabled: false,
    })
    return;
  } else {
    that.setData({
      countdown: countdown
    })
    countdown--;
  }
  setTimeout(function () {
    settime(that);
  },1000)
}

/**
 * 计算图片高度
 */
var autoimg = function (event) {
  var windowWidth = parseInt(wx.getSystemInfoSync().windowWidth)-20;  //获取屏幕宽度
  var imgwidth = event.detail.width,imgheight = event.detail.height,ratio = imgwidth / imgheight;
  var imgheight = parseInt(windowWidth / ratio);
  var imgheights = [];
      imgheights.push(imgheight)
  return imgheights
}
exports.settime = settime;
exports.autoimg = autoimg;