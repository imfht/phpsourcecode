/***
 * 日志记录 - 2016年12月14日 星期三
 * 文件记录方式- 可扩展为数据库扩展
 */

const fs = require('fs'),
      util = require('util')
const cro = require(__dirname+'/conero.js')
const setting = cro.setting(),
      conf = cro.conf()

var dir = (conf.dir.log)? cro.baseDir+conf.dir.log : __dirname+'/log/';
const dt = new Date()
const filename = dt.getFullYear()+"-"+dt.getMonth()+"-"+dt.getDate()

var log = module.exports = {}

// 日志记录器 - 异步
log.report = (text) =>{
    log.write(text,".log",(error) =>{
        if (error){
            console.log('日志记录器-文件写入失败，请检查程序？？')
            throw error
        }        
    })
}
// 调试输出 
log.debug = (data) => {    
    log.write(data,".debug.log",(error) =>{
        if (error){ 
            console.log('调试输出-文件写入失败，请检查程序？？')
            throw error
        }        
    })
}
// 写入工具
log.write = (data,suffix,callback) =>{
    if(data && suffix){
        var text = ""
        if(typeof(data) == "object") text = JSON.stringify(data)
        else text = data
        const file = dir+filename+suffix
        fs.appendFile(file,text,"utf8",callback)
    }
}