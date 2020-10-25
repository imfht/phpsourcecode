// 首页
exports.main = (req,res,option) => {    
    const view = option.view
    const cro = option.cro
    const qr = cro.local("qr-image")
    var data = {}
    data.emma = "emma is test by "+ Math.random()*1000
    data.qrtest = qr.imageSync('well, 你说呢， 告诉下一步：'+Math.random()+'Englis - woow'+Math.random(), { type: 'svg' })
    return data
}