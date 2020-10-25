/**
 * Created by kunono on 2015/2/10.
 */

app.controller('addressCtrl',['base','data','log','address','$state','anchorSmoothScroll','user',function(base,data,log,address,$state,anchorSmoothScroll,user){
    user.makeLogin();
    var vm = this;
    vm.address = data.address;
    vm.getProvinces = address.getProvinces;
    vm.getCities = address.getCities;
    vm.getCounties = address.getCounties;
    vm.goAdd = goAdd;
    vm.goModify = goModify;
    vm.editConfirm = editConfirm;
    vm.remove = remove;
    function goAdd(){
        data.address.edit = {};
        data.address.edit.mode=0;//add
        anchorSmoothScroll.scrollTo('edit');
    }
    function goModify(add){
        vm.address.edit = _.clone(add);
        vm.address.edit.mode = 1;//modify
        anchorSmoothScroll.scrollTo('edit');
    }
    function editConfirm(){
        if(data.address.edit == undefined){
            return;
        }
        var defer = null;
        if(data.address.edit.mode == 0){//add
            var t = vm.address.edit;
            if(t.province == undefined || t.city == undefined || t.county == undefined){
                return;
            }
            defer = address.add(t.province.ProID, t.city.CityID, t.county.Id, t.detail, t.name, t.phone, t.postcode);
        }
        else if(data.address.edit.mode == 1) {//modify
            var t = vm.address.edit;
            defer = address.modify(t.id, t.province.ProID, t.city.CityID, t.county.Id, t.detail, t.name, t.phone, t.postcode);
        }
        if(defer != null){
            defer.then(function(success){
            });
        }

    }
    function remove(add){
        address.remove(add.id);
    }
}]);