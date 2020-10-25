var AjaxUrl = "http://www.icqkx.cn/weixin";
var Ajax_Home = AjaxUrl + "/index";
var Ajax_Home_Good_List = AjaxUrl + "/index/cate";
var Ajax_Wxlogin = AjaxUrl+"/user/wxlogin";
var Ajax_Wxlogin_Openid = AjaxUrl + "/user/getOpenId";
var Ajax_User_address = AjaxUrl+"/user/address";
var Ajax_User_Add_address = AjaxUrl+"/address/add";
var Ajax_User_Order = AjaxUrl+"/order/index";
var Ajax_Pay_Order = AjaxUrl +"/pay/payOrder";
var Ajax_mall_config = AjaxUrl+"/good/setting";
var Ajax_Order_goods = AjaxUrl +"/order/orderlist";
var Ajax_User_SelectAddress = AjaxUrl+"/user/selectaddress";
var Ajax_User_showOrder=AjaxUrl+"/order/userorder";
var Ajax_User_Order_All=AjaxUrl+"/order/userallorder";
var Ajax_Good_Info = AjaxUrl+"/good/infos";
var Ajax_User_Code = AjaxUrl+"/user/sendcode";
var Ajax_User_Login = AjaxUrl +"/user/wxapplogin";
var Ajax_User_Order_List = AjaxUrl+"/order/userorderlist"
module.exports = {
  Ajax_Home: Ajax_Home,
  Good_Cate_list: Ajax_Home_Good_List,
  Wxlogin: Ajax_Wxlogin,
  OpenId: Ajax_Wxlogin_Openid,
  Uaddress: Ajax_User_address,
  Adaddress: Ajax_User_Add_address,
  Order:Ajax_User_Order,
  PayOrder: Ajax_Pay_Order,
  MallConfig: Ajax_mall_config,
  OrderList: Ajax_Order_goods,
  SelectAddress: Ajax_User_SelectAddress,
  ShowOrders: Ajax_User_showOrder,
  AllOrders: Ajax_User_Order_All,
  GoodInfo: Ajax_Good_Info,
  UserCode:Ajax_User_Code,
  UserLogin: Ajax_User_Login,
  OrderUserList: Ajax_User_Order_List
}
