function GoodCart(product){
  var Cart = wx.getStorageSync('cart');
  //console.log("这是获得的购物车的数据"+Cart);
 // console.log(product);
  if (Cart == undefined || Cart == '') {
    var jsonstr =
      {
        "productlist": [
          {
            "id": product.id,
            "name": product.name,
            "num": product.num,
            "price": product.price,
            "imgs": product.imgs,
            "mprice": product.saleprice,
            "sname":product.sname
          }
        ],
        "totalNumber": product.num,
        "totalAmount": (product.saleprice *  product.num),
        "postprice":product.postprice
      };
   // console.log("第一次点击过后的数据"+JSON.stringify(jsonstr));
    //将数据保存到本地
    wx.setStorageSync('cart', jsonstr)
   
    console.log(wx.getStorageSync('cart'));
  }else{
    var jsonstr = Cart;

    var productlist = jsonstr.productlist;
    var result = false;
    //取出配对
    for (var i in productlist) {
      if (productlist[i].id == product.id) {
        productlist[i].num = parseInt(productlist[i].num) + 1;
        result = true;
      }
    }
    if (!result) {

      productlist.push({ "id": product.id, "name": product.name, "num": product.num, "price": product.price, 'imgs': product.imgs, 'mprice': product.saleprice,'sname':product.sname });
    }

    jsonstr.totalNumber = parseInt(jsonstr.totalNumber) + parseInt(product.num);
    jsonstr.totalAmount = (parseFloat(jsonstr.totalAmount) + (parseInt(product.num) * parseFloat(product.saleprice))).toFixed(2);
    wx.setStorageSync('cart', jsonstr)
   
    //console.log(wx.getStorageSync('cart'));
  }
  
}

var UpdataNum = function (id) {
  var ShopingCart = wx.getStorageSync('cart');



  var jsonstr = ShopingCart;
  var productlist = jsonstr.productlist;
  var list = [];
  for (var i in productlist) {
    if (productlist[i].id == id) {
      jsonstr.totalNumber = parseInt(jsonstr.totalNumber) - 1;
      jsonstr.totalAmount = (parseFloat(jsonstr.totalAmount) - parseFloat(productlist[i].mprice)).toFixed(2);
      productlist[i].num = parseInt(productlist[i].num) - 1;
    }

    if (parseInt(productlist[i].num) == 0) {
      delete productlist[i];
    } else {
      list.push(productlist[i]);
    }


  }
  jsonstr.productlist = list;
  if (jsonstr.totalNumber == 0) {
    wx.removeStorageSync('cart')
    

  } else {
   
    wx.setStorageSync('cart', jsonstr)
  }
  //console.log(wx.getStorageSync('cart'));
}

var AddCart = function(id){
  var ShopingCart = wx.getStorageSync('cart');
  var jsonstr = ShopingCart;
  var productlist = jsonstr.productlist;
  var list = [];
  for (var i in productlist) {
    if (productlist[i].id == id) {
      jsonstr.totalNumber = parseInt(jsonstr.totalNumber) + 1;
      jsonstr.totalAmount = (parseFloat(jsonstr.totalAmount)+ parseFloat(productlist[i].mprice)).toFixed(2);
      productlist[i].num = parseInt(productlist[i].num) + 1;
    }
    list.push(productlist[i]);
   }
  jsonstr.productlist = list;
  wx.setStorageSync('cart', jsonstr)
 
}

module.exports = {
  GoodCart: GoodCart,
  UpdataNum: UpdataNum,
  AddCart: AddCart
}