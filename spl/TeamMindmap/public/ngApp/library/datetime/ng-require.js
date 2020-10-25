/**
 * Created by spatra on 15-3-7.
 */

/**
 * 此模块提供一些如日期和时间处理相关的服务或指令.
 *
 */
define(['angular'], function(angular){
  var currentModule = angular.module('TeamMindmap.library.datetime', []);

  /**
   * 用于实现判断某个时间或日期是否处于规定范围内的辅助服务
   */
  currentModule.factory('DatetimeCheckRangeService', function(){

    //Unix纪元开始的1970-01-01是周四
    var now = null, oneDayMSeconds = 1000 * 60 * 60 *24, unixStartWeekDay = 4;

    /**
     * 返回当前时间，如果没有设定过，则新建，否则返回已经设定的值
     * @returns {*}
     */
    function getNow(){
      if( ! now ){
        return new Date;
      }
      else{
        return now;
      }
    }

    /**
     * 返回JS的Date对象
     *
     * @param source 这个参数用于构造Date对象，如果本身已经是则直接返回
     * @returns {*}
     */
    function getDatetimeObj(source){
      if( source === null || source === undefined ){
        return null;
      }
      else if( ! (source instanceof Date) ){
        return new Date(source);
      }
      else{
        return source;
      }
    }

    /**
     * 检查两个时间是否处于同一个年月.
     *
     * @param lhs
     * @param rhs
     * @returns {boolean}
     */
    function checkMonthAndYear(lhs, rhs){
      return lhs.getMonth() === rhs.getMonth() && lhs.getYear() === rhs.getYear();
    }

    /**
     * 检查两个时间是否处于同一个星期
     *
     * @param lhs
     * @param rhs
     * @returns {boolean}
     */
    function checkSameWeek(lhs, rhs){
      var lhsCounter = parseInt( lhs.getTime() / oneDayMSeconds),
        rhsCounter = parseInt( rhs.getTime() / oneDayMSeconds );

      return parseInt( (lhsCounter + unixStartWeekDay) / 7 ) === parseInt( (rhsCounter + unixStartWeekDay ) / 7 );
    }

    /**
     * 检查是否处于两天之内.
     *
     * @param checkTime 待检查的时间
     * @param timePoint 开始计算的时间点，一般为当前时间点
     * @returns {boolean}
     */
    function checkTowDays(checkTime, timePoint){
      timePoint = timePoint || getNow();

      var timePointDayMSeconds = timePoint.getTime() -
        ( timePoint.getHours() * 60 * 60 + timePoint.getMinutes() * 60 + timePoint.getSeconds() ) * 1000;

      return checkTime.getTime() >= timePointDayMSeconds &&
        checkTime.getTime() <= timePointDayMSeconds + 2 * oneDayMSeconds;
    }

    return {
      //设置当前的时间点，以此作为比较的基准，如果不存入参数或存入null则以运行时时间为当前时间点
      setNow: function(datetime){
        now = getDatetimeObj(datetime);
      },
      //获得当前时间点
      getNow: getNow,
      /*
       判断当期时间点与指定时间是否处于同一个范围:
       'today': 今天之内,
       'twodays': 两天之内,
       'week': 属于同一个星期,
       'month': 属于同一个年月
       */
      checkRange: function(datetime, range){
        datetime = getDatetimeObj(datetime);

        var result = false, now = getNow();

        switch (range){
          case 'today':
            result = datetime.getDate() === now.getDate() && checkMonthAndYear(datetime, now);
            break;
          case 'twodays':
            result =  checkTowDays(datetime, now);
            break;
          case 'week':
            result = checkSameWeek(datetime, now);
            break;
          case 'month':
            result = checkMonthAndYear(datetime, now);
            break;
          default :
            console.error('invalid time change');
            break;
        }

        return result;
      }
    };
  });

  return currentModule.name;
});