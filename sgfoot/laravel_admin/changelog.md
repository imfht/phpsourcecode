# CHANGE LOG 

## 2018/04/25 v1.0
- 解决vip-admin点击菜无效问题
```html
vip_common.js 100~128间之间 
window.addTab(elem, $(this).html(), this.getAttribute('href-url'));    
```
- 解决菜单名若一样,url不一样,无法显示问题.以url做为唯一