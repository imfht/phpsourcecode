/**
 * Created by spatra on 15-5-23.
 */

define(['routeApp/module'], function(module){

  module.factory('AuthInterceptor', ['$window', '$q', function($window, $q){

    return {
      responseError: function(rejection){
        if( rejection.status === 401 ){
          $window.location.reload();
        } else {
          return $q.reject(rejection);
        }
      }
    };
  }]);

  return module;
});