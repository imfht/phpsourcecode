/**
 *
 * Created by kunono on 2015/3/5.
 */
app.controller('adminProductCtrl',['$rootScope','adminProduct','product','log','component_uploadFile','category',function($rootScope,adminProduct,product,log,component_uploadFile,category){
    var vm = this;
    vm.hasPermissionProduct = false;
    adminProduct.hasPermission().then(function(flag){
       vm.hasPermissionProduct = flag;
    });
    component_uploadFile.init();
    vm.refresh = refresh;
    vm.refresh();
    function refresh(){
        product.all_noImage().then(function(success){
            vm.currentProduct = {};
            vm.mode = 'add';
            vm.products = success.products;
        });
    }
    category.all().then(function(data){
       vm.categories = data.categories;
    });
    vm.mode = 'add';
    vm.currentProduct = {};
    vm.setCurrentProduct = setCurrentProduct;
    vm.saveEdit = saveEdit;
    function setCurrentProduct(product){
        refreshImages(product);
        vm.currentProduct = product;
    }
    function saveEdit(){
        productt = vm.currentProduct;
        if(_.isEmpty(productt)){
            log.log('admin product saveEdit no current product');
            return;
        }
        if(_.isNaN(productt.price)|| _.isNaN(productt.old_price)){
            log.log('admin product saveEdit price or old_price must be a number');
        }
        if(vm.mode == 'add'){
            adminProduct.add(0,productt.name,productt.number,productt.price,productt.old_price,productt.comment,productt.summary).then(function(success){
                vm.refresh();
            })
        }
        else{
            adminProduct.edit(productt.id,productt.category_id,productt.name,productt.number,productt.price,productt.old_price,productt.comment,productt.summary).then(function(success){
                vm.refresh();
            });
        }

    }
    function refreshImages(productt){
        product.getProductImages(productt.id).then(function(success){
            productt.images=success.images;
        })
    }
    vm.addImages = addImages;
    function addImages(){
        component_uploadFile.upload().then(function(success){
            var t= _.map(success.files,function(file){
                return file.upload_file_name;
            });
            adminProduct.addImages(vm.currentProduct.id, t).then(function(success){
                refreshImages(vm.currentProduct);
            })
        });
    }
    vm.removeImage = removeImage;
    function removeImage(image){
        adminProduct.removeImage(vm.currentProduct.id,image.upload_file_name).then(function(success){
            refreshImages(vm.currentProduct);
        })
    }
    vm.remove = remove;
    function remove(){
        adminProduct.remove(vm.currentProduct.id).then(function(success){
            refresh();
        })
    }
    vm.getValidCategories = getValidCategories;
    function getValidCategories(){
        return _.filter(vm.categories,function(category){
            return vm.currentProduct.category_id != category.id;
        })
    }
    function refreshCategory(productt){
        product.get(productt.id).then(function(data){
            productt.category_id = data.product.category_id;
        })
    }
    vm.setCategory = setCategory;
    function setCategory(category){
        adminProduct.setCategory(vm.currentProduct.id,category.id).then(function(success){
            refreshCategory(vm.currentProduct);
        })
    }
}]);