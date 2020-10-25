//当前配置文件不需要修改（参数在ext.json中）
let extConfig = wx.getExtConfigSync ? wx.getExtConfigSync() : {}
module.exports = {
    name: extConfig.name,
    siteroot: extConfig.attr.host,
    miniapp: extConfig.attr.miniapp,
};