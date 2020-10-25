/*
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-24 00:08:45
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-24 00:08:45
 */


//笛卡尔积算法
function descartes(array){
   
    if( array.length < 2 ) return array[0] || [];
    return [].reduce.call(array, function(col, set) {
        var res = [];
        col.forEach(function(c) {
            set.forEach(function(s) {
                var t = [].concat( Array.isArray(c) ? c : [c] );
                t.push(s);
                res.push(t);
        })});
        return res;
    });
}

export default   descartes