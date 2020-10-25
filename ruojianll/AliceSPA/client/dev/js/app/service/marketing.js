/**
 * Created by kunono on 2015/3/12.
 */
app.service('marketing',['base',function(base){
    return {
        allBanners:allBanners,
        home:home
    };
    function allBanners(){
        return base.get('/marketing/banner/all','admin marketing banner all');
    }
    function home(){
        return base.get('/marketing/home','marketing home');
    }
}]);
