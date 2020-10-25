// node 原生包
const http = require('http')

// 自定义包
const setting = require('./config/setting.json'),
    cro = require('./modules/conero.js')
const log = cro.use('log')    
const router = cro.use("router")

const wsport = setting.wsport   // webserver 服务器端口
const hsport = setting.hsport   // httpserver 端口号
/*
const server = http.createServer()
const io = cro.local('socket.io')(server)
io.on('connection', function(client){
  client.on('event', function(data){
      console.log(data)
  });
  client.on('disconnect', function(){});
});
server.listen(wsport);
*/

// 服务器app
const socketio = http.createServer(function(req,res){
    router.main(req,res)
    //log.debug(JSON.stringify(req))
    //res.end() // Mustache 模块内嵌入 end
})
const io = cro.local('socket.io')(socketio)
io.on('connection', function(client){
    console.log("io 连接成功")
    //client.emit('news',{emma:'come on,emma'})
    client.on('event', function(data){
        console.log(data)
    });
    // 信息接收
    client.on('news',function(news){
        console.log("news:"+news)
        io.emit("news",news+", I got it Jone")
    })
    client.on('disconnect', function(){});
});
socketio.listen(hsport)


reportStart()
function reportStart(){
    const dt = new Date()
    const report = 
`
 >> NodeServer for Conero.PHP    
 >> @start - 2016年12月14日 星期三    
 >> @author - Joshua Coenro Doeeking Yang  
 -- 0.0.0.0:${wsport}端口号的 Webserve 服务器正在运行....
 -- 0.0.0.0:${hsport}端口号的 Httpserver 服务器正在运行....
 -- 时间：${dt}
 -- right: @Coenro
`
    console.log(report)
    log.report(report)
}