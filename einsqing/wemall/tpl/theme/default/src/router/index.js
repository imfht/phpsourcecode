import Vue from 'vue'
import Router from 'vue-router'


Vue.use(Router)

import login from 'components/login'
import register from 'components/register'
import forgetPassword from 'components/forgetPassword'
import resetPassword from 'components/resetPassword'
import home from 'components/home'
import header from 'components/header'
import footer from 'components/footer'
import productDetail from 'components/productDetail'
import cart from 'components/cart'
import checkoutSuccess from 'components/checkoutSuccess'
import myorder from 'components/myorder'
import user from 'components/user'
import voucher from 'components/voucher'
import exchange from 'components/exchange'
import recharge from 'components/recharge'
import scanCourse from 'components/scanCourse'
import address from 'components/address'
import addAddress from 'components/addAddress'
import editAddress from 'components/editAddress'
import addVoucher from 'components/addVoucher'


export default new Router({
  routes: [
    {
      path: '/login',
      component: login
    },
    {
      path: '/register',
      component: register
    },
    {
      path: '/forgetPassword',
      component: forgetPassword
    },
    {
      path: '/resetPassword',
      component: resetPassword
    },
    {
      path: '/home',
      component: home
    },
    {
      path: '/header',
      component: header
    },
    {
      path: '/footer',
      component: footer
    },
    {
      path: '/productDetail',
      component: productDetail
    },
    {
      path: '/cart',
      component: cart,
      children:[{
	      path: '/addVoucher',
	      component: addVoucher
      }]
    },
    {
      path: '/checkoutSuccess',
      component: checkoutSuccess
    },
    {
      path: '/myorder',
      component: myorder
    },
    {
      path: '/user',
      component: user
    },
    {
      path: '/voucher',
      component: voucher
    },
    {
      path: '/exchange',
      component: exchange
    },
    {
      path: '/recharge',
      component: recharge
    },
    {
      path: '/scanCourse',
      component: scanCourse
    },
    {
      path: '/address',
      component: address
    },
    {
      path: '/addAddress',
      component: addAddress
    },
    {
      path: '/editAddress',
      component:editAddress
    },
//  {
//    path: '/addVoucher',
//    component: addVoucher
//  },
    {
      path: '*',
      component: home
    }
  ],
})
