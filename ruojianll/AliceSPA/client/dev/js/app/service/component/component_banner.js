/**
 * Created by kunono on 2015/3/12.
 */
app.service('component_banner',['$rootScope','$state','marketing','anchorSmoothScroll',function($rootScope,$state,marketing,anchorSmoothScroll){
    if($rootScope.component  == undefined){
        $rootScope.component = {};
    }

    $rootScope.component.banner = {init:{},banners:[]};
    var banner =$rootScope.component.banner;
    marketing.allBanners().then(function(success){
        banner.banners = success.banners

    });

    banner.load = function(){
        $('.flicker-example').flicker();
    };

    banner.click = function(type,value){
        var k;
        if(type == 1){
            k='product';
        }
        else if(type==2){
            k='category';
        }
        if(k!= undefined){
            $state.go(k,{id:value});
        }

    }


}]);
