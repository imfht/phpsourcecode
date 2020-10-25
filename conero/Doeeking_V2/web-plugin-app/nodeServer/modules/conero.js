/***
 * web 协助类 - api  - 2016年12月14日 星期三
 * NodeJs: v6.9.1
 */
const fs = require('fs')

// loacal/global 为Windows系统下无法用 require 加载的模块函数补丁
var app_dir = __dirname.replace('\\modules','')
exports.local = (file) => {
    var sys = conf('sys'),
        prefix
    if(sys && sys.local) prefix = sys.local
    else prefix = ''
    //console.log(prefix)
    if(fs.existsSync(prefix+file)){
        return require(prefix+file)
    }
    // 目录或文件不存在是用原始的方法
    return require(file)
}
exports.global = (file) => {
    var sys = conf('sys'),
        prefix
    if(sys && sys.global) prefix = sys.global
    else prefix = ''
    if(fs.existsSync(prefix+file)){
        return require(prefix+file)
    }
    // 目录或文件不存在是用原始的方法
    return require(file)
}
// 接口读取
exports.conf = (key) => {
    return conf(key)
}
// setting
exports.setting = (key) => {
    const setting = require(app_dir + conf('setting'))
    if(key){
        if(setting[key]) return setting[key]
        return ''
    }
    return setting
}
// app 引入机制
exports.use = (name,dir) =>{
    //const app_dir = conf("dir")
    
    const baseDir = __dirname.replace('\\modules','')
    dir = baseDir+(dir? dir :"/modules/")
    var file = dir+name+".js"
    return require(file)
}
exports.baseDir = app_dir
//  读取配置文件
function conf(key){    
    var dir = __dirname
    json = require(dir.replace('\\modules','')+'/package.json')
    if(key){
        if(json[key]) return json[key]
        return ''
    }
    return json
}