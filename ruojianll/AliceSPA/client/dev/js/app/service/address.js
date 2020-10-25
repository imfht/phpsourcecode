/**
 *
 * Created by kunono on 2015/2/10.
 */

app.service('address',['base','log','data','$q',function(base,log,data,$q){
    if(_.isEmpty(data.address.region)){
        base.getData('/json/region.json','json region').then(function(region){
            data.address.region = region;
            refresh();
            log.log('get address region success');
            log.log(data.address.region);
        });
    }
    else{
        log.log('address region is exist');
    }
    return{
        refresh:refresh,
        add:add,
        getProvinces:getProvinces,
        getCities:getCities,
        getCounties:getCounties,
        getProvince:getProvince,
        getCity:getCity,
        getCounty:getCounty,
        remove:remove,
        modify:modify,
        getAddress:getAddress,
        fix:fix
        //copy:copy
    };
    function refresh(){
        if(!data.user.data.isLoggedIn){
            log.log('address user is not loggedin');
            return;
        }
        var defer = $q.defer();
        base.get('/address/all','address all').then(function(adds){
            data.address.data = adds.addresses;
            if(!_.isEmpty(data.address.region)){
                _.each(data.address.data,function(add){
                    add.province = getProvince(add.province_id);
                    add.city = getCity(add.city_id);
                    add.county = getCounty(add.county_id);
                });
            }
            defer.resolve(data.address.data);
            log.log('address refresh success');
        },function(err){
            defer.reject(null);
        });
        return defer.promise;
    }
    function getProvinces(){
        if(!_.isEmpty(data.address.region)){
            return data.address.region.province;
        }
    }
    function getAddress(addid){
        if(addid == undefined){
            return null;
        }
        return _.find(data.address.data,function(addr){
           return addr.id == addid;
        });
    }
    function getCities(proid){
        if(proid == undefined){
            return [];
        }
        if(!_.isEmpty(data.address.region)){
            return _.filter(data.address.region.city,function(city){
                return city.ProID == proid;
            })
        }
    }
    function getCounties(cityid){
        if(cityid == undefined){
            return [];
        }
        if(!_.isEmpty(data.address.region)){
            return _.filter(data.address.region.county,function(county){
                return county.CityID == cityid;
            })
        }
    }
    function add(proid,ciid,coid,detail,name,phone,postcode){
        var json = {};
        json.province_id = proid;
        json.city_id = ciid;
        json.county_id = coid;
        json.detail = detail;
        json.name = name;
        json.phone = phone;
        json.postcode = postcode;
        var defer = $q.defer();
        base.post('/address/add',json,'address add').then(function(data){
            refresh();
            defer.resolve(data);
            log.log('address add success');
        },function(err){
            defer.reject(err);
        });
        return defer.promise;
    }
    function getProvince(proid){
        if(proid == undefined){
            return {};
        }
        if(!_.isEmpty(data.address.region)) {
            return _.find(data.address.region.province, function (pro) {
                return pro.ProID == proid;
            })
        }
    }
    function getCity(cityid){
        if(cityid == undefined){
            return {};
        }
        if(!_.isEmpty(data.address.region)){
            return _.find(data.address.region.city,function(city){
                return city.CityID == cityid;
            })
        }
    }
    function getCounty(countyid){
        if(countyid == undefined){
            return {};
        }
        if(!_.isEmpty(data.address.region)){
            return _.find(data.address.region.county,function(county){
                return county.Id == countyid;
            })
        }
    }
    function remove(addid){
        var json = {};
        json.id = addid;
        var defer = $q.defer();
        base.post('/address/delete',json,'address delete').then(function(data){
            refresh();
            defer.resolve(data);
            log.log('address remove success');
        },function(err){
            defer.reject(err);
        });
    }
    function modify(id,proid,ciid,coid,detail,name,phone,postcode){
        var json = {};
        json.id = id;
        json.province_id = proid;
        json.city_id = ciid;
        json.county_id = coid;
        json.detail = detail;
        json.name = name;
        json.phone = phone;
        json.postcode = postcode;
        var defer = $q.defer();
        base.post('/address/modify',json,'address modify').then(function(res){
            refresh();
            defer.resolve(res);
            log.log('address modify success');
        },function(err){
            defer.reject(err);
        });
        return defer.promise;
    }
    function fix(address){
        address.province = getProvince(address.province_id);
        address.city = getCity(address.city_id);
        address.county = getCounty(address.county_id);
    }
    //function copy(addr){
    //    var t = {
    //        province:addr.province,
    //        city:addr.city,
    //        county:addr.county,
    //        name:addr.name,
    //        phone:addr.phone,
    //        detail:addr.detail
    //    }
    //    if(!_.isEmpty(addr.postcode)){
    //        t.postcode = addr.postcode;
    //    }
    //    return t;
    //}
}]);