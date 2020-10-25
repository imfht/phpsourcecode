/**
 * 2016年12月19日 星期一 网络请求处理
 */
const cro = require(__dirname+'/conero.js')
const url = require('url')
const session = cro.use('session')
var request = module.exports = {}
var headers, req
// 设备常量
const Device = {
    mobile:['Android','iPhone','iPad','Windows Phone'],
    pc:['Windows']
}

// 初始化
request.init = function(httpRequest){
    req = httpRequest
    headers = req.headers
    //console.log(headers)
    //console.log(httpRequest)
    
    // 代码测试
    console.log({
        getIp:request.getIp(),
        getServerIp:request.getServerIp(),
        getHost:request.getHost(),
        getUrl:request.getUrl(),
        isMobile:request.isMobile(),
        parseAgent:request.parseAgent(),
        Query:request.Query(),
        getQuery:request.getQuery(),
        postQuery:request.postQuery()
    })
    
}
// 获取到服务器
request.getHost = function(){
    return headers.host
}
// 获取系统访问地址 url
request.getUrl = function(){
    var reqUrl = req.url
    if(reqUrl) reqUrl = reqUrl.replace(/^\/*/,'')
    reqUrl = reqUrl? reqUrl : ''
    // 首页没有该值
    if(headers.referer) return headers.referer + reqUrl
    return 'http://'+headers.host+'/'+reqUrl
}
// 获取访问IP
request.getIp = function(type){
    if((/(\bsever|\bsv|\bs)/gi).test(type)) return request.getServerIp()
    var ipStr = req.headers['x-forwarded-for'] ||
        req.connection.remoteAddress ||                 //   ::ffff:1.1.1.116
        req.socket.remoteAddress ||                     // ::ffff:1.1.1.116
        req.connection.socket.remoteAddress
    var arr = ipStr.split(':')
    type = type || 'IPv4'
    var ret,IPv4 = arr[arr.length-1]
    if(type == 'IPv4') ret = IPv4
    else if(type == 'IPv6') ret = ipStr.replace(IPv4,'')
    else ret = ipStr
    return ret
}
// 获取服务器的IP地址
request.getServerIp = function(){
    var reqIp = headers.host
    if(reqIp.indexOf(':') > -1){
        var tmpArr = reqIp.split(':')
        reqIp = tmpArr[0]
    }
    return reqIp
}

// 是否为移动设备
request.isMobile = function(agent){
    agent = agent? agent: headers["user-agent"]
    agent = (agent).toLowerCase()
    var mobile = Device.mobile,value
    for(var k in mobile){
        value = (mobile[k]).toLowerCase()
        if(agent.indexOf(value) > -1) return true
    }
    return false
}

/**
 * 解析 Agent 为JSON 2016年12月19日 星期一
 * 小米： Mozilla/5.0 (Linux; U; Android 5.1.1; zh-cn; 2014813 Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/46.0.2490.85 Mobile Safari/537.36 XiaoMi/MiuiBrowser/8.4.4    
 * window： Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0
 *      var str = 'Mozilla/5.0 (Linux; U; Android 5.1.1; zh-cn; 2014813 Build/LMY47V) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/46.0.2490.85 Mobile Safari/537.36 XiaoMi/MiuiBrowser/8.4.4';
 *      str.match(/([a-z\d\.]+)([\/]+)([a-z\d\.\/]+)/gi); // 匹配 s/screen
 *      str.match(/([^\s]+)([\/]+)([^\s]+)/g); // 匹配 s/screen
 *      str.match(/(\()([^\(\)]+)(\))/g); // 匹配 ()    
 */
request.parseAgent = function(agent){
    agent = agent? agent : headers["user-agent"]
    var retArra = {}, list = new Array(), value,tmp
    var reg1 = /(\()([^\(\)]+)(\))/g        // ()
    var strArr1 = agent.match(reg1)
    for(var k in strArr1){
        value = strArr1[k]
        value = value.replace(/(\(|\)|\s)/g,'')
        if(value.indexOf(';') > -1){
            tmp = value.split(';')
            for(var kk in tmp) list.push(tmp[kk])
        }
        list.push(value)
    }

    agent = agent.replace(reg1,'')
    var reg2 = /([^\s]+)([\/]+)([^\s]+)/g   // n/n
    var strArr2 = agent.match(reg2)
    if(strArr2.length > 0){
        for(var k in strArr2){
            value = (strArr2[k]).replace(/\//,':')  // 只替换第一个下划线
            tmp = value.split(':')
            if(tmp.length == 2) retArra[tmp[0]] = tmp[1]
            else list.push(strArr2[k])
        }
        /*
        var tmpJsonStr = strArr2.join('","')
        tmpJsonStr = '{"'+tmpJsonStr.replace(/\//g,'":"')+'"}'
        retArra = JSON.parse(tmpJsonStr)
        */
    }

    agent = agent.replace(reg2,'')

    retArra['list'] = list
    retArra['left'] = agent.replace(/\s/g,'')
    return retArra
}

// 请求地址
request.Query = function(){
    var data = {'__GET':null,'__POST':null};
    try{
        if(req.method == "GET"){
            data['__GET'] = url.parse(req.url,true).query;
            data['method'] = 'GET';
        }else{
            var postdata = "";
            var POST = {};
            req.addListener("data",function(postchunk){
                postdata += postchunk;
            });
            req.addListener("end", function () {
                POST = query.parse(postdata);
                data['__POST'] = POST;// 方式1)，2)方法无法将异步数据回写
                //console.log(POST);
            });
            //data['__POST'] = POST;// 方式2)
            data['method'] = 'POST';
        }
        /*
        //  session值绑定
        req['session'] = this.session(req,res);
        var S = req['session'];
        data['__SESSION'] = S.toJson();
        */
    }catch(e){console.log(e);}
    return data;
}
// $_GET
request.getQuery = function(){
    var data = request.Query()
    return data['__GET']
}
// $_POST
request.postQuery = function(){
    var data = request.Query()
    return data['__POST']
}