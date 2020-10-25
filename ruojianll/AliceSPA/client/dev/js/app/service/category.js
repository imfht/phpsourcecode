/**
 * Created by kunono on 2015/3/9.
 */
app.service('category',['base',function(base){
    return {
        all:all,
        getImage:getImage,
        getProductLimit:getProductLimit,
        get:get
    };
    function all(){
        return base.get('/category/all','category all');
    }
    function getImage(cid){
        var json = {};
        json.id = cid;
        return base.post('/category/image',json,'category getImage');
    }
    function getProductLimit(catid,limit){
        return base.get('/category/product/category_id-'+catid+'_limit-'+limit,'/category/product/category_id-limit-');
    }
    function get(id){
        return base.get('/category/id-'+id,'/category/id-');
    }
}]);
