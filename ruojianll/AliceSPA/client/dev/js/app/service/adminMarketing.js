/**
 * Created by kunono on 2015/3/12.
 */

app.service('adminMarketing',['base',function(base){
    return {
        hasPermission:hasPermission,
        addBanner:addBanner,
        editBanner:editBanner,
        removeBanner:removeBanner,
        setBannerImage:setBannerImage,
        removeBannerImage:removeBannerImage

    };
    function hasPermission(){
        return base.post('/admin/hasPermission/marketing',{},'admin hasPermission marketing');
    }
    function addBanner(title,subtitle,type,value){
        var json = {};
        json.title = title;
        json.subtitle = subtitle;
        json.type = type;
        json.value = value;
        return base.post('/admin/marketing/banner/add',json,'admin marketing banner add');
    }

    function editBanner(id,title,subtitle,type,value){
        var json = {};
        json.id = id;
        json.title = title;
        json.subtitle = subtitle;
        json.type = type;
        json.value = value;
        return base.post('/admin/marketing/banner/edit',json,'admin marketing banner edit');
    }
    function removeBanner(id){
        var json = {};
        json.id = id;
        return base.post('/admin/marketing/banner/remove',json,'admin marketing banner remove');
    }
    function setBannerImage(id,upload_file_name){
        var json = {};
        json.id = id;
        json.upload_file_name = upload_file_name;
        return base.post('/admin/marketing/banner/setImage',json,'admin marketing banner setImage');
    }
    function removeBannerImage(id){
        var json = {};
        json.id = id;
        return base.post('/admin/marketing/banner/removeImage',json,'admin marketing banner removeImage');
    }
}
]);
