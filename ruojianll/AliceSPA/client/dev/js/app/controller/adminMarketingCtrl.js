/**
 * Created by kunono on 2015/3/12.
 */
app.controller('adminMarketingCtrl',['marketing','adminMarketing','component_uploadFile','config',function(marketing,adminMarketing,component_uploadFile,config){
    var vm = this;
    vm.hasPermissionOrder = false;
    component_uploadFile.init();
    component_uploadFile.multiple = false;
    vm.bannerTypes = config.bannerTypes;
    vm.saveBanner = saveBanner;
    vm.removeBanner = removeBanner;
    vm.setBannerImage = setBannerImage;
    vm.removeBannerImage = removeBannerImage;
    refresh();
    adminMarketing.hasPermission().then(function(res){
        vm.hasPermissionMarketing = res.hasPermission;
    });

    function refresh(){
        marketing.allBanners().then(function(data){
            vm.banners = data.banners;
            vm.currentBanner = {isNew:true,typeModel:vm.bannerTypes[0]};
            _.each(vm.banners,function(ban){
                ban.isNew = false;
                ban.typeModel = _.find(vm.bannerTypes,function(t){
                    return t.type == ban.type;
                })
            })
        });
    }
    function saveBanner(){
        var ban = vm.currentBanner;
        if(ban.isNew){
            adminMarketing.addBanner(ban.title,ban.subtitle,ban.typeModel.type,ban.value).then(function(success){
                refresh();
            });
        }
        else{
            adminMarketing.editBanner(ban.id,ban.title,ban.subtitle,ban.typeModel.type,ban.value).then(function(success){
                refresh();
            });
        }
    }
    function removeBanner(){
        adminMarketing.removeBanner(vm.currentBanner.id).then(function(success){
            refresh();
        })
    }
    function setBannerImage(){
        component_uploadFile.upload().then(function(success){
            if(success.files.length == 1){
                adminMarketing.setBannerImage(vm.currentBanner.id,success.files[0].upload_file_name).then(function(success){
                    refresh();
                })
            }

        });
    }
    function removeBannerImage(){
        adminMarketing.removeBannerImage(vm.currentBanner.id).then(function(success){
            refresh();
        })
    }
}]);
