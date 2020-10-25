/**
 * Created by hexpang on 16/8/18.
 */


app.controller('ChannelListController',function($scope,CommonService,FileUploader){
    $scope.programs = [];
    $scope.channels = [];
    $scope.nChannel = null;
    $scope.index = -1;
    CommonService.Http.Get('programs',function(response){
        console.log(response)
        $scope.programs = response;
    });
    $scope.cancelProgram = function(){
        if($scope.index > - 1 && $scope.programs[$scope.index].id == 0){
            $scope.programs = $scope.programs.slice(0,$scope.programs.length-1);
        }else{
            $scope.programs[$scope.index].edit = false;
        }
        $scope.index = -1;
        $scope.channels = [];
    }
    $scope.editChannel = function(index){
        var channel = $scope.channels[index];
        $scope.channels[index].edit = true;
        $scope.nChannel = channel;
    }
    $scope.loadChannel = function(channels){
        $scope.channels = channels;
        //CommonService.Http.Get('channels',{pid:pid},function(response){
        //    $scope.channels = response;
        //})
    }
    $scope.keyPress = function(){
        var item = $scope.programs[$scope.programs.length - 1];
        console.log(item);
        if(item.name.length > 1 && item.name.length < 32){
            CommonService.Http.Post('programs',{name:item.name,id:item.id},function(response){
                if(response.id != undefined){
                    $scope.programs[$scope.index].id = response.id;
                    $scope.programs[$scope.index].edit = null;
                    $scope.index = -1;
                    $scope.channels = [];
                }
            })
        }
    }
    $scope.addChannel = function(){
        $scope.nChannel = {};
    }
    $scope.removeChannel = function(){
        CommonService.Http.Post('remove',{id:$scope.nChannel.id},function(response){
            if(response == 1)
            {
                for(var i=0;i<$scope.channels.length;i++){
                    var item = $scope.channels[i];
                    if(item.id == $scope.nChannel.id){
                        $scope.channels.splice(i,1);
                    }
                }
                $scope.nChannel = null;
            }
        })
    }
    $scope.saveChannel = function(){
        if(!$scope.nChannel.name || !$scope.nChannel.url){
            $scope.nChannel = null;
            return;
        }
        CommonService.Http.Post('channels',{name:$scope.nChannel.name,url:$scope.nChannel.url,pid:$scope.programs[$scope.index].id,id:$scope.nChannel.id},function(response){
            if(response.id != undefined){
                if($scope.nChannel.id != undefined){

                }else{
                    $scope.channels.push(response);
                }
                $scope.nChannel = null;

            }
        })
    }
    $scope.channelEnterPress = function(index,offset){
        console.log([index,offset]);

    }
    $scope.doQueue = function (){
        if($scope.queue.length > 0){
            var item = $scope.queue[$scope.queue.length-1];
            var target = $scope.channels[$scope.channels.length - ($scope.channels.length - $scope.queue.length) - 1];
            target.verify = "Validating...";
            console.log(item);
            CommonService.Http.Get('verify',{id:item.id},function(response){
                console.log(response);
                target.vcode = response;
                if(response == 200) {
                    target.verify = "Passed";
                }else if(response == 0){
                    target.verify = "Invalid";
                }else{
                    target.verify = "[" + response + "]";
                }
                $scope.doQueue();
            })
            $scope.queue.pop();
        }
    }
    $scope.checkChannel = function(){
        $scope.queue = $scope.channels.slice();
        $scope.doQueue();
    }
    $scope.copyUrl = function(index){
        var item = $scope.programs[index];
        item.shareUrl = "https://iptv.platform.jabstruse.com/s/" + item.id;
    }
    $scope.editName = function(index){
        var item = $scope.programs[index];
        $scope.index = index;
        item.edit = true;
        $scope.loadChannel(item.channels);
    }
    $scope.newProgram = function(){
        var program = {
            name:'',
            id:0
        };
        $scope.currentItem = program;
        $scope.programs.push($scope.currentItem);
        $scope.index = $scope.programs.length-1;
    }
    $scope.share = function(index,enable){
        var item = $scope.programs[index];
        console.log(item);
        CommonService.Http.Post('share',{id:item.id,share:enable},function(response){
            console.log(response);
            if(response != 0){
                $scope.programs[index] = response;
            }
        })
    }
})