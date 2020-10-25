/**
 * Created by kunono on 2015/3/9.
 */
app.controller('adminCategoryCtrl',['adminCategory','category','component_uploadFile',function(adminCategory,category,component_uploadFile){
    var vm = this;
    vm.hasPermissionCategory = false;
    adminCategory.hasPermission().then(function(data){
        vm.hasPermissionCategory = data.hasPermission;
    });
    component_uploadFile.init();
    component_uploadFile.multiple = false;
    vm.refresh = refresh;
    refresh();
    function refresh(){
        vm.mode="add";
        vm.currentCategory = {};
        category.all().then(function(data){
            vm.categories = data.categories;
        })
    }
    vm.setCurrentCategory = setCurrentCategory;
    function setCurrentCategory(category){
        vm.currentCategory = category;
    }
    vm.saveEdit = saveEdit;
    function saveEdit(){
        if(vm.mode == 'edit'){
            adminCategory.edit(vm.currentCategory.id,vm.currentCategory.name).then(function(success){
                refresh();
            });
        }
        if(vm.mode == 'add'){
            adminCategory.add(vm.currentCategory.name).then(function(success){
                refresh();
            })
        }
    }
    vm.remove = remove;
    function remove(){
        adminCategory.remove(vm.currentCategory.id).then(function(success){
            refresh();
        })
    }
    vm.setImage = setImage;
    function setImage(){
        component_uploadFile.upload().then(function(success){
            if(success.files.length == 1){
                adminCategory.setImage(vm.currentCategory.id,success.files[0].upload_file_name).then(function(success){
                    refreshImage(vm.currentCategory);
                })
            }

        });
    }
    vm.removeImage = removeImage;
    function removeImage(){
        adminCategory.removeImage(vm.currentCategory.id).then(function(success){
            refreshImage(vm.currentCategory);
        })
    }
    function refreshImage(categoryt){
        category.getImage(categoryt.id).then(function(data){
            categoryt.image = data.image;
        })
    }
}]);
