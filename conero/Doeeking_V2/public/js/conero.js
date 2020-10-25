/** 
 * Index/TeCenter 模块公共前端库
 * 2016年9月24日 星期六-与jQuery独立
 */
function Conero(){
    // 私有成员
    var Csl = console
    // 首页导航自适应
    this.navAbjust = function(){
        var path = location.pathname
        path = path.replace('/TeCenter/','')
        var arr = path.split('/'),name
        if(arr[1]){
            name = arr[1]
            name = name.replace('.html','')
        }else name = 'index'
        $('#navbar .navbar-nav').find('li.'+name).addClass(name+' active')
    }    
    // storage 对象
    this.storage = function(engine){
        return new jutilStorage(engine)
    }
	//  自动生成描点
    this.createHash = function(name,feek){
        if(name){
            var href = location.href
            var arr = href.split('#')
            href = arr[0]+'#'+name
            if(feek) return href
            location.href = href
        }
        return ''
    }
	// $_GET// URL 解析
	this.getQuery = function(key){
		/*
		if('#' == key){
			var hash = location.hash
			hash = hash.replace(new RegExp('#','g'),'')
			return hash
		}
		*/
		// 获取描点
		if('#' == key){
			var href = location.href
			if(href.indexOf('#')>-1){
				var arr = href.split('#')
				return arr[1]
			}
			return ''
		}
		var ser = location.search
		if(ser){
			ser = ser.replace('?','')
			ser = ser.replace(new RegExp('=','g'),'":"')
			ser = ser.replace(new RegExp('&','g'),'","')
			ser = '{"'+ser+'"}'
			var GET = JSON.parse(ser)
			if(key){
				if(GET[key]) return GET[key]
				return ''
			}
			return GET
		}
		return ''
	}
    // PHP+js+Base64
    var _jsVar
    this.getJsVar = function(key){
        if(this.is_string(coneroJsVar) && !this.is_object(_jsVar)){
            _jsVar = JSON.parse(Base64.decode(coneroJsVar))
        }        
        if(this.is_object(_jsVar)){
            if(this.undefind(key)) return jsVar
            if(this.empty(_jsVar[key])) return ''
            return _jsVar[key]
        }
    }
    //--------------------------调试帮助
    this.log = function(value){Csl.log(value)}
    this.is_string = function(value){
        if(typeof(value) == 'string') return true
        return false
    }
    this.is_object = function(value){
        if(typeof(value) == 'object') return true
        return false
    }
    this.undefind = function(value){
        if(typeof(value) == 'undefind') return true
        return false
    }
    this.empty = function(value){
        if(this.undefind(value)) return true
		else if(value == '') return true
		else if(value == 0) return true
		return false
    }
}

