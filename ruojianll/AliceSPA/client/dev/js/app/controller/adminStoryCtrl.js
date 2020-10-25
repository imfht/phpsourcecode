/**
 * Created by kunono on 2015/3/8.
 */
app.controller('adminStoryCtrl',['story','adminStory','log','component_uploadFile','product',function(story,adminStory,log,component_uploadFile,product){
    var vm = this;
    vm.hasPermissionStory = false;
    adminStory.hasPermission().then(function(flag){
        vm.hasPermissionStory = flag;
    });
    component_uploadFile.init();
    vm.mode = 'add';
    vm.currentStory = {};
    vm.refresh = refresh;
    vm.refresh();
    function refresh(){
        adminStory.all().then(function(success){
            vm.currentStory = {};
            vm.mode="add";
            vm.stories = success.stories;
        });
    }


    vm.saveEdit = saveEdit;
    function saveEdit(){
        if(vm.mode == 'add'){
            adminStory.add(vm.currentStory.title,vm.currentStory.content).then(function(success){
                vm.refresh();
            });
        }
        if(vm.mode == 'edit'){
            adminStory.edit(vm.currentStory.id,vm.currentStory.title,vm.currentStory.content).then(function(success){
                refresh();
            });
        }
    }
    vm.setCurrentStory = setCurrentStory;
    function setCurrentStory(story){
        vm.currentStory = story;
        refreshImages(vm.currentStory);
        refreshProducts(vm.currentStory);
    }
    vm.addImages = addImages;
    function addImages(){
        component_uploadFile.upload().then(function(success){
            var t= _.map(success.files,function(file){
               return file.upload_file_name;
            });
            adminStory.addImages(vm.currentStory.id, t).then(function(success){
                refreshImages(vm.currentStory);
            })
        });
    }
    function refreshImages(storyt){
        story.getImages(storyt.id).then(function(data){
            storyt.images = data.images;
        })
    }
    vm.removeImage = removeImage;
    function removeImage(image){
        adminStory.removeImage(vm.currentStory.id,image.upload_file_name).then(function(success){
           vm.currentStory.images = _.filter(vm.currentStory.images,function(imaget){
               return image != imaget;
           });
        });
    }
    vm.addProduct = addProduct;
    function addProduct(product){
        adminStory.addProduct(vm.currentStory.id,product.id).then(function(success){
            vm.currentStory.products = vm.currentStory.products||[];
            vm.currentStory.products.push(product);
        })
    }
    product.all_noImage().then(function(data){
        vm.products = data.products;
    });
//    vm.refreshProducts = refreshProducts;
    function refreshProducts(storyt){
        story.getProducts(storyt.id).then(function(data){
            storyt.products = data.products;
        })
    }
    vm.removeProduct = removeProduct;
    function removeProduct(product){
        adminStory.removeProduct(vm.currentStory.id,product.id).then(function(success){
            refreshProducts(vm.currentStory);
        });
    }
    vm.getValidProducts = getValidProducts;
    function getValidProducts(){

        return _.filter(vm.products, function(product){
            return _.filter(vm.currentStory.products,function(prot){
                    return prot.id==product.id
            }).length == 0
        })
    }
    vm.remove = remove;
    function remove(){
        adminStory.remove(vm.currentStory.id).then(function(success){
            refresh();
        })
    }
}]);
