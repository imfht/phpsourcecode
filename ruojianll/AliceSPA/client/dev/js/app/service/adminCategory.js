/**
 * Created by kunono on 2015/3/9.
 */
app.service('adminCategory',['base','$q',function(base,$q){
    return{
        hasPermission:hasPermission,
        edit:edit,
        add:add,
        remove:remove,
        setImage:setImage,
        removeImage:removeImage
    };

    function hasPermission() {
        return base.post('/admin/hasPermission/category', {}, 'admin hasPermission category');
    }
    function edit(id,name){
        var json = {};
        json.id = id;
        json.name = name;
        return base.post('/admin/category/edit',json,'admin category edit');
    }
    function add(name){
        var json = {};
        json.name = name;
        return base.post('/admin/category/add',json,'admin category add');
    }
    function remove(id){
        var json = {};
        json.id = id;
        return base.post('/admin/category/remove',json,'admin category remove');
    }
    function setImage(category_id,upload_file_name){
        var json = {};
        json.category_id = category_id;
        json.upload_file_name = upload_file_name;
        return base.post('/admin/category/setImage',json,'admin category setImage');
    }
    function removeImage(cid){
        var json = {};
        json.id = cid;
        return base.post('/admin/category/removeImage',json,'admin category removeImage');
    }
}]);

