/**
 * Created by spatra on 15-3-7.
 */

/**
 * 此模块用于提供一些，当前项目与ng-bootstrap-ui结合使用时，所复用的一些辅助服务或指令.
 *
 */
define(['angular'], function(angular){

  var module = angular.module('TeamMindmap.library.ngBootstrapUIHelper', []);

  /**
   * 此服务用于配合angular-ui-bootstrap-datePicker使用，提供一些默认的设置
   */
  module.factory('NgUIDatePickerService', function(){

    /**
     * 构造函数，生成用于设定的对象
     *
     * @param settingObj
     * @constructor
     */
    function DatePickerSetting(settingObj){
      settingObj = settingObj || {};

      var currentDate = new Date;

      this.date = currentDate.toISOString().split("T")[0];
      this.minDate =currentDate;

      this.dateFormat = settingObj.dateFormat || 'dd-MMMM-yyyy';
      this.dateOptions = settingObj.dateOptions || {formatYear: 'yy', startingDay: 6};
    }

    DatePickerSetting.prototype = {
      open: function($event){
        $event.preventDefault();
        $event.stopPropagation();

        this.opened = true;
      },
      disabled: function(date, mode){
        return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
      },
      getDate: function(){
        var rtn = this.date.toISOString ? this.date.toISOString().split("T")[0] : this.date;
        return rtn;
      }
    };

    return {
      getDefault: function(){
        return new DatePickerSetting();
      },
      get: function(settingObj){
        return new DatePickerSetting(settingObj);
      }
    };
  });

  return module.name;
});