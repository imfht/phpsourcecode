/**
 * Created by kunono on 2015/2/28.
 */
app.service('comment',['log','base',function(log,base){
    return{
        make:make
    };
    function make(product_id,content,rating,upload_file_names,upload_file_limit_id){
        var json = {};
        json.product_id = product_id;
        json.content = content;
        json.rating = rating;
        json.upload_file_names = upload_file_names;
        json.upload_file_limit_id = upload_file_limit_id;
        log.log(json);
        return base.post('/comment/make',json,'comment make');
    }
}]);
