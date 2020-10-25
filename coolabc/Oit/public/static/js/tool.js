// 判断当前参数是否是空值
var isNull = function (str) {
    return (!str || str == "" || str == "null" || str == "undefined" || str.length == 0 || JSON.stringify(str) == "{}");
};

// 返回某个对象的键值数组
var get_obj_key = function (obj) {
    var arr = [];
    for (var p in obj) {
        if (obj.hasOwnProperty(p)) {
            arr.push(p);
        }
    }
    return arr;
};

// js替换对象
var extendObj = function (target, source) {
    for (var p in source) {
        if (source.hasOwnProperty(p)) {
            target[p] = source[p];
        }
    }

    return target;
};


// 日期
//日期格式化
//示例：
//alert(new Date().Format("yyyy年MM月dd日"));
//alert(new Date().Format("MM/dd/yyyy"));
//alert(new Date().Format("yyyyMMdd"));
//alert(new Date().Format("yyyy-MM-dd hh:mm:ss"));
Date.prototype.format = function (format) {
    var o = {
        "M+": this.getMonth() + 1, //month
        "d+": this.getDate(), //day
        "h+": this.getHours(), //hour
        "m+": this.getMinutes(), //minute
        "s+": this.getSeconds(), //second
        "q+": Math.floor((this.getMonth() + 3) / 3), //quarter
        "S": this.getMilliseconds() //millisecond
    };

    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }

    for (var k in o) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return format;
};


// 当前月份第一天
Date.prototype.get_curr_mon_beg_day = function (format) {
    var date = new Date();
    date.setDate(1);
    return date;
};

// 当前月份最后一天
Date.prototype.get_curr_mon_end_day = function (format) {
    var date = new Date();
    var currentMonth = date.getMonth();
    var nextMonth = ++currentMonth;
    var nextMonthFirstDay = new Date(date.getFullYear(), nextMonth, 1);
    var oneDay = 1000 * 60 * 60 * 24;
    return new Date(nextMonthFirstDay - oneDay);
};

// 增加天数
Date.prototype.addDay = function (num) {
    this.setDate(this.getDate() + num);
    return this;
};

// 增加月份
Date.prototype.addMonth = function (num) {
    var tempDate = this.getDate();
    this.setMonth(this.getMonth() + num);
    if (tempDate != this.getDate())
        this.setDate(0);
    return this;
};

// 增加年份
Date.prototype.addYear = function (num) {
    var tempDate = this.getDate();
    this.setYear(this.getYear() + num);
    if (tempDate != this.getDate())
        this.setDate(0);
    return this;
};


// 省略字符串
String.prototype.part = function (str, number, count) {
    if (str.length > number) {
        return str = str.substring(0, count) + "....";
    }
};

var base64_keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

// 字符转数组
String.prototype.ToCharArray = function () {
    return this.split("");
};

// 倒序
String.prototype.Reverse = function () {
    return this.split("").reverse().join("");
};

// 是否包含指定字符
String.prototype.IsContains = function (str) {
    return (this.indexOf(str) > -1);
};

// 判断是否为空
String.prototype.IsEmpty = function () {
    return this == "";
};

// 判断是否是数字
String.prototype.IsNumeric = function () {
    var tmpFloat = parseFloat(this);
    if (isNaN(tmpFloat))
        return false;
    return true;
};

// 判断是否是整数
String.prototype.IsInt = function () {
    if (this == "NaN")
        return false;
    return this == parseInt(this).toString();
};

// 合并多个空白为一个空白
String.prototype.resetBlank = function () {
    return this.replace(/s+/g, "");
};

// 保留数字
String.prototype.getNum = function () {
    return this.replace(/[^d]/g, "");
};

// 保留字母
String.prototype.getEn = function () {
    return this.replace(/[^A-Za-z]/g, "");
};

// 保留中文
String.prototype.getCn = function () {
    return this.replace(/[^u4e00-u9fa5uf900-ufa2d]/g, "");
};

// 获取字节长度
String.prototype.ByteLength = function () {
    return this.replace(/[^\x00-\xff]/g, "aa").length;
};

// 从左截取指定长度的字串
String.prototype.left = function (n) {
    return this.slice(0, n);
};

// 从右截取指定长度的字串
String.prototype.right = function (n) {
    return this.slice(this.length - n);
};

// HTML编码
String.prototype.HTMLEncode = function () {
    var re = this;
    var q1 = [/x26/g, /x3C/g, /x3E/g, /x20/g];
    var q2 = ["&", "<", ">", " "];
    for (var i = 0; i < q1.length; i++)
        re = re.replace(q1[i], q2[i]);
    return re;
};

// 获取Unicode
String.prototype.Unicode = function () {
    var tmpArr = [];
    for (var i = 0; i < this.length; i++)
        tmpArr.push("&#" + this.charCodeAt(i) + ";");
    return tmpArr.join("");
};

// 指定位置插入字符串
String.prototype.Insert = function (index, str) {
    return this.substring(0, index) + str + this.substr(index);
};

/**
 * 判断字符串是否以指定的字符串开始
 */
String.prototype.startsWith = function (str) {
    return this.substr(0, str.length) == str;
};

/**
 * 判断字符串是否以指定的字符串开始，忽略大小写
 */
String.prototype.iStartsWith = function (str) {
    return this.substr(0, str.length).iEquals(str);
};

/**
 * 判断字符串是否以指定的字符串结束
 */
String.prototype.endsWith = function (str) {
    return this.substr(this.length - str.length) == str;
};

/**
 * 判断字符串是否以指定的字符串结束，忽略大小写
 */
String.prototype.iEndsWith = function (str) {
    return this.substr(this.length - str.length).iEquals(str);
};

/**
 * 忽略大小写比较字符串 注：不忽略大小写比较用 == 号
 */
String.prototype.iEquals = function (str) {
    return this.toLowerCase() == str.toLowerCase();
};

