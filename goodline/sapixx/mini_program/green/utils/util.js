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
//判断是否有某个值
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
//随机数
function getRandom(num) {
  return Math.floor((Math.random() + Math.floor(Math.random() * 9 + 1)) * Math.pow(10,num - 1));
}
//16进制4位补0
function stringbty(num,aicode,arr){
  var aicodeString = aicode.toString(16).toUpperCase();
  var len = num-count(aicodeString);
  var zero = '';
  if(len > 0){
    for (let index = 0; index < len; index++) {
      zero += '0';
    }
  }
  var aicode = zero+aicodeString;
  var aicode_len = aicode.length/2;
  var i = 0;
  for (let index = 0; index < aicode_len; index++) {
      arr.push(aicode.substr(i,2))
      i = i+2;
  }
  return arr;
}
//增加不补位
function btyAdd(arr){
  var num = '00';
  for (let i = 0; i <= arr.length; i++) {
    if(parseInt('0x'+arr[i]) > 0){
      num = parseInt('0x'+num.toString(16)) + parseInt('0x'+arr[i]);
      if(num.toString(16).length >= 3){
        num = num.toString(16).substr(-2,2)
      }
    }
  }
  return num.toString(16).toUpperCase();
}
//腾讯地图
function baidutotencent(lng, lat) {
  let x_pi = 3.14159265358979324 * 3000.0 / 180.0;
  let x = lng - 0.0065;
  let y = lat - 0.006;
  let z = Math.sqrt(x * x + y * y) + 0.00002 * Math.sin(y * x_pi);
  let theta = Math.atan2(y, x) + 0.000003 * Math.cos(x * x_pi);
  let lngs = z * Math.cos(theta);
  let lats = z * Math.sin(theta);
  return {
    longitude:lngs,
    latitude:lats
  };
}
//腾讯地图多数据
function bdtotx(lng_lat) {
  var data = [];
  for (let index = 0; index < lng_lat.length; index++) {
    var result = baidutotencent(lng_lat[index].longitude,lng_lat[index].latitude);
    data[index] = lng_lat[index];
    data[index]['iconPath']  = "/img/hot.png";
    data[index]['width']     = 40;
    data[index]['height']    = 41;
    data[index]['longitude'] = result.longitude;
    data[index]['latitude']  = result.latitude;
    data[index]['callout']   = {
      content:"名称:"+lng_lat[index].title+"\r\n"+"地址:"+lng_lat[index].address,
      bgColor:"#fff",padding:"5px",borderRadius:"5px",borderWidth:"1px",borderColor:"#07c160",
    }
  }
  return data;
}
//随机数
module.exports = {
  formatTime: formatTime,
  isNull: isNull,
  isString: isString,
  isArray: isArray,
  isObject: isObject,
  count: count,
  strToArray: strToArray,
  inArray: inArray,
  trim: trim,
  getRandom: getRandom,
  baidutotencent:baidutotencent,
  bdtotx:bdtotx,
  stringbty:stringbty,
  btyAdd:btyAdd,
}