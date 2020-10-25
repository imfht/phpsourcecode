/**
 * Created by rockyren on 14/11/4.
 */

requirejs.config({
  baseUrl: '/',
  paths: {
    'jquery': 'packages/bower/jquery/dist/jquery',
    'bootstrap': 'packages/bower/bootstrap/dist/js/bootstrap.min',
    'guide': 'js/guide',
    'common_lib': 'js/common/common_lib'
  },
  shim: {
    'bootstrap': {
      deps: ['jquery'],
      exports: 'bootstrap'
    }
  }
});

requirejs(['jquery','bootstrap','guide/guide-common'],function($,bootstrap,common){
  common.run();
});