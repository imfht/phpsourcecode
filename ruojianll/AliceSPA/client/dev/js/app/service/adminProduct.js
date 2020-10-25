/**
 * Created by kunono on 2015/3/5.
 */
app.service('adminProduct',['log','base','$q',function(log,base,$q){
    return {
        hasPermission:hasPermission,
        edit:edit,
        addImages:addImages,
        removeImage:removeImage,
        add:add,
        remove:remove,
        setCategory:setCategory
    };
    function hasPermission(){
        var defer = $q.defer();
        base.post('/admin/hasPermission/product',{},'admin hasPermission product').then(function(success){
            defer.resolve(success.hasPermission);
        },function(err){
            defer.reject(null);
        });
        return defer.promise;
    }
    function edit(id,category_id,name,number,price,old_price,comment,summary){
        var json = {};
        json.id = id;
        json.category_id = category_id;
        json.name = name;
        json.number = number;
        json.price = price;
        json.old_price = old_price;
        json.comment = comment;
        json.summary = summary;
        return base.post('/admin/product/edit',json,'admin product edit');
    }
    function addImages(product_id,upload_file_names){
        var json = {};
        json.product_id = product_id;
        json.upload_file_names = upload_file_names;
        return base.post('/admin/product/addImages',json,'admin product addImages');
    }
    function removeImage(product_id,upload_file_name){
        var json = {};
        json.product_id = product_id;
        json.upload_file_name = upload_file_name;

        return base.post('/admin/product/removeImage',json,'admin product removeImage');
    }
    function add(category_id,name,number,price,old_price,comment,summary){
        var json = {};
        json.category_id = category_id;
        json.name = name;
        json.number = number;
        json.price = price;
        json.old_price = old_price;
        json.comment = comment;
        json.summary = summary;
        return base.post('/admin/product/add',json,'admin product add');
    }
    function remove(id){
        var json = {};
        json.id = id;
        return base.post('/admin/product/remove',json,'admin product remove');
    }
    function setCategory(product_id,category_id){
        var json = {};
        json.product_id = product_id;
        json.category_id = category_id;
        return base.post('/admin/product/setCategory',json,'admin product setCategory');
    }
}]);

