(function(){
  /**
   * Main Controller for the Angular Material Starter App
   */
  angular.module('object',['ng.ueditor']).controller('ObjectController',
  function( $scope, $rootScope, $state, $stateParams, API, LxNotificationService, LxDialogService,LxProgressService,$upload, $location,$http) {

    //获取参数
    var stateName      = $state.current.name;
    var objectName     = $stateParams.objectName;
    var objectId       = $stateParams.objectId;
    var objectRelate   = $stateParams.objectRelate;

    //过滤
    $scope.searchStr = "";
    $scope.filterArr = angular.copy($location.search());
    
    console.log("过滤项：",$scope.filterArr); 
    console.log("本体：",objectName,objectId);


    //初始化加载
    $scope.init = function(){
      API.get({"object":objectName,"id":"config"},function(returnData){
        if(!returnData.error){
          $scope.objectConfig = returnData.result;
          console.log("配置项",$scope.objectConfig);
          
          //加载
          if(stateName == "objectDetail" || stateName == "objectRelateDetail"){
            if(objectId == 0){
              $scope.create();
            }else{
              $scope.show();
            }
            
          }else{
            $scope.index();
          }
          loaded = true;
        }
      });
    }

    //修改实体
    if(stateName == "objectRelate" || stateName == "objectRelateDetail"){
      var relate = relateData[objectName+"_"+objectRelate];
      //确定objectName
      objectName = relate.r_table;
      //确定objectId或过滤项
      API.get({"object":relate.table,"id":objectId},function(returnData){
        console.log("本体数据-控制器：",returnData);

        if(!returnData.error){
          angular.forEach(relate.r_filter,function(val,key){
            $scope.filterArr[key] = val;
          });
          $scope.filterArr[relate.r_field] = returnData.result[relate.field];
          console.log("过滤项：",$scope.filterArr); 

          if(stateName == "objectRelateDetail"){
            API.get({"object":relate.r_table,"filter_str":relate.r_field+"="+returnData.result[relate.field]},function(returnData){
              console.log("关联实体数据：",returnData);
              if(!returnData.error){
                if(returnData.result.data.length>0){
                  objectId   = returnData.result.data[0].id;
                }else{
                  objectId   = 0;
                }
                $scope.init();
              }
            });
          }

          if(stateName == "objectRelate"){
            objectId = 0;
            $scope.init();
          }
          console.log("实体：",objectName,objectId);
        }
      });
       
    }else{
      $scope.init();
    }
    
     

    //分页
    $scope.countPerPages = [{num:'5'},{num:'10'},{num:'20'},{num:'30'},{num:'50'}]; 
    $scope.countPerPage  = $scope.countPerPages[2].num;    
    $scope.pageNum       = 1;

    //排序
    $scope.sortField = null;
    $scope.sortValue = null;


    //搜索
    $scope.search = function(searchStr){
      $scope.searchStr = searchStr;
      $scope.index();
    };

    //高级搜索
    $scope.filter = function(){
      //LxDialogService.open('filterDialog');
    };

    //排序
    $scope.sortChange = function(field){
      if(!field.in_sort) return;

      $scope.sortField = field.name;
      $scope.sortValue = field.sort_value?false:true;
      field.sort_value = $scope.sortValue;
      console.log(field.sort_value);
      //加载
      $scope.index();
    };

    //换页
    $scope.pageNumChange = function(pageNum){
      $scope.pageNum = pageNum;    
      //加载
      $scope.index();
    };

    //更换每页条数
    $scope.countPerPageChange = function(count){
      $scope.countPerPage = count;    
      //加载
      $scope.index();
    };

    //索引
    $scope.index = function(){

      var params = {};

      params["object"]       = objectName;
      //分页
      params["currentPage"]  = $scope.pageNum;
      params["countPerPage"] = $scope.countPerPage;
      //排序
      if($scope.sortField){
        params["sortField"] = $scope.sortField
        params["sortValue"] = $scope.sortValue;
      }
      //搜索
      if($scope.searchStr != ""){
        var filter_str = "";
        angular.forEach($scope.objectConfig.fields,function(field){
          if(field.in_search){
            tableName = objectName;
            fieldName = field.name;
            filter_str += tableName+"."+fieldName+" LIKE '%"+$scope.searchStr+"%' OR ";
          }
        });
        if(filter_str != "")
          params["filter_str"] = filter_str.substr(0,filter_str.length-3);
      }
      //过滤
      params["filter"] = [];$i = 0;
      angular.forEach($scope.filterArr,function(value,key){
        params["filter["+$i+"][0]"] = key;
        params["filter["+$i+"][1]"] = "=";
        params["filter["+$i+"][2]"] = value;
        $i++;
      });

      console.log("查找条件：",params);
      $scope.showLinearProgress();

      API.query(params,function(returnData){
        console.log("索引结果：",returnData);
        if(!returnData.error){
          $scope.pager   = getPager(returnData.result);
          $scope.objects = returnData.result.data;
          $scope.hideLinearProgress();
        }
      }); 
    };

    //新建
    $scope.create = function(){
      $scope.object = {};
      angular.forEach($scope.objectConfig.fields,function(field){
        $scope.object[field.name] = field.default;
      });
      angular.forEach($scope.filterArr,function(value,key){
        $scope.object[key] = value;
      });
      console.log("默认记录：",$scope.object);
      if(stateName == "object" || stateName == "objectRelate")
        LxDialogService.open('showDialog');
    };

    //编辑
    $scope.show = function(object){
      objectIdSubmit = (typeof objectId == "undefined" || objectId == 0) ? object.id : objectId;
      API.get({"object":objectName,"id":objectIdSubmit},function(returnData){
        if(!returnData.error){
          $scope.object = returnData.result;
          console.log("单条记录：",$scope.object);
        }
      });
      if(stateName == "object" || stateName == "objectRelate")
        LxDialogService.open('showDialog');
    };

    //新建提交
    $scope.store = function(object){
      console.log("创建内容：",object);
      $scope.showLinearProgress();

      API.save({"object":objectName},object,function(returnData){
        console.log("创建结果",returnData);
        if(!returnData.error){
          LxNotificationService.success('创建成功！！');
          if((stateName == "object" || stateName == "objectRelate"))
            LxDialogService.close('showDialog');
        }else{
          LxNotificationService.error('创建失败！');
        }
        $scope.hideLinearProgress();
        //加载
        if((stateName == "object" || stateName == "objectRelate")){
          $scope.index();
        }else{
          objectId = returnData.result;
          $scope.show();
        }
      });
    };

    //更新提交
    $scope.update = function(object,fieldName){
      var updateData = {};

      if(typeof fieldName == "undefined"){
        updateData = object;
      }else if(typeof fieldName == "string"){
        updateData[fieldName] = object[fieldName];
      }else if(typeof fieldName == "object"){
        for(var i=0;i<fieldName.length;i++){
          updateData[fieldName[i]] = object[fieldName[i]];
        }
      }
      console.log("更新内容：",updateData);
      $scope.showLinearProgress();

      API.update({"object":objectName,"id":object.id},updateData,function(returnData){
        console.log("更新结果：",returnData);
        if(!returnData.error){
          LxNotificationService.success('更新成功！');
          if((stateName == "object" || stateName == "objectRelate") && typeof fieldName == "undefined")
            LxDialogService.close('showDialog');
        }else{
          LxNotificationService.error('更新失败！');
        }
        $scope.hideLinearProgress();
        //加载
        if((stateName == "object" || stateName == "objectRelate"))
          $scope.index();
      }); 
    };

    //删除
    $scope.destroy = function(object){
      LxNotificationService.confirm('确定删除此记录？', '删除后不可恢复！', { cancel:'删除', ok:'取消' }, function(answer)
      {
        if(answer) return false;
        $scope.showLinearProgress();

        API.delete({"object":objectName,"id":object.id},function(returnData){
          console.log("删除结果",returnData);
          if(!returnData.error){
            $scope.objects.splice($scope.objects.indexOf(object), 1);
            LxNotificationService.success('删除成功！');
          }else{
            LxNotificationService.error('删除失败！');
          }
          $scope.hideLinearProgress();
        });
      });
    };

    //上传文件
    $scope.$watch('files', function () {
        $scope.upload($scope.files);
    });
    $scope.upload = function (files,object,field) {
        if (files && files.length) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                $upload.upload({
                    url: '/api/file',
                    file: file,
                    data: file,
                    method: 'post',
                    fileFormDataName:"file",
                    headers: {'Content-Type': file.type}
                }).progress(function (evt) {
                    var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                    console.log('文件上传进度: ' + progressPercentage + '% ' + evt.config.file.name);
                }).success(function (data, status, headers, config) {
                    console.log("文件返回结果：",data);
                    if(!data.error){
                      object[field] = data.result;
                    }
                    
                });
            }
        }
    };

    //enum关联项
    $scope.enumSelect = {
        list: {},
        toModel: function(data, callback)
        {
            if (data)
            {
                callback(data.key);
            }
            else
            {
                callback();
            }
        },
        toSelection: function(enumData, data, callback)
        {
            if (data)
            {
                callback(enumData[data]);
            }
            else
            {
                callback();
            }
        },
        init:function(name,enumData)
        {
          $scope.enumSelect.list[name] = [];
          angular.forEach(enumData,function(val){
            $scope.enumSelect.list[name].push(val);
          });
        }
    };

    //ajax关联项
    $scope.ajax = {
        selected: '',
        list: {},
        listOrigin:{},
        update: function(relate, newFilter, oldFilter)
        {
            if (newFilter)
            {
                $scope.ajax.loading = true;
                var relateTable  = relate.table;
                var relateSelect = relate.select[0];
                var filterStr = (relate.filter?relate.filter + " AND ":"") + relateTable + "." + relateSelect + " LIKE '%" + newFilter + "%'";

                API.query({"object":relateTable,"filter_str":filterStr,"no_relate":1},function(returnData){
                  if(!returnData.error){
                    console.log("关联查找结果：","update",relateTable,returnData.result.data);
                    
                    if(returnData.result.data.length){
                      $scope.ajax.list[relateTable] = returnData.result.data;
                    }else{
                      $scope.ajax.list[relateTable] = $scope.ajax.listOrigin[relateTable];
                    }
                    $scope.ajax.loading = false;

                  }else{
                    $scope.ajax.loading = false;
                  }
                },function(){
                    $scope.ajax.loading = false;
                });
            }
            else
            {
                $scope.ajax.list = false;
            }
        },
        toModel: function(relate, data, callback)
        {
            if (data)
            {
                callback(data[relate.field]);
            }
            else
            {
                callback();
            }
        },
        toSelection: function(relate, data, callback)
        {
            if (typeof data != "undefined")
            {
                var relateTable  = relate.table;
                var relateFiled = relate.field;
                var filterStr = (relate.filter?relate.filter + " AND ":"") + relateTable + "." + relateFiled + " = '" + data+"'";
                console.log("关联查找条件：",filterStr);
                API.query({"object":relateTable,"filter_str":filterStr,"no_relate":1},function(returnData){
                  console.log("关联查找结果：","toSelection",relateTable,returnData);
                  if(!returnData.error && returnData.result.data.length>0){     
                    callback(returnData.result.data[0]);
                  }else{
                    callback();
                  }
                },function(){
                   callback();
                });
            }
            else
            {
                callback();
            }
        },
        init:function(relate)
        {
            $scope.ajax.loading = true;
            var relateTable  = relate.table;
            var relateSelect = relate.select;
            var filterStr = relate.filter?relate.filter:" 1 ";

            API.query({"object":relateTable,"filter_str":filterStr,"no_relate":1},function(returnData){
              if(!returnData.error){
                console.log("关联查找结果：","list",relateTable,returnData.result.data);
                $scope.ajax.list[relateTable]       = returnData.result.data;
                $scope.ajax.listOrigin[relateTable] = returnData.result.data;
                $scope.ajax.loading = false;

              }else{
                $scope.ajax.loading = false;
              }
            },function(){
                $scope.ajax.loading = false;
            });
        },
        loading: false
    };

    $scope.ueditorConfig = {
      "initialFrameHeight":520,
      "autoHeightEnabled":false,
    }

    //菜单列表
    $scope.menuList = (typeof objectMenuData[objectName] != "undefined")?objectMenuData[objectName]:{};
    
    //操作列表
    if(typeof operaData[objectName] != "undefined")
      $scope.operaList = operaData[objectName]; 

    if(typeof bgColorData[objectName] != "undefined")
      $scope.bgColorList = bgColorData[objectName]; 

    //页面跳转
    $scope.stateGo = function(object,opera){
      if(typeof opera != "undefined"){
        opera.state[1]["objectId"] = object.id;
        $state.go(opera.state[0],opera.state[1]);
      }else{
        for (var i = $scope.menuList.length - 1; i >= 0; i--) {
          if($scope.menuList[i].state[0] == "objectDetail"){
            $state.go("objectDetail",{"objectName":objectName,"objectId":object.id});
            break;
          }
        }
      }
    };

    //函数执行
    $scope.operaGo = function(object,opera){
      eval("$scope."+opera.function+"(object,opera.params)") ;
    };

    //加载条
    $scope.showLinearProgress = function()
    {
        LxProgressService.linear.show('#5fa2db', '#progress');
    };

    $scope.hideLinearProgress = function()
    {
        LxProgressService.linear.hide();
    }


    //自定义函数

    //改变订单状态
    $scope.changeTradeState = function(object,params){
      console.log(params);
      object.state = params.state;
      fieldsNames  = ["state"];
      switch(params.state){
        case 1:
          object.confirm_time = getTime();
          fieldsNames.push("confirm_time");
          break;
        case 2:
          object.product_time = getTime();
          object.producter_id = 1;
          fieldsNames.push("product_time","producter_id");
          break;
        case 3:
          object.sned_time = getTime();
          object.sender_id = 1;
          fieldsNames.push("sned_time","sender_id");
          break;
        case 4:
          object.recieve_time = getTime();
          fieldsNames.push("recieve_time");
          break;
      }
      console.log("changeTradeState:",object,fieldsNames);
      $scope.update(object,fieldsNames);
    }
 
  
    //打印订单
    $scope.printTrade = function(object,params){
      window.open("http://baidu.com?id="+object.id);
    };

  });
})();
