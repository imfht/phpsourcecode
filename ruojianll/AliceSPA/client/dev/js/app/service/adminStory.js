/**
 * Created by kunono on 2015/3/8.
 */
app.service('adminStory',['base','$q',function(base,$q){
    return{
        all:all,
        hasPermission:hasPermission,
        add:add,
        edit:edit,
        addImages:addImages,
        removeImage:removeImage,
        addProduct:addProduct,
        removeProduct:removeProduct,
        remove:remove
    };
    function hasPermission(){
        var defer = $q.defer();
        base.post('/admin/hasPermission/story',{},'admin hasPermission story').then(function(success){
            defer.resolve(success.hasPermission);
        },function(err){
            defer.reject(null);
        });
        return defer.promise;
    }

    function add(title,content){
        var json = {};
        json.title = title;
        json.content = content;
        return base.post('/admin/story/add',json,'admin story add');
    }

    function edit(id,title,content){
        var json = {};
        json.id = id;
        json.title = title;
        json.content = content;
        return base.post('/admin/story/edit',json,'admin sotry edit');
    }
    function addImages(id,upload_file_names){
        var json = {};
        json.story_id = id;
        json.upload_file_names = upload_file_names;
        return base.post('/admin/story/addImages',json,'admin story addImages');
    }
    function removeImage(id,upload_file_name){
        var json = {};
        json.story_id = id;
        json.upload_file_name = upload_file_name;
        return base.post('/admin/story/removeImage',json,'admin story removeImage');
    }
    function addProduct(sid,pid){
        var json={};
        json.story_id = sid;
        json.product_id = pid;
        return base.post('/admin/story/addProduct',json,'admin story addProduct');
    }
    function removeProduct(sid,pid){
        var json={};
        json.story_id = sid;
        json.product_id = pid;
        return base.post('/admin/story/removeProduct',json,'admin story removeProduct');
    }
    function remove(id){
        var json = {};
        json.id = id;
        return base.post('/admin/story/remove',json,'admin story remove');
    }
    function all(){
        return base.post('/admin/story/all',{},'admin story all');
    }
}]);
