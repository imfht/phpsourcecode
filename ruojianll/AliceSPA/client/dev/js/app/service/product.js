/**
 * Created by kunono on 2015/2/28.
 */

app.service('product',['log','base','$q',function(log,base,$q) {
    return {
        all: all,
        get: get,
        all_noImage:all_noImage,
        getProductImages:getProductImages,
        getStories:getStories
    };
    function all() {
        var defer = $q.defer();
        return base.get('/product/all_image', 'product all_image');
    }

    function get(id) {
        var url = '/product/id-' + id + '_image';
        return base.get(url, 'product id_image');
    }
    function all_noImage(){
        return base.get('/product/all','product all');

    }
    function getProductImages(product_id){
        return base.get('/product/id-'+product_id+'/image/all','product id- image all');
    }
    function getStories(id,hasContent){
        var json = {};
        json.id = id;
        json.hasContent = hasContent;
        return base.post('/product/story/all',json,'product story all');
    }

}]);