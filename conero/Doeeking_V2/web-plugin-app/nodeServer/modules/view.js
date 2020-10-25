/***
 * web 视图处理器 - api  - 2016年12月14日 星期三
 */
const cro = require(__dirname+'/conero.js')
const mu2 = cro.local('mu2'),
      setting = cro.setting(),
      dir = cro.conf("dir")

const viewConf = setting.view
const root = cro.baseDir + dir.application+"view"

var view = module.exports = {}

var viewTplName = ""

// option = {name*; }
view.init = function(option){
    viewTplName = option.name
    var suffix = viewConf.suffix
    if(viewTplName.indexOf(suffix) == -1) viewTplName += '.'+suffix
    viewTplName = viewTplName.replace(/^\//,'')
}
// 自定义模板名称
view.setViewTpl = function(name){
    if(name) viewTplName = name
}
// 视图渲染
view.fetch = function(res,data){
    mu2.root = root    
    // 删除模板缓存
    if(!viewConf.view_cache) mu2.clearCache()
    var stream = mu2.compileAndRender(viewTplName, data)
    stream.pipe(res)
    
}