/**----------------------新扩展--------------------------2016年8月30日 星期二>>  				类似数据库处理操作// js面向对象式编程
*	engine 引擎|| session/local，默认前者
*	add()		插入数据到storage内，会覆盖历史数据
*	update()	更新数据到storage内，支持 json/string，可实现删除内部数据
*	select()	storage数据获取
*	get()		简单数据获取法
*	
*	table()		get/set storage数据键名
*	error()		get/set storage数据异常
*	session()/local()	storage数据设置获取函数(原型)
*	undefind()	数据格式检测
*	object(),empty()
*/
function jutilStorage(engine){
	if('session' != engine && 'local' != engine) this.engine = 'session'
	else this.engine = engine
	this.table = function(tb){
		if(this.undefind(tb)){
			var table = this._table
			if(this.undefind(table)) this._table = ''
			return this._table
		}else{
			this._table = tb
			return this// 链式数据处理
		}
	}
	//	会覆盖原来的值，若存在原来的json数据
	this.add = function(data,table){
		if(this.empty(table)) table = this.table()
		if(this.empty(table)){
			this.error('add## 无法获取到table的值！')
			return ''
		}
		if(!this.object(data)){
			this.error('add## 存储数据必须为JSON格式数据！')
			return ''
		}
		var str = JSON.stringify(data)
		if(this.engine == 'session') this.session(table,str)
		else this.local(table,str)
		return true
	}
	//	更新数据/可更新不存在的数据
	this.update = function(key,value){
		var tb = this.table()
		if(this.empty(tb)){
			this.error('select## 无法获取到table的值！')
			return false
		}
		var data = this.select()
		if(this.empty(key)){
			this.error('update## 无法获取到key的值,请设置key（json/string）值！')
			return false
		}
		if(!this.object(data)) data = {}
		if(this.object(key)){			
			for(var k in key){data[k] = key[k]}
		}
		if(this.undefind(value)){
			delete data[key]
		}else{
			data[key] = value
		}
		var str = JSON.stringify(data)
		return this.engine == 'session'? this.session(tb,str):this.local(tb,str)
	}
	this.select = function(key,value){
		var tb = this.table()
		if(this.empty(tb)){
			this.error('select## 无法获取到table的值！')
			return ''
		}
		var str = this.engine == 'session'? this.session(tb):this.local(tb)
		try{
			var data = JSON.parse(str)
		}catch(e){
			this.error(e)
			var data = {}
		}
		if(this.empty(key)) return data//	返回整个json数据
		else if(this.undefind(value)){//	返回单个json数据
			if(data[key]) return data[key]
			return ''
		}
		data[key] = value//	设置json的属性值
	}
	this.get = function(key){
		return this.select(key)
	}
	this.undefind = function(value){
		if(typeof(value) == 'undefind') return true
		else if(value == null) return true
		return false
	}
	this.object = function(data){
		if(null == data) return false
		if(typeof(data) == 'object') return true
		return false
	}
	this.empty = function(value){
		if(this.undefind(value)) return true
		else if(value == '') return true
		else if(value == 0) return true
		return false
	}
	this.session = function(name,value)
	{
		if(this.undefind(window.sessionStorage)){this.error('浏览器不支持 sessionStorage')}
		if(this.empty(name)) return null
		if(this.empty(value))	return sessionStorage.getItem(name)
		sessionStorage.setItem(name,value)
		return true
	}
		//loaclstroge 本地存储	2016/4/8
	this.local = function(name,value)
	{
		if(this.undefind(window.localStorage)){this.error('浏览器不支持 localStorage')}
		if(this.empty(name)) return false
		if(this.empty(value))	return localStorage.getItem(name)
		localStorage.setItem(name,value)
		return true
	}
	this.error = function(err){
		if(this.undefind(err)){
			var message = this._error
			if(this.undefind(message)) this._error = '0'
			//	浏览器自动调试输出
			try{console.log(this._error)}catch(e){}
			return this._error
		}else this._error = err
	}
}

var Base64 = {
	// private property
	_keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode: function (input) {
		var output = ""
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4
		var i = 0
		input = this._utf8_encode(input)
		while (i < input.length) {
			chr1 = input.charCodeAt(i++)
			chr2 = input.charCodeAt(i++)
			chr3 = input.charCodeAt(i++)
			enc1 = chr1 >> 2
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4)
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6)
			enc4 = chr3 & 63
			if (isNaN(chr2)) {
				enc3 = enc4 = 64
			} else if (isNaN(chr3)) {
				enc4 = 64
			}
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4)
		}
		return output
	},
 
	// public method for decoding
	decode: function (input) {
		var output = ""
		var chr1, chr2, chr3
		var enc1, enc2, enc3, enc4
		var i = 0
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "")
		while (i < input.length) {
			enc1 = this._keyStr.indexOf(input.charAt(i++))
			enc2 = this._keyStr.indexOf(input.charAt(i++))
			enc3 = this._keyStr.indexOf(input.charAt(i++))
			enc4 = this._keyStr.indexOf(input.charAt(i++))
			chr1 = (enc1 << 2) | (enc2 >> 4)
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2)
			chr3 = ((enc3 & 3) << 6) | enc4
			output = output + String.fromCharCode(chr1)
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2)
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3)
			}
		}
		output = this._utf8_decode(output)
		return output
	},
 
	// private method for UTF-8 encoding
	_utf8_encode:function (string) {
		string = string.replace(/\r\n/g,"\n")
		var utftext = ""
		for (var n = 0; n < string.length; n++) {
			var c = string.charCodeAt(n)
			if (c < 128) {
				utftext += String.fromCharCode(c)
			} else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192)
				utftext += String.fromCharCode((c & 63) | 128)
			} else {
				utftext += String.fromCharCode((c >> 12) | 224)
				utftext += String.fromCharCode(((c >> 6) & 63) | 128)
				utftext += String.fromCharCode((c & 63) | 128)
			}
 
		}
		return utftext
	},
 
	// private method for UTF-8 decoding
	_utf8_decode:function (utftext) {
		var string = ""
		var i = 0
		var c = c1 = c2 = 0
		while ( i < utftext.length ) {
			c = utftext.charCodeAt(i)
			if (c < 128) {
				string += String.fromCharCode(c)
				i++
			} else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1)
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63))
				i += 2
			} else {
				c2 = utftext.charCodeAt(i+1)
				c3 = utftext.charCodeAt(i+2)
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63))
				i += 3
			}
		}
		return string
	}
}