/**
 * 比较字符串，根据结果返回 -1, 0, 1
 */
String.prototype.compareTo = function (str) {
    if (this == str) {
        return 0;
    } else if (this < str) {
        return -1;
    } else {
        return 1;
    }
};

/**
 * 忽略大小写比较字符串，根据结果返回 -1, 0, 1
 */
String.prototype.iCompareTo = function (str) {
    return this.toLowerCase().compareTo(str.toLowerCase());
};

// 加码
String.prototype.encode64 = function () {
    var output = "";
    var chr1, chr2, chr3 = "";
    var enc1, enc2, enc3, enc4 = "";
    var i = 0;
    var input = this.toString();
    do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
            enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
            enc4 = 64;
        }

        output = output + base64_keyStr.charAt(enc1) + base64_keyStr.charAt(enc2) + base64_keyStr.charAt(enc3) + base64_keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
    } while (i < input.length);

    return output;
};

// 解码
String.prototype.decode64 = function (input) {
    var output = "";
    var chr1, chr2, chr3 = "";
    var enc1, enc2, enc3, enc4 = "";
    var i = 0;

    if (input.length % 4 != 0) {
        return "";
    }
    var base64test = /[^A-Za-z0-9\+\/\=]/g;
    if (base64test.exec(input)) {
        return "";
    }

    do {
        enc1 = base64_keyStr.indexOf(input.charAt(i++));
        enc2 = base64_keyStr.indexOf(input.charAt(i++));
        enc3 = base64_keyStr.indexOf(input.charAt(i++));
        enc4 = base64_keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
            output += String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
            output += String.fromCharCode(chr3);
        }

        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";

    } while (i < input.length);

    return output;
};

// 替换所有
String.prototype.replaceAll = function (reallyDo, replaceWith, ignoreCase) {
    if (!RegExp.prototype.isPrototypeOf(reallyDo)) {
        return this.replace(new RegExp(reallyDo, (ignoreCase ? "gi" : "g")), replaceWith);
    } else {
        return this.replace(reallyDo, replaceWith);
    }
};

// 2017-07-08 转成2017/07/08
// 小于等于
function dateLe(startDate, endDate) {
    if (startDate && endDate) {
        startDate = startDate.replace(/-/g, "/");
        endDate = endDate.replace(/-/g, "/");
        var dt1 = new Date(Date.parse(startDate));
        var dt2 = new Date(Date.parse(endDate));
        return (dt1 <= dt2);
    } else {
        return false;
    }
};

// start 小于 end
function dateLt(startDate, endDate) {
    if (startDate && endDate) {
        startDate = startDate.replace(/-/g, "/");
        endDate = endDate.replace(/-/g, "/");
        var dt1 = new Date(Date.parse(startDate));
        var dt2 = new Date(Date.parse(endDate));
        return (dt1 < dt2);
    } else {
        return false;
    }
};

// start 大于 end
function dateGt(startDate, endDate) {
    if (startDate && endDate) {
        startDate = startDate.replace(/-/g, "/");
        endDate = endDate.replace(/-/g, "/");
        var dt1 = new Date(Date.parse(startDate));
        var dt2 = new Date(Date.parse(endDate));
        return (dt1 > dt2);
    } else {
        return false;
    }
};

// 大于等于
function dateGe(startDate, endDate) {
    if (startDate && endDate) {
        startDate = startDate.replace(/-/g, "/");
        endDate = endDate.replace(/-/g, "/");
        var dt1 = new Date(Date.parse(startDate));
        var dt2 = new Date(Date.parse(endDate));
        return (dt1 >= dt2);
    } else {
        return false;
    }
};

// 得到本地链接
function getLocationHref() {
    return document.location.href;
};

// 求字符的长度，是字母时，+1
// 不是字母时 +2
function checkLength(strTemp) {
    var i, sum;
    sum = 0;
    for (i = 0; i < strTemp.length; i++) {
        if ((strTemp.charCodeAt(i) >= 0) && (strTemp.charCodeAt(i) <= 255)) {
            sum = sum + 1;
        } else {
            sum = sum + 2;
        }
    }
    return sum;
}

// 区得范围内的随机数
function getRandomNumber(min, max) {
    var range = max - min;
    var rand = Math.random();
    return (min + Math.round(rand * range));
}

// 判断是否是json 数组
function isJson(obj) {
    var isjson = typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;
    return isjson;
}

// 字符转 json
function strToJson(str) {
    var json = eval('(' + str + ')');
    return json;
}

// 开始日期是否大于结束日期
function checkDate(startDate, endDate) {
    var sDate = new Date(startDate.replace(/\-/g, "\/"));
    var eDate = new Date(endDate.replace(/\-/g, "\/"));
    return (sDate <= eDate);
}

