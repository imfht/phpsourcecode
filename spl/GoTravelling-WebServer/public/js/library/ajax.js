/**
 * Created by spatra on 15-4-20.
 */

define(['library/deferred'], function(deferred){
  //针对IE实现兼容性处理
  var XMLHttpRequest = null;
  if( window.XMLHttpRequest === undefined ){
    XMLHttpRequest = function(){
      try{
        return new ActiveXObject('Msml2.XMLHTTP.6.0');
      }catch (error){
        try{
          return new ActiveXObject('Msml2.XMLHTTP.3.0');
        }catch (err){
          throw new Error('XMLHttpRequest is not supported');
        }
      }
    };
  }
  else{
   XMLHttpRequest = window.XMLHttpRequest;
  }

  //全局的配置对象
  var globalConfig = {};

  var send = function(config){
    var defer = deferred.build();
    var request = new XMLHttpRequest();

    request.open(config['method'], config['url']);

    if( globalConfig['headers'] ){
      for(var prop in globalConfig['headers'] ){
        if( globalConfig['headers'].hasOwnProperty(prop) ){
          request.setHeaders(prop, globalConfig['headers'][prop]);
        }
      }
    }

    request.send(config['data'] || null);

    request.onreadystatechange = function(){
      if( request.readyState === 4 && (request.status <= 209 && request.status >= 200 ) ){
        if( config['type'] === 'json' ){
          defer.resolve(JSON.parse(request.responseText));
        }
        else if( config['type'] === 'dom' ){
          defer.resolve(request.responseXML);
        }
        else{
          defer.resolve(request.responseText);
        }
      }
      else{
        defer.reject(request.responseText);
      }
    };

    var successCallback = config['success'],
      failureCallback = config['failure'];

    if( successCallback || failureCallback ){
      return defer.promise.then(successCallback, failureCallback);
    }
    else{
      return defer.promise;
    }

  };

  var setHeaders = function(key, value){
    if( !globalConfig.hasOwnProperty('headers') ){
      globalConfig['headers'] = {};
    }

    globalConfig['headers'][key] = value;
  };

  return {
    send: send,
    setHeaders: setHeaders
  }
});