const formatTime = date => {
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()
  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}
const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}
//自动判断类型并判断类型是否为空
function isNull(value) {
  if (value == null || value == undefined) return true
  if (this.isString(value)) {
    if (value.trim().length == 0) return true
  } else if (this.isArray(value)) {
    if (value.length == 0) return true
  } else if (this.isObject(value)) {
    for (let name in value) return false
    return true
  }
  return false;
}
//判断字符串是否空
function isString(value) {
  return value != null && value != undefined && value.constructor == String
}
//判断数组是否空
function isArray(value) {
  return value != null && value != undefined && value.constructor == Array
}
//判断对象是否空
function isObject(value) {
  return value != null && value != undefined && value.constructor == Object
}
//精确的乘法结果
function accMul(arg1, arg2) {
  var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
  try { m += s1.split(".")[1].length } catch (e) { }
  try { m += s2.split(".")[1].length } catch (e) { }
  return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
} 
//统计长度和数量
function count(obj) {
  var objType = typeof obj;
  if (objType == "string") {
    return obj.length;
  } else if (objType == "object") {
    var objLen = 0;
    for (var i in obj) {
      objLen++;
    }
    return objLen;
  }
  return false;
}
//删除空数组
function clearArray(array) {
  for (var i = 0; i < array.length; i++) {
    if (array[i] == "" || typeof (array[i]) == "undefined") {
      array.splice(i, 1);
      i = i - 1;
    }
  }
  return array;
}
//Canvas文字换行
function breakTextCanvas(context, text, width, font) {
  var result = [];
  var textArray = text.split('\r\n');
  for (let i = 0; i < textArray.length; i++) {
    let item = textArray[i];
    var breakPoint = 0;
    while ((breakPoint = findBreakPoint(item, width, context)) !== -1) {
      result.push(item.substr(0, breakPoint));
      item = item.substr(breakPoint);
    }
    if (item) {
      result.push(item);
    }
  }
  return result;
}
function findBreakPoint(text, width, context) {
  var min = 0;
  var max = text.length - 1;
  while (min <= max) {
    var middle = Math.floor((min + max) / 2);
    var middleWidth = context.measureText(text.substr(0, middle)).width;
    var oneCharWiderThanMiddleWidth = context.measureText(text.substr(0, middle + 1)).width;
    if (middleWidth <= width && oneCharWiderThanMiddleWidth > width) {
      return middle;
    }
    if (middleWidth < width) {
      min = middle + 1;
    } else {
      max = middle - 1;
    }
  }
  return -1;
}
//get参数转换数组
function strToArray(str) {
  var arr = str.split('&');
  var newArray = new Object();
  for (let i in arr) {
    var kye = arr[i].split("=")[0]
    var value = arr[i].split("=")[1]
    newArray[kye] = value
  }
  return newArray;
}
/**
 * 判断是否有某个值
 */
function inArray(arr, value) {
  if (arr.indexOf && typeof (arr.indexOf) == 'function') {
    var index = arr.indexOf(value);
    if (index >= 0) {
      return true;
    }
  }
  return false;
}
//去除字符串左右两端的空格
function trim(str) {
  var str = str.toString();
  return str.replace(/(^\s*)|(\s*$)/g, "");
}
module.exports = {
  formatTime: formatTime,
  isNull: isNull,
  isString: isString,
  isArray: isArray,
  isObject: isObject,
  count: count,
  accMul: accMul,
  clearArray: clearArray,
  breakTextCanvas: breakTextCanvas,
  strToArray: strToArray,
  inArray: inArray,
  trim: trim,
}