function parseURL() {
    var url = document.baseURI || document.URL;
    url = decodeURI(url);
    var parse = url.match(/^(([a-z]+):\/\/)?([^\/\?#]+)\/*([^\?#]*)\??([^#]*)#?(\w*)$/i);
    var query = parse[5];
    var arrtmp = query.split("&");
    var queryMap = new HashMap();
    for (i = 0; i < arrtmp.length; i++) {
        num = arrtmp[i].indexOf("=");
        if (num > 0) {
            name = arrtmp[i].substring(0, num);
            value = arrtmp[i].substr(num + 1);
            queryMap.put(name, value);
        }
    }
    return queryMap;
};

// 仿haspmap对象
function HashMap() {
    //定义长度
    var length = 0;
    //创建一个对象
    var obj = new Object();

    /**
     * 判断Map是否为空
     */
    this.isEmpty = function () {
        return length == 0;
    };

    /**
     * 判断对象中是否包含给定Key
     */
    this.containsKey = function (key) {
        return (key in obj);
    };

    /**
     * 判断对象中是否包含给定的Value
     */
    this.containsValue = function (value) {
        for (var key in obj) {
            if (obj[key] == value) {
                return true;
            }
        }
        return false;
    };

    /**
     *向map中添加数据
     */
    this.put = function (key, value) {
        if (!this.containsKey(key)) {
            length++;
        }
        obj[key] = value;
    };

    /**
     * 根据给定的Key获得Value
     */
    this.get = function (key) {
        return this.containsKey(key) ? obj[key] : null;
    };

    /**
     * 根据给定的Key删除一个值
     */
    this.remove = function (key) {
        if (this.containsKey(key) && (delete obj[key])) {
            length--;
        }
    };

    /**
     * 获得Map中的所有Value
     */
    this.values = function () {
        var _values = new Array();
        for (var key in obj) {
            _values.push(obj[key]);
        }
        return _values;
    };

    /**
     * 获得Map中的所有Key
     */
    this.keySet = function () {
        var _keys = new Array();
        for (var key in obj) {
            _keys.push(key);
        }
        return _keys;
    };

    /**
     * 获得Map的长度
     */
    this.size = function () {
        return length;
    };

    /**
     * 清空Map
     */
    this.clear = function () {
        length = 0;
        obj = new Object();
    };
}


// ----------------------------------------------------
/**
 * Array.js - v0.0.2
 *
 * - $contains(v) / $include(v)
 * - $removeValue(v)
 * - $remove(index)
 * - $removeIf(fn)
 * - $keepIf(fn)
 * - $replace(newValues)
 * - $clear()
 * - $each(fn)
 * - $unique(fn)
 * - $get(index)
 * - $getAll(index1, index2, ...) / $getAll(indexes1, indexes2, ...)
 * - $first()
 * - $last()
 * - $set(index, value)
 * - $copy()
 * - $isEmpty()
 * - $all(fn)
 * - $any(fn)
 * - $map(fn) / $collect(fn)
 * - $reduce(fn)
 * - $find(fn)
 * - $findAll(fn) / $filter(fn)
 * - $reject(fn)
 * - $grep(pattern)
 * - $keys(value, strict) / $indexesOf(value, strict)
 * - $sort(compare)
 * - $rsort(compare)
 * - $arsort(compare)
 * - $diff(array2)
 * - $intersect(array2)
 * - $max(compare)
 * - $min(compare)
 * - $swap(index1, index2)
 * - $sum(fn)
 * - $product(fn)
 * - $chunk(size)
 * - $combine(array1, ...)
 * - $pad(value, size)
 * - $fill(value, length)
 * - $shuffle()
 * - $rand(size)
 * - $size() / $count()
 * - $push(value1, value2, ...)
 * - $pushAll(array2)
 * - $insert(index, obj1, ...)
 * - $asc(field)
 * - $desc(field)
 * - $equal(array2)
 * - $loop(fn)
 * - $asJSON(field)
 * - Array.$range(start, end, step)
 * - Array.$isArray(obj)
 *
 * - Array.$json_col(obj)
 *
 * @author 刘祥超 <iwind.liu@gmail.com>
 */

/**
 * 空对象
 */
Array.$nil = {};

/**
 * 判断数组中是否包含某个值
 * 全等
 */
Array.prototype.$contains = function (v) {
    var that = this;
    if (that == null) {
        return false;
    }
    for (var i = 0; i < that.length; i++) {
        if (that[i] == v) {
            return true;
        }
    }
    return false;
};

/**
 * 同$contains(v)
 */
Array.prototype.$include = function (v) {
    var that = this;
    if (that == null) {
        return false;
    }
    return that.$contains(v);
};

/**
 * 从数组中删除某个值
 */
Array.prototype.$removeValue = function (v) {
    var that = this;
    if (that == null) {
        return true;
    }
    var newArray = [];
    for (var i = 0; i < that.length; i++) {
        if (that[i] != v) {
            newArray.push(that[i]);
        }
    }
    that.$clear();
    that.$pushAll(newArray);
    return true;
};

/**
 * 从数组中删除某个位置上的值
 */
Array.prototype.$remove = function (index) {
    var that = this;
    if (that == null) {
        return true;
    }
    that.splice(index, 1);
    return true;
};

/**
 * 删除所有满足条件的元素，并返回删除的元素的个数
 */
Array.prototype.$removeIf = function (fn) {
    var that = this;
    if (that == null) {
        return 0;
    }

    var oldLength = that.length;
    var left = that.$reject(fn);
    that.$replace(left);
    return oldLength - that.length;
};

/**
 * 保留所有满足条件的元素，删除不满足条件的元素
 */
Array.prototype.$keepIf = function (fn) {
    var that = this;
    if (that == null) {
        return 0;
    }

    var oldLength = that.length;
    var left = that.$findAll(fn);
    that.$replace(left);
    return oldLength - that.length;
};

/**
 * 将当前数组的元素替换成新的数组中的元素
 */
Array.prototype.$replace = function (newValues) {
    var that = this;
    if (that == null) {
        return false;
    }
    if (!Array.isArray(newValues)) {
        return false;
    }

    that.splice.apply(that, [0, that.length].concat(newValues));

    return true;
};

/**
 * 清空数组
 */
Array.prototype.$clear = function () {
    var that = this;
    if (that == null) {
        return true;
    }

    if (that.length == 0) {
        return true;
    }
    that.splice(0, that.length);
    return true;
};

/**
 * 遍历数组
 */
Array.prototype.$each = function (fn) {
    var that = this;
    if (that == null) {
        return true;
    }
    if (typeof(fn) != "function") {
        return true;
    }

    var length = that.length;
    for (var i = 0; i < length; i++) {
        fn.call(that, i, that[i]);
    }

    return true;
};

/**
 * 去除数组中的相同数据
 */
Array.prototype.$unique = function (fn) {
    var that = this;
    if (that == null) {
        return true;
    }
    var newArray = [];
    var indexes = [];
    that.$each(function (k, v) {
        if (typeof(fn) == "function") {
            v = fn.call(that, k, v);
        }
        if (!newArray.$contains(v)) {
            newArray.push(v);
            indexes.push(k);
        }
    });
    var copy = that.$copy();
    that.$clear();
    for (var i = 0; i < indexes.length; i++) {
        that.push(copy[indexes[i]]);
    }
    return true;
};

/**
 * 获取某个索引位置上的值
 */
Array.prototype.$get = function (index) {
    var that = this;
    if (that == null) {
        return null;
    }
    if (index > that.length - 1) {
        return null;
    }
    return that[index];
};

/**
 * 获取一组索引对应的值
 *
 * 如果超出索引范围，则不返回数据
 */
Array.prototype.$getAll = function (index1) {
    var that = this;
    if (that == null) {
        return [];
    }
    var values = [];
    for (var i = 0; i < arguments.length; i++) {
        var arg = arguments[i];
        if (Array.$isArray(arg)) {
            values.$pushAll(that.$getAll.apply(that, arg));
        }
        else if (typeof(arg) == "number" && arg < that.length) {
            values.$push(that.$get(arg));
        }
        else if (typeof(arg) == "string" && /^\\d+$/.test(arg)) {
            arg = parseInt(arg);
            if (arg < that.length) {
                values.$push(that.$get(arg));
            }
        }
    }
    return values;
};

/**
 * 设置某个索引位置上的值
 */
Array.prototype.$set = function (index, value) {
    var that = this;
    if (that == null) {
        return false;
    }
    if (index > that.length - 1) {
        return false;
    }
    that[index] = value;
    return true;
};

// 尝试复制对象
var clone = function (obj) {
    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj) return obj;

    // Handle Date
    if (obj instanceof Date) {
        var copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
    }

    // Handle Array
    if (obj instanceof Array) {
        var copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        var copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
};

/**
 * 拷贝数组
 */
Array.prototype.$copy = function () {
    var that = this;
    if (that == null) {
        return that;
    }

    var newArray = [];
    for (var i = 0; i < that.length; i++) {
        newArray.push(that[i]);
    }
    return newArray;
};

/**
 * 判断数组是否为空
 */
Array.prototype.$isEmpty = function () {
    var that = this;
    if (that == null) {
        return true;
    }
    return (that.length == 0);
};

/**
 * 对容器中元素应用迭代器,并判断是否全部返回真
 */
Array.prototype.$all = function (fn) {
    var that = this;
    if (that == null) {
        return false;
    }
    for (var i = 0; i < that.length; i++) {
        if (!fn.call(that, i, that[i])) {
            return false;
        }
    }
    return true;
};

/**
 * 对容器中元素应用迭代器,并判断是否有一次返回真
 */
Array.prototype.$any = function (fn) {
    var that = this;
    if (that == null) {
        return false;
    }
    for (var i = 0; i < that.length; i++) {
        if (fn.call(that, i, that[i])) {
            return true;
        }
    }
    return false;
};

/**
 * 对容器中元素应用迭代器,并将每次执行的结果放入一新数组中
 */
Array.prototype.$map = function (fn) {
    var that = this;
    if (that == null) {
        return [];
    }
    var arr = [];
    for (var i = 0; i < that.length; i++) {
        var result = fn.call(that, i, that[i]);
        if (result === Array.$nil) {
            continue;
        }
        arr.push(result);
    }
    return arr;
};

/**
 * 对容器中元素应用迭代器,并将每次执行的结果放入到下一次迭代的参数中
 */
Array.prototype.$reduce = function (fn) {
    var that = this;
    if (that == null) {
        return null;
    }
    var value = null;
    that.$each(function (k, v) {
        value = fn.call(that, k, v, value);
    });
    return value;
};

/**
 * 对容器中元素应用迭代器,并将每次执行的结果放入一新数组中
 */
Array.prototype.$collect = function (fn) {
    var that = this;
    if (that == null) {
        return [];
    }
    return that.$map(fn);
};

/**
 * 对容器中元素应用迭代器,只要有一次返回值即立即返回由当前元素的索引
 */
Array.prototype.$find = function (fn) {
    var that = this;
    if (that == null) {
        return -1;
    }
    if (typeof(fn) == "undefined") {
        return that.$get(0);
    }
    var index = -1;
    var result = null;
    that.$each(function (k, v) {
        if (index > -1) {
            return;
        }
        if (fn.call(that, k, v)) {
            index = k;
            result = v;
        }
    });
    return result;
};

/**
 * 对容器中元素应用迭代器,将所有返回真的元素放入一数组中
 */
Array.prototype.$findAll = function (fn) {
    var that = this;
    if (that == null) {
        return [];
    }
    if (typeof(fn) == "undefined") {
        return that.$copy();
    }
    var result = [];
    that.$each(function (k, v) {
        if (fn.call(that, k, v)) {
            result.push(v);
        }
    });
    return result;
};

/**
 * 同$findAll()
 */
Array.prototype.$filter = function (fn) {
    var that = this;
    if (that == null) {
        return [];
    }
    return that.$findAll(fn);
};

/**
 * 对容器中元素应用迭代器,将所有返回假的元素放入一数组中
 */
Array.prototype.$reject = function (fn) {
    var that = this;
    if (that == null) {
        return [];
    }
    if (typeof(fn) == "undefined") {
        return [];
    }
    var result = [];
    that.$each(function (k, v) {
        if (!fn.call(that, k, v)) {
            result.push(v);
        }
    });
    return result;
};

/**
 * 找出匹配某正则表达式的元素，返回匹配的元素数组
 */
Array.prototype.$grep = function (pattern) {
    var that = this;
    if (that == null) {
        return [];
    }
    return that.$findAll(function (k, v) {
        if (v == null) {
            return false;
        }
        return pattern.test(v.toString());
    });
};

/**
 * 取得某一个值在数组中出现的所有的键的集合
 */
Array.prototype.$keys = function (value, strict) {
    var that = this;
    if (that == null) {
        return [];
    }
    if (arguments.length == 0) {
        return Array.$range(0, that.length - 1);
    }
    var keys = [];
    if (typeof(strict) == "undefined") {
        strict = false;
    }
    for (var i = 0; i < that.length; i++) {
        if ((strict && value === that[i]) || (!strict && value == that[i])) {
            keys.push(i);
        }
    }
    return keys;
};

/**
 * 取得某一个值在数组中出现的所有的键的集合
 */
Array.prototype.$indexesOf = function (value, strict) {
    var that = this;
    if (that == null) {
        return [];
    }
    if (arguments.length == 0) {
        return Array.$range(0, that.length - 1);
    }
    return that.$keys(value, strict);
};

/**
 * 对该数组进行正排序
 */
Array.prototype.$sort = function (sortFunction) {
    var that = this;
    if (that == null) {
        return false
    }
    if (typeof(sortFunction) == "undefined") {
        sortFunction = function (v1, v2) {
            if (v1 > v2) {
                return 1;
            }
            else if (v1 == v2) {
                return 0;
            }
            else {
                return -1;
            }
        };
    }
    that.sort(sortFunction);
    return true;
};

/**
 * 对该数组进行反排序
 */
Array.prototype.$rsort = function (sortFunction) {
    var that = this;
    if (that == null) {
        return false
    }

    this.$sort(sortFunction);
    that.reverse();
    return true;
};

/**
 * 对该数组进行正排序，并返回排序后对应的索引
 */
Array.prototype.$asort = function (sortFunction) {
    var that = this;
    if (that == null) {
        return []
    }

    var indexes = [];
    for (var i = 0; i < that.length; i++) {
        indexes.push(i);
    }
    if (typeof(sortFunction) == "undefined") {
        sortFunction = function (e1, e2) {
            if (e1 < e2) return -1;
            if (e1 > e2) return 1;
            return 0;
        };
    }
    for (i = 0; i < that.length; i++) {
        for (var j = 0; j < that.length; j++) {
            if (j > 0 && sortFunction(that[j - 1], that[j]) > 0) {
                that.$swap(j, j - 1);
                indexes.$swap(j, j - 1);
            }
        }
    }
    return indexes;
};

/**
 * 对该数组进行反排序，并返回排序后对应的索引
 */
Array.prototype.$arsort = function (sortFunction) {
    var that = this;
    if (that == null) {
        return []
    }

    var indexes = that.$asort(sortFunction);
    that.reverse();
    indexes.reverse();
    return indexes;
};

/**
 * 取当前数组与另一数组的差集
 */
Array.prototype.$diff = function (array2) {
    var that = this;
    if (that == null) {
        return []
    }

    var result = [];
    that.$each(function (k, v) {
        if (!array2.$contains(v)) {
            result.push(v);
        }
    });
    return result;
};


/**
 * 取当前数组与另一数组的交集
 */
Array.prototype.$intersect = function (array2) {
    var that = this;
    if (that == null) {
        return []
    }

    var result = [];
    that.$each(function (k, v) {
        if (array2.$contains(v)) {
            result.push(v);
        }
    });
    return result;
};

/**
 * 取得当前集合中最大的一个值
 */
Array.prototype.$max = function (sortFunction) {
    var that = this;
    if (that == null) {
        return null;
    }
    if (that.length > 0) {
        var _array = that.$copy();
        _array.$rsort(sortFunction);
        return _array.$get(0);
    }
    return null;
};

/**
 * 取得当前集合中最小的一个值
 */
Array.prototype.$min = function (sortFunction) {
    var that = this;
    if (that == null) {
        return null;
    }
    if (that.length > 0) {
        var _array = that.$copy();
        _array.$sort(sortFunction);
        return _array.$get(0);
    }
    return null;
};

/**
 * 交换数组的两个索引对应的值
 */
Array.prototype.$swap = function (index1, index2) {
    var that = this;
    if (that == null) {
        return false;
    }
    var value1 = that.$get(index1);
    var value2 = that.$get(index2);
    that.$set(index1, value2);
    that.$set(index2, value1);
    return true;
};

/**
 * 计算数组中的所有元素的总和
 */
Array.prototype.$sum = function (fn) {
    var that = this;
    if (that == null) {
        return 0;
    }
    var sum = 0;
    that.$each(function (k, v) {
        if (typeof(fn) == "function") {
            v = fn.call(that, k, v);
        }
        if (typeof(v) == "number") {
            sum += v;
        }
        else if (typeof(v) == "string") {
            var n = parseFloat(v);
            if (!isNaN(n)) {
                sum += n;
            }
        }
    });
    return sum;
};

/**
 * 计算数组中的所有元素的乘积
 */
Array.prototype.$product = function (fn) {
    var that = this;
    if (that == null) {
        return 0;
    }
    var result = 1;
    that.$each(function (k, v) {
        if (typeof(fn) == "function") {
            v = fn.call(that, k, v);
        }
        if (typeof(v) == "number") {
            result *= v;
        }
        else if (typeof(v) == "string") {
            var n = parseFloat(v);
            if (!isNaN(n)) {
                result *= n;
            }
        }
    });
    return result;
};

/**
 * 返回数组分成新多个片段的结果
 */
Array.prototype.$chunk = function (size) {
    var that = this;
    if (that == null) {
        return [];
    }
    if (typeof(size) == "undefined") {
        size = 1;
    }
    size = parseInt(size);
    if (isNaN(size) || size < 1) {
        return [];
    }
    var result = [];
    for (var i = 0; i < that.length / size; i++) {
        result.$push(that.slice(i * size, (i + 1) * size));
    }
    return result;
};

/**
 * 取得当前数组和其他数组组合之后的结果
 */
Array.prototype.$combine = function (array1) {
    var that = this;
    if (that == null) {
        return [];
    }

    var result = that.$chunk(1);
    for (var i = 0; i < arguments.length; i++) {
        var arr = arguments[i];
        if (Array.$isArray(arr)) {
            for (var j = 0; j < that.length; j++) {
                result[j].$push(arr.$get(j));
            }
        }
    }
    return result;
};

/**
 * 填充数组
 */
Array.prototype.$pad = function (value, size) {
    var that = this;
    if (that == null) {
        return false;
    }
    if (typeof(size) == "undefined") {
        size = 1;
    }
    if (size < 1) {
        return false;
    }
    for (var i = 0; i < size; i++) {
        that.push(value);
    }
    return true;
};

/**
 * 填充数组到一定长度
 */
Array.prototype.$fill = function (value, length) {
    var that = this;
    if (that == null) {
        return false;
    }
    if (typeof(length) == "undefined") {
        length = that.length;
    }
    if (length < that.length) {
        return false;
    }
    if (length == that.length) {
        return true;
    }
    var size = length - that.length;
    for (var i = 0; i < size; i++) {
        that.push(value);
    }
    return true;
};

/**
 * 打乱数组中元素顺序
 */
Array.prototype.$shuffle = function () {
    var that = this;
    if (that == null) {
        return false;
    }

    that.$sort(function () {
        return Math.random() - 0.5;
    });
    return true;
};

/**
 * 随机截取数组片段
 */
Array.prototype.$rand = function (size) {
    var that = this;
    if (that == null) {
        return false;
    }
    if (typeof(size) == "undefined") {
        size = 1;
    }
    var copy = that.$copy();
    copy.$shuffle();
    return copy.slice(0, size);
};

/**
 * 计算元素数量
 */
Array.prototype.$size = function () {
    var that = this;
    if (that == null) {
        return 0;
    }
    return that.length;
};

/**
 * 同$size()
 */
Array.prototype.$count = function () {
    var that = this;
    if (that == null) {
        return 0;
    }
    return that.length;
};

/**
 * 取得第一个元素值
 */
Array.prototype.$first = function () {
    var that = this;
    if (that == null) {
        return null;
    }
    if (that.length == 0) {
        return null;
    }
    return that.$get(0);
};

/**
 * 取得第一个元素值
 */
Array.prototype.$last = function () {
    var that = this;
    if (that == null) {
        return null;
    }
    if (that.length == 0) {
        return null;
    }
    return that[that.length - 1];
};

/**
 * 在尾部加入一个或多个元素
 */
Array.prototype.$push = function () {
    var that = this;
    if (that == null) {
        return 0;
    }
    return Array.prototype.push.apply(that, arguments);
};

/**
 * 一次性加入多个元素
 */
Array.prototype.$pushAll = function (array2) {
    var that = this;
    if (that == null) {
        return 0;
    }
    return Array.prototype.push.apply(that, array2);
};

/**
 * 在指定位置插入新的元素
 */
Array.prototype.$insert = function (index, obj1) {
    var that = this;
    if (that == null) {
        return false;
    }

    var args = [];
    if (arguments.length == 0) {
        return false;
    }

    for (var i = 1; i < arguments.length; i++) {
        args.push(arguments[i]);
    }

    if (index < 0) {
        index = that.length + index + 1;
    }

    that.splice.apply(that, [index, 0].concat(args));

    return true;
};

/**
 * 依据单个字段进行正排序
 */
Array.prototype.$asc = function (field) {
    var that = this;
    if (that == null) {
        return false;
    }
    return that.$sort(function (v1, v2) {
        if (typeof(v1) == "object" && typeof(v2) == "object") {
            if (v1[field] > v2[field]) {
                return 1;
            }
            if (v1[field] == v2[field]) {
                return 0;
            }
            return -1;
        }
        return 0;
    });
};

/**
 * 依据单个字段进行倒排序
 */
Array.prototype.$desc = function (field) {
    var that = this;
    if (that == null) {
        return false;
    }
    return that.$sort(function (v1, v2) {
        if (typeof(v1) == "object" && typeof(v2) == "object") {
            if (v1[field] > v2[field]) {
                return -1;
            }
            if (v1[field] == v2[field]) {
                return 0;
            }
            return 1;
        }
        return 0;
    });
};

/**
 * 判断两个数组是否以同样的顺序包含同样的元素
 */
Array.prototype.$equal = function (array2) {
    var that = this;
    if (that == null) {
        return false;
    }
    if (!Array.$isArray(array2)) {
        return false;
    }
    if (that.length != array2.length) {
        return false;
    }

    for (var i = 0; i < that.length; i++) {
        if (that[i] != array2[i]) {
            return false;
        }
    }
    return true;
};

/**
 * 循环使用当前数组的元素来调用某个函数
 */
Array.prototype.$loop = function (fn) {
    var that = this;
    if (that == null) {
        return false;
    }
    if (that.length == 0) {
        return false;
    }

    fn.call(that, 0, that[0], {
        "index": 0,
        "next": function () {
            this.index++;
            if (this.index > that.length - 1) {
                this.index = 0;
            }
            fn.call(that, this.index, that[this.index], this);
            return this.index;
        },
        "sleep": function (ms) {
            var that = this;
            setTimeout(function () {
                that.next();
            }, ms);
        }
    });

    return true;
};

/**
 * 取得当前数组转换为JSON格式的字符串
 */
Array.prototype.$asJSON = function () {
    return JSON.stringify(this);
};

/**
 * 从一个限定的范围数字或字符生成一个数组
 */
Array.$range = function (start, end, step) {
    var array = [];

    if (typeof(step) == "undefined") {
        step = 1;
    }

    if (start < end) {
        for (var i = start; i <= end; i += step) {
            array.push(i);
        }
    }
    else {
        for (var i = start; i >= end; i -= step) {
            array.push(i);
        }
    }
    return array;
};

/**
 * 判断一个对象是否为数组
 */
Array.$isArray = function (obj) {
    return Object.prototype.toString.call(obj) === "[object Array]";
};

// Production steps of ECMA-262, Edition 6, 22.1.2.1
//来自 https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/from
if (!Array.from) {
    Array.from = (function () {
        var toStr = Object.prototype.toString;
        var isCallable = function (fn) {
            return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
        };
        var toInteger = function (value) {
            var number = Number(value);
            if (isNaN(number)) {
                return 0;
            }
            if (number === 0 || !isFinite(number)) {
                return number;
            }
            return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
        };
        var maxSafeInteger = Math.pow(2, 53) - 1;
        var toLength = function (value) {
            var len = toInteger(value);
            return Math.min(Math.max(len, 0), maxSafeInteger);
        };

        // The length property of the from method is 1.
        return function from(arrayLike/*, mapFn, thisArg */) {
            // 1. Let C be the this value.
            var C = this;

            // 2. Let items be ToObject(arrayLike).
            var items = Object(arrayLike);

            // 3. ReturnIfAbrupt(items).
            if (arrayLike == null) {
                throw new TypeError("Array.from requires an array-like object - not null or undefined");
            }

            // 4. If mapfn is undefined, then let mapping be false.
            var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
            var T;
            if (typeof mapFn !== 'undefined') {
                // 5. else
                // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
                if (!isCallable(mapFn)) {
                    throw new TypeError('Array.from: when provided, the second argument must be a function');
                }

                // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
                if (arguments.length > 2) {
                    T = arguments[2];
                }
            }

            // 10. Let lenValue be Get(items, "length").
            // 11. Let len be ToLength(lenValue).
            var len = toLength(items.length);

            // 13. If IsConstructor(C) is true, then
            // 13. a. Let A be the result of calling the [[Construct]] internal method
            // of C with an argument list containing the single item len.
            // 14. a. Else, Let A be ArrayCreate(len).
            var A = isCallable(C) ? Object(new C(len)) : new Array(len);

            // 16. Let k be 0.
            var k = 0;
            // 17. Repeat, while k < len… (also steps a - h)
            var kValue;
            while (k < len) {
                kValue = items[k];
                if (mapFn) {
                    A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
                } else {
                    A[k] = kValue;
                }
                k += 1;
            }
            // 18. Let putStatus be Put(A, "length", len, true).
            A.length = len;
            // 20. Return A.
            return A;
        };
    }());
}

/**
 * 返回数组的json项中某一列
 * @param col
 * @returns {*}
 */
Array.prototype.$json_col = function (col) {
    var that = this;
    if (that == null) {
        return false;
    }
    var newArray = [];
    for (var i = 0; i < that.length; i++) {
        newArray.push(that[i][col]);
    }
    return newArray;
};

// 将数组转化为 'xx','xx'
Array.prototype.$sql_str = function () {
    var that = this;
    var str = '';

    that.forEach(function (item, index) {
        if (index == 0) {
            str += "('" + item + "'";
        } else if (index + 1 == that.length) {
            str += ",'" + item + "')";
        } else {
            str += ",'" + item + "'";
        }
    });

    return str;
};


/**
 * 过滤数组中 json 数据
 * 列的差集
 * @param col
 * @param arr2
 * @returns {*}
 */
Array.prototype.$json_diff = function (col, arr2) {
    var that = this;
    if (that == null) {
        return false;
    }
    arr2.forEach(function (item) {
        that.forEach(function (json, index) {
            if (json[col] == item) {
                that.splice(index, 1);
            }
        });
    });
    return this;
};


/**
 * 保留数组中json数据中 某一列的值 与 数组2中相等的值
 * @param col
 * @param arr2
 * @param filter_type
 * @returns {*}
 */
Array.prototype.$json_intersect = function (col, arr2, filter_type) {
    var that = this;
    if (that == null) {
        return false;
    }
    if (filter_type == undefined) {
        filter_type = '=';
    }
    var new_array = [];
    switch(filter_type) {
        case '=':
            that.forEach(function (json, index) {
                if (arr2.$contains(json[col])) {
                    new_array.push(that[index]);
                }
            });
            break;
        case '>':
            break;
        case '>=':
            break;
        case '<':
            break;
        case '<=':
            break;
        default :

    }
    return new_array;
};


/**
 * 在数组json项数据中搜索 某些字段 是否 包含 某个值
 * 返回包含的数组
 * @param col
 * @param val
 * @returns {*}
 */
Array.prototype.$like_contains = function (col, val) {
    var that = this;
    if (that == null) {
        return false;
    }
    var new_array = [];
    //var patten = new RegExp("\\.\\*" + val + "\\.\\*", "gi");
    that.forEach(function (json, index) {
        if (Array.isArray(col)) {
            var add_entry = 0;
            col.forEach(function (v, k) {
                if (~that[index][v].indexOf(val)) {
                    add_entry = 1;
                }
            });

            if (add_entry == 1) {
                new_array.push(that[index]);
            }
        } else if (typeof col == 'string') {
            if (~that[index][col].indexOf(val)) {
                new_array.push(that[index]);
            }
        }
    });
    return new_array;
};

/**
 * 在数据中 搜索 数组中某些字段 是否 包含 某个值
 * 包含返回true,否则返回false
 * @param col
 * @param val
 * @return bool
 */
Array.prototype.$col_have_val = function (col, val) {
    var that = this;
    if (that == null) {
        return false;
    }
    var new_array = [];
    //var patten = new RegExp("\\.\\*" + val + "\\.\\*", "gi");
    that.forEach(function (json, index) {
        if (Array.isArray(col)) {
            var add_entry = 0;
            col.forEach(function (v, k) {
                //if(patten.test(that[index][v])){
                if (~that[index][v].indexOf(val)) {
                    add_entry = 1;
                }
            });

            if (add_entry == 1) {
                new_array.push(that[index]);
            }
        } else if (typeof col == 'string') {
            if (~that[index][col].indexOf(val)) {
                new_array.push(that[index]);
            }
        }
    });
    return (new_array.length > 0);
};

/**
 *  返回某些根级下面所有的子级
 * @param data arr  源数据
 * @param root arr 需要查询子级的数组
 * @param id  查询哪个字段
 * @param parent_id  字段的父级字段
 * @returns {*}
 */
var json_root_all_sub = function (data, root, id, parent_id) {
    //console.log('data: ' + console.log(data));
    //console.log('root: ' + console.log(root));
    //console.log('id: ' + id);
    //console.log('parent_id: ' + parent_id);

    var new_array = [];
    root.forEach(function (v, k) {
        data.forEach(function (val, key) {
            if (v == data[key][id]) {
                new_array.push(data[key]);
            }
            // console.log(data[key][parent_id]);
            if (v == data[key][parent_id]) {
                var sub_array = [];
                sub_array = json_root_all_sub(data, [data[key][id]], id, parent_id);
                new_array = new_array.concat(sub_array);
            }
        });
    });

    return new_array;
};


// 异步顺序中，某一步骤 需要同步调用多个异步时使用
// 判断是否跳出父级异步顺序中的某一步
// 当step_arr 中全是true的时候，sup_obj中 curr_step 加 1
var sup_step_add = function (step_arr, k, sup_obj, out_time) {
    var jump = '';
    var is_2w_arr = step_arr.every(function (item, index, array) {
        return (Array.isArray(item));
    });

    if (is_2w_arr) {
        var temp_arr = [];
        step_arr.forEach(function (tv, tk) {
            var temp = tv.every(function (item, index, array) {
                return (item == true);
            });
            temp_arr.push(temp);
        });

        jump = temp_arr.every(function (item, index, array) {
            return (item == true);
        });
    } else {
        // 1 给异步方法执行完毕一个结果
        step_arr[k] = true;
        // 2 判断当所有异步项结果都是true的时候，进行下一步
        jump = step_arr.every(function (item, index, array) {
            return (item == true);
        });
    }

    console.log('处理数据表字典列 k:' + k + ' : ' + step_arr[k]);
    console.log('字典处理结果: ' + step_arr);
    console.log('是否跳转: ' + jump);
    if (jump) {
        console.log("顺序中某一步中多异步执行完毕.");
        if (out_time) {
            setTimeout(function () {
                sup_obj.run_next();
            }, 200);
        } else {
            sup_obj.run_next();
        }
    }
};

/**
 * 将数组中的json数据，根据A列，合并B的数据。
 * 返回一个新的数组，并且A列唯一
 * @param compare_col
 * @param merge_col
 * return arr
 * 将 [{a:1, b:333},{a:1, b: 444}] 合并成[{a:1, b:333444}]
 */
Array.prototype.$arr_json_unite_col = function (compare_col, merge_col) {
    var that = this;
    if (isNull(that)) {
        return false;
    }
    var new_arr = [];
    that.forEach(function (v, k) {
        var temp = v[compare_col];
        var temp_arr = new_arr.$json_col(compare_col);
        var curr = temp_arr.indexOf(temp);
        if (curr == -1) {
            new_arr.push(clone(v));
        } else {
            new_arr[curr][merge_col] += v[merge_col];
        }
    });
    return new_arr;
};


/**
 * 比较other_arr中compare_col与本数组对象相同的，相同就将add_col加入到本对象中
 * @param other_arr 从这数组中添加数据
 * @param com_t_col 源数据中要比较的列名
 * @param com_o_col 另一数据中要比较的列名
 * @param add_o_col 另一数据中要添加的列名
 * @param add_r_col 添加之后改成另一个列名
 */
Array.prototype.$arr_json_add_col = function (other_arr, com_t_col, com_o_col, add_o_col, add_r_col) {
    var that = this;
    if (isNull(that)) {
        return false;
    }
    that.forEach(function (v, k) {
        var temp = v[com_t_col];
        for (var i = 0, c = other_arr.length; i < c; i++) {
            if (temp == other_arr[i][com_o_col]) {
                v[add_r_col] = clone(other_arr[i][add_o_col]);
                break;
            }
        }
    });
    return that;
};

/**
 * 返回 id 的高度
 * @param id
 * @returns {number}
 */
var get_ele_id_height = function (id) {
    return document.getElementById(id).offsetHeight;
};


/**
 * 返回 id 的宽度
 * @param id
 * @returns {number}
 */
var get_ele_id_width = function (id) {
    return document.getElementById(id).offsetWidth;
};


/**
 * 将ui日期控件中的-去掉
 * @returns {string}
 */
String.prototype.date_ui_to_sys = function () {
    var that = this;
    return that.replace(/-/g, "");
};

/**
 * 将系统中日期格式中增加-
 * @returns {string}
 */
String.prototype.date_sys_to_ui = function () {
    var that = this;
    var y = that.substr(0, 4);
    var m = that.substr(4, 2);
    var d = that.substr(6, 2);
    return y + '-' + m + '-' + d;
};

/**
 * ****在企业微信浏览器中不能正常调用****
 * 返回在字符串中查找字符的位置
 * find_char 要查找的字符
 * n 第几次
 */
function find_char_pos(that, find_char, n) {
    var positions = [];
    var pos = that.indexOf(find_char);
    while (pos > -1) {
        positions.push(pos);
        pos = that.indexOf(find_char, pos + 1);
    }

    return positions[n - 1];
}

/**
 * ****在企业微信浏览器中不能正常调用****
 * 正则表达式
 * 查找字符在字符串中第几次出现的位置
 * RegExp 要查找的字符
 * n 第几次
 */
String.prototype.find_char_pos_reg = function (reg1, n) {
    var that = this;
    var positions = [];
    var result = [];
    while ((result = reg1.exec(that)) != null) {
        positions.push(result['index']);
    }
    console.log(positions);

    return positions[n - 1];
};
