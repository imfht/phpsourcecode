/**
 * Created by spatra on 15-4-20.
 */

define(function(){
  /**
   *
   * @param resolver
   * @constructor
   */
  function Promise(resolver){
    var queue = [];
    resolver(resolve, reject);

    /**
     *
     * @param x
     */
    function resolve(x){
      next(0, x);
    }

    /**
     *
     * @param reason
     */
    function reject(reason){
      next(1, reason);
    }

    /**
     *
     * @param index
     * @param value
     */
    function next(index, value){
      setTimeout(function(){
        while( queue.length ){
          var item = queue.shift();

          if( typeof item[index] === 'function' ){
            try{
              var chain = item[index](value);
            }catch(e){
              return reject(e);
            }

            if( chain && typeof chain.then === 'function' ){
              return chain.then(resolve, reject);
            }
            else{
              return Promise.resolved(chain).then(resolve, reject);
            }
          }
        }
      }, 0);
    }

    /**
     *
     * @type {Function}
     */
    this.chain = this.then = function(resolve, reject){
      queue.push([resolve, reject]);
      return this;
    };

    /**
     *
     * @param reject
     * @returns {*}
     */
    this['catch'] = function(reject){
      return this.then(undefined, reject);
    };
  }

  /**
   *
   * @type {Function}
   */
  Promise.resolved = Promise.cast = function(x){
    return new Promise(function(resolve){
      resolve(x);
    });
  };

  /**
   *
   * @param reason
   * @returns {Promise}
   */
  Promise.rejected = function(reason){
    return new Promise(function(resolve, reject){
      reject(reason);
    });
  };

  /**
   *
   * @param values
   * @returns {d.promise|promise|Promise|m.ready.promise}
   */
  Promise.all = function(values) {
    var defer = Promise.deferred();
    var len = values.length;
    var results = [];

    values.forEach(function(item, index) {
      item.then(function(x) {
        results[index] = x;
        len--;
        if (len === 0) {
          defer.resolve(results);
        }
      }, function(r) {
        defer.reject(r)
      })
    });

    return defer.promise;
  };

  Promise.deferred = function(){
    var result = {};

    result.promise = new Promise(function(resolve, reject){
      result.resolve = resolve;
      result.reject = reject;
    });

    return result;
  };

  return {
    build: Promise.deferred,
    Promise: Promise
  };
});