/***
 * web 路由器 - api  - 2016年12月14日 星期三
 */
const fs = require("fs")
const cro = require(__dirname+'/conero.js')
const mime = cro.local('mime'),
      setting = cro.setting(),
      view = cro.use('view'),
      log = cro.use('log'),
      request = cro.use('request')
// 路由器配置参数
const routerConf = setting.router
var router = module.exports = {}
// 主函数 - 返回 {html:string,error:0}
router.main = function(req,res){
    var page = router.index(req.url)
    res.writeHead(200,{'Content-Type': mime.lookup(page)})
    var baseDir = cro.baseDir+cro.conf("dir").application
    var file = cro.baseDir + req.url
    console.log(file,page)
    var name = ""
    // 页面存在时
    var isServerScript = false
    var existPage = function(){
        //var sfl = fs.fstatSync(file)
        //if(!sfl.isFile() && (page.indexOf('.html') > -1 || page.indexOf('.') == -1)){// 一般不真正目录下的文件
        if(router.formatCheck(page) || page.indexOf('.') == -1){// 一般不真正目录下的文件
            request.init(req)
            isServerScript = true  
            file = baseDir+(page.indexOf('.html') > -1? page.replace('.html','.js') : page.replace('.','.js'))
            view.init({name:page})
            var data = require(file).main(req,res,{view:view,cro:cro})
            data = typeof(data) == "object"? data:{}
            view.fetch(res,data)
        }
        else if(fs.existsSync(file)){// 文件存在时，直接访问文件
            /*
            res.write(fs.readFileSync(file).toString())
            res.end()
            */
            fs.readFile(file,function(err,content){
                //res.write(content)
                res.end(content)
            })
        }
        else res.end()
        /*
        else{
            // 错误页面
            file = baseDir+setting.error+".js"
            view.init({name:'error.html'})
            var data = require(file).main(req,res,{view:view})
            data = typeof(data) == "object"? data:{}
            view.fetch(res,data)
        }
        */
    }
    // 页面不存在
    var unExistPage = function(e){
        var report = ""
        log.report(e.message)
        console.log(e.message)
    }

    // 开发模式
    if(setting.release == "DEVELOPMENT"){
        existPage()
        //  删除-require缓存
        if(isServerScript == true) {
            delete require.cache[require.resolve(file)]
            isServerScript = false
        }
    }
    else{
        try {existPage()} catch (error) {unExistPage(error)}
    }
}

//  默认-index文件检测
router.index = function(urlStr,type){
    type = typeof(type) == 'undefined'? 'index.html':type
    //if(url.charAt(url.length-1) != '/') return url;
    if(urlStr == '/') return urlStr+type;
    var arr = urlStr.split('/')
    var len = arr.length-1
    if(arr[len] == '') urlStr += type
    return urlStr
}
// 文件格式检测
router.formatCheck = function(text){
    var suffix = routerConf.suffix
    if(suffix){
        var regText = ""
        if(typeof(suffix) == 'string' || (typeof(suffix) == 'object' && suffix.length == 1)){
            regText = "\.("+(typeof(suffix) == 'string'? regText:suffix[0])+")"
            var reg = new RegExp(regText,'gi')
            return reg.test(text)
        }
        else if(typeof(suffix) == 'object' && suffix.length > 1){
            regText = "\\.(" + suffix.join("|") + ")"
            var reg = new RegExp(regText,'gi')
            return reg.test(text)
        }
    }
    return false
}