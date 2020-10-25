/**
 * Created by rockyren on 15/4/22.
 */

requirejs.config({
  //指向public目录
  baseUrl: '/public',
  paths: {
    jquery: 'packages/bower/jquery/dist/jquery',
    bootstrap: 'packages/bower/bootstrap/dist/js/bootstrap.min'
  },
  shim: {
    'bootstrap': {
      deps: ['jquery'],
      exports: 'bootstrap'
    }
  }
});

requirejs(['jquery', 'bootstrap'], function($){

  $(document).ready(function(){
    $('.authority-error button').click(function(){
      $(this).hide();
    })
  });

});