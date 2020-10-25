/**
 * Created by rockyren on 14-10-25.
 */
requirejs.config({
  //相对于public目录
  baseUrl: '/',
  paths:{
    'home': 'js/home',
    'jquery': 'packages/bower/jquery/dist/jquery',
    'bootstrap': 'packages/bower/bootstrap/dist/js/bootstrap.min',
    'common_lib': 'js/common/common_lib'
  },

  shim: {
    'bootstrap': {
      deps: ['jquery'],
      exports: 'bootstrap'
    }
  }

});

requirejs(['jquery','bootstrap','home/home-common'],function($,bootstrap,common){
  common.run();
});