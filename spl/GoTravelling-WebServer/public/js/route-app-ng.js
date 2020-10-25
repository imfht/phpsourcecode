/**
 * Created by spatra on 15-5-19.
 */

requirejs([
    'angular',
    'routeApp/module',
    'routeApp/services',
    'routeApp/controllers',
    'routeApp/Interceptors',
    'routeApp/directives',
    'routeApp/resourceServices'],
  function(angular, appModule){

    //手动启动AngularJS
    angular.element(document).ready(function(){
      angular.bootstrap(document.getElementById('RouteNgApp'), [appModule.name]);
    });


});