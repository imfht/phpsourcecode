/**
 * Created by kunono on 2015/3/8.
 */
app.service('story',['base','$q',function(base,$q){
    return{
        getImages:getImages,
        getProducts:getProducts,
        get:get
    };

    function getImages(id){
        var json = {};
        json.id = id;
        return base.post('/story/getImages',json,'story getImages');
    }
    function getProducts(sid){
        var json = {};
        json.story_id = sid;
        return base.post('/story/getProducts',json,'story getProducts');
    }
    function get(id){
        var json = {};
        json.id = id;
        return base.post('/story/get',json,'story get');
    }

}]);
