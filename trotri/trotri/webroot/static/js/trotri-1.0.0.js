/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Trotri
 * @author songhuan <trotri@yeah.net>
 * @version $Id: Trotri-1.0.0.js 1 2013-10-16 18:38:00Z $
 */
Trotri = {
  /**
   * 向控制台输出信息
   * @param string|object v
   * @return void
   */
  info: function(v) {
    if (window.console && window.console.info) {
      window.console.info(v);
    }
  },

  /**
   * 向控制台输出日志
   * @param string|object v
   * @return void
   */
  log: function(v) {
    if (window.console && window.console.log) {
      window.console.log(v);
    }
  },

  /**
   * 通过ID名，获取元素对象
   * @param string|object element
   * @return object
   */
  id: function(element) {
    return typeof(element) == 'object' ? element : document.getElementById(element);
  },

  /**
   * 清除字符串左边空格
   * @param string E
   * @return string
   */
  ltrim: function(E) {
    return (E||"").replace(/^\s+/g,"");
  },

  /**
   * 清除字符串右边空格
   * @param string E
   * @return string
   */
  rtrim: function(E) {
    return (E||"").replace(/\s+$/g,"");
  },

  /**
   * 清除字符串两边空格
   * @param string E
   * @return string
   */
  trim: function(E) {
    return (E||"").replace(/^\s+|\s+$/g,"");
  },

  /**
   * 字符串首字母大写，其他部分小写
   * @param string E
   * @return string
   */
  ucfirst: function(E) {
    return E.substr(0, 1).toUpperCase() + E.substr(1).toLowerCase();
  },

  /**
   * 将数组连接成字符串
   * @param string glue
   * @param array E
   * @return string
   */
  join: function(glue, E) {
    if (E.length <= 0) return "";
    var r = "";
    for (var i in E) {
      r += E[i] + glue;
    }
    return r.substr(0, r.length - glue.length);
  },

  /**
   * 判断字符在数组中的位置，如果不存在，返回-1
   * @param string v
   * @param array E
   * @return integer
   */
  inArray: function(v, E) {
    if (typeof(v) == 'string' || typeof(v) == 'number') {
      for (var i in E) {
        if (E[i] == v) { return i; }
      }
    }
    return -1;
  },

  /**
   * 匹配字符串前缀
   * @param string v
   * @param string E
   * @return boolean
   */
  startWith: function(v, E) {
    return E.substr(0, v.length) == v;
  },

  /**
   * 匹配字符串后缀
   * @param string v
   * @param string E
   * @return boolean
   */
  endWith: function(v, E) {
    var p = E.length - v.length;
    return E.substr(p) == v;
  },

  /**
   * 判断是否是邮箱格式
   * @param string E
   * @return boolean
   */
  isMail: function(E) {
    var pattern = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/ ;
    return (E != "" && pattern.test(E));
  },

  /**
   * 判断是否是整数
   * @param integer E
   * @return boolean
   */
  isInt: function(E) {
    var pattern = /^[0-9]+$/ ;
    return (E != "" && pattern.test(E));
  },

  /**
   * 判断是否是闰年
   * @param integer E
   * @return boolean
   */
  isLeapYear: function(E) {
    return (0 == E%4 && ((E%100 != 0) || (E%400 == 0)));
  },

  /**
   * 计算字符串长度
   * @param string E
   * @param string charset
   * @return integer
   */
  mb_strlen: function(E, charset) {
    var len = 0; var Elen = E.length;
    for (var i = 0; i < Elen; i++) {
      len += E.charCodeAt(i) < 0 || E.charCodeAt(i) > 255 ? (charset == "utf-8" ? 3 : 2) : 1;
    }
    return len;
  },

  /**
   * 切割字符串
   * @param string E
   * @param integer maxlen
   * @param string charset
   * @return string
   */
  mb_cutstr: function(E, maxlen, charset) {
    var len = 0; var ret = ""; var Elen = E.length;
    for (var i = 0; i < Elen; i++) {
      len += (E.charCodeAt(i) < 0 || E.charCodeAt(i) > 255) ? (charset == "utf-8" ? 3 : 2) : 1;
      if (len > maxlen) { break; }
      ret += E.substr(i, 1);
    }
    return ret;
  },

  /**
   * 获取Cookie
   * @param string name
   * @return string
   */
  getCookie: function(name) {
    var cookies = document.cookie.split("; ");
    for (var i in cookies) {
      var crumbs = cookies[i].split("=");
      if (name == crumbs[0]) {
        return decodeURIComponent(crumbs[1]);
      }
    }
    return null;
  },

  /**
   * 设置Cookie
   * @param string name
   * @param string value
   * @param integer expires
   * @return void
   */
  setCookie: function(name, value, expires) {
    var cookies = name + "=" + encodeURIComponent(value);
    if (expires != null) {
      cookies += "; expires=" + expires;
    }
    document.cookie = cookies;
  },

  /**
   * 删除Cookie
   * @param string name
   * @return void
   */
  removeCookie: function(name) {
    document.cookie = name + "=; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
  },

  /**
   * Ajax
   * @param json p
   * p = {type: "GET"|"POST", url: "", data: "", dataType: "TEXT|JSON", async: true|false, success: function(ret) {}}
   * @return mixed
   */
  ajax: function(p) {
    if (typeof(p) != "object") {
      Trotri.log("Trotri.ajax args is wrong");
    }

    var xhrObj = false;
    try {
      xhrObj = new XMLHttpRequest(); // Firefox IE8和非IE内核
    }
    catch (e) {
      var progid = ["MSXML2.XMLHTTP.5.0", "MSXML2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"]; // IE5.5 IE6 IE7内核
      for (var i in progid) {
        try {
          xhrObj = new ActiveXObject(progid[i]);
        }
        catch (e) { continue; }
        break;
      }
    }

    if (p.type != undefined) { p.type = p.type.toUpperCase(); }
    if (p.type != "POST") {
      p.type = "GET";
      p.url.indexOf("?") == -1 ? p.url += "?" + p.data : "";
      p.data = null;
    }

    p.async != false ? p.async = true : "";
    xhrObj.open(p.type, p.url, p.async);
    if (p.type == "POST") {
      xhrObj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    }
    xhrObj.send(p.data);

    if (p.dataType != undefined) { p.dataType = p.dataType.toUpperCase(); }
    p.dataType != "JSON" ? p.dataType = "TEXT" : "";

    xhrObj.onreadystatechange = function() {
      /**
       * readyState 值
       * 2 已经发送数据了，但是还没接收到反馈
       * 3 收到反馈了，反馈描述数据正在发送的过程中
       * 4 反馈描述数据已经被接收完毕
       */
      if (xhrObj.readyState == 4) {
        if (xhrObj.status == 200) {
          var data = xhrObj.responseText;
          if (p.dataType == "JSON") {
            eval("data = " + data + ";");
          }
          return p.success(data);
        }
      }
    }
  },

  /**
   * 获取所有被选中的checkbox框的值，用英文逗号分隔
   * @param string name
   * @return string
   */
  getCheckedValues: function(name) {
    var a = Trotri.getCheckeds(name);
    if (a.length <= 0) return "";
    var r = "";
    for (var i in a) {
      r += a[i].value + ",";
    }
    return r.substr(0, r.length - 1);
  },

  /**
   * 获取所有被选中的checkbox框
   * @param string name
   * @return array of object HTMLInputElement
   */
  getCheckeds: function(name) {
    var r = []; var n = 0;
    var a = Trotri.getInputs("checkbox", name);
    for (var i in a) {
      if (!Trotri.isInt(i)) { continue; }
      if (a[i].checked) {
        r[n++] = a[i];
      }
    }
    return r;
  },

  /**
   * 通过类型和名称过滤Input，并获取第一个Input
   * @param string type
   * @param string name
   * @return object HTMLInputElement | undefined
   */
  getInput: function(type, name) {
    var a = Trotri.getInputs(type, name);
    if (a.length > 0) {
      return a[0];
    }
    return undefined;
  },

  /**
   * 通过类型和名称过滤Input
   * @param string type
   * @param string name
   * @return array of object HTMLInputElement
   */
  getInputs: function(type, name) {
    var r = []; var n = 0;
    var a = document.getElementsByTagName("input");
    for (var i in a) {
      if (!Trotri.isInt(i)) { continue; }
      if (type != undefined && a[i].type != type) { continue; }
      if (name != undefined && a[i].name != name) { continue; }
      r[n++] = a[i];
    }
    return r;
  },

  /**
   * 刷新页面
   * @return void
   */
  refresh: function() {
    window.location.href = window.location.href;
    return false;
  },

  /**
   * 页面跳转
   * @param string url
   * @return void
   */
  href: function(url) {
    window.location.href = url;
    return false;
  },

  /**
   * 页面跳转，在新窗口打开
   * @param string url
   * @return void
   */
  bHref: function(url) {
    window.open(url);
    return false;
  }
}
