/**
 * Created by spatra on 14-12-12.
 */

define(['personalNotificationJS/module'], function(notificationModule){

  /**
   * 用于`通知`相关的服务
  */
  notificationModule.factory('NotificationService', ['ResourceService',
    function(ResourceService){

      return {
        accessor: ResourceService.getResourceAccessor({
          resourceName: 'notify'
        }),
        setRead: function(notificationId, readStatus){
          if( readStatus === undefined ){
            readStatus = true;
          }

          var self = this;

          return self.accessor.update(notificationId, {
            "read": readStatus
          });
        },
        getStateObj: function(notification, typeLabelSet){
          var projectTypeId = 2;

          var stateObj = {
            stateName: '',
            stateParams: {}
          };

          if(notification.type_id === projectTypeId){
            stateObj.stateName = 'project.show.desktop';
            stateObj.stateParams['projectId'] = notification.project_id;

          }else{
          //由type_id得到stateName
            var curTypeLabel = typeLabelSet[notification.type_id];
            stateObj.stateName = 'project.show.' + curTypeLabel + '.info';
            stateObj.stateParams['projectId'] = notification.project_id;
            var sourceIdName = curTypeLabel + 'Id';
            stateObj.stateParams[sourceIdName] = notification.source_id;

          }


          return stateObj;
        }
      };
  }]);// End of --> NotificationService

  notificationModule.factory('NotificationTypeService', ['ResourceService', function(ResourceService){
    return {
      accessor: ResourceService.getResourceAccessor({
        'resourceName': 'notify-type'
      }),
      getTypeLabelSet: function(){
        return this.accessor.get()
          .then(function(resp){
            var typeList = resp.data;

            var typeLabelSet = {};

            for(var i=0; i<typeList.length; i++){
              var type = typeList[i];

              var splitName = type.name.split('_');
              var typeName = splitName[splitName.length-1];


              typeLabelSet[type.id] = typeName;
            }

            return typeLabelSet;

          },function(resp) { console.log(resp); });
      }
    };
  }]);//End of --> ng-factory: NotificationTypeService


});