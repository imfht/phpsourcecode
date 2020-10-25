/**
 * 封装操作cookie的方法
 * 
 * @name ucb.Cookie
 * @author Ican Cheung <zhangrq@ucweb.com>
 * 
 */
var ucb = ucb || {};

ucb.Supports = {
    /**
     * @property 是否支持触控
     * @type {Boolean}
     */
    Touch: ('ontouchstart' in window)
};

ucb.Cookie = ucb.Cookie || {};

(function(cookie) {
    
    /**
     * 验证字符串是否合法的cookie键名
     * 
     * @param {string} source 需要验证的key
     * @return {bool} 是否合法的cookie键名
     */
    cookie._isValidKey = function(key) {
        
        return (new RegExp("^[^\\x00-\\x20\\x7f\\(\\)<>@,;:\\\\\\\"\\[\\]\\?=\\{\\}\\/\\u0080-\\uffff]+\x24"))
                .test(key);
    };
    
    /**
     * 获取cookie的值，不对值进行解码
     * 
     * @function
     * @param {string} key 需要获取Cookie的键名
     * 
     * @returns {string|null} 获取的Cookie值，获取不到时返回null
     */
    cookie.getRaw = function(key) {
        if (cookie._isValidKey(key)) {
            var reg = new RegExp("(^| )" + key + "=([^;]*)(;|\x24)"), result = reg.exec(document.cookie);
            
            if (result) {
                return result[2] || null;
            }
        }
        
        return null;
    };
    
    /**
     * 获取cookie的值，用decodeURIComponent进行解码
     * 
     * @function
     * @param {string} key 需要获取Cookie的键名
     * @description <b>注意：</b>该方法会对cookie值进行decodeURIComponent解码。如果想获得cookie源字符串，请使用getRaw方法。
     * 
     * @returns {string|null} cookie的值，获取不到时返回null
     */
    cookie.get = function(key) {
        var value = cookie.getRaw(key);
        if ('string' == typeof value) {
            value = decodeURIComponent(value);
            return value;
        }
        return null;
    };
    
    /**
     * 设置cookie的值，不对值进行编码
     * 
     * @function
     * @param {string} key 需要设置Cookie的键名
     * @param {string} value 需要设置Cookie的值
     * @param {Object} [options] 设置Cookie的其他可选参数
     * @config {string} [path] cookie路径
     * @config {Date|number} [expires] cookie过期时间,如果类型是数字的话, 单位是毫秒
     * @config {string} [domain] cookie域名
     * @config {string} [secure] cookie是否安全传输
     */
    cookie.setRaw = function(key, value, options) {
        if (!cookie._isValidKey(key)) {
            return;
        }
        
        options = options || {};
        
        // 计算cookie过期时间
        var expires = options.expires;
        if ('number' == typeof options.expires) {
            expires = new Date();
            expires.setTime(expires.getTime() + options.expires);
        }
        
        document.cookie = key + "=" + value + (options.path ? "; path=" + options.path : "")
                + (expires ? "; expires=" + expires.toGMTString() : "")
                + (options.domain ? "; domain=" + options.domain : "") + (options.secure ? "; secure" : '');
    };
    
    /**
     * 设置cookie的值，用encodeURIComponent进行编码
     * 
     * @function
     * @param {string} key 需要设置Cookie的键名
     * @param {string} value 需要设置Cookie的值
     * @param {Object} [options] 设置Cookie的其他可选参数
     * @config {string} [path] cookie路径
     * @config {Date|number} [expires] cookie过期时间,如果类型是数字的话, 单位是毫秒
     * @config {string} [domain] cookie域名
     * @config {string} [secure] cookie是否安全传输
     */
    cookie.set = function(key, value, options) {
        cookie.setRaw(key, encodeURIComponent(value), options);
    };
    
    /**
     * 删除cookie的值
     * 
     * @function
     * @param {string} key 需要删除Cookie的键名
     * @param {Object} options 需要删除的cookie对应的 path domain 等值
     */
    cookie.remove = function(key, options) {
        options = options || {};
        options.expires = new Date(0);
        cookie.setRaw(key, '', options);
    };
    
})(ucb.Cookie